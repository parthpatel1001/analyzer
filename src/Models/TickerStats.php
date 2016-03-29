<?php

namespace Analyzer\Models;
use Analyzer\Helpers\Math;


/**
 * Class TickerStats
 * @package Analyzer\Models
 */
class TickerStats
{

    /**
     * @var Math
     */
    private $math;

    const DEFAULT_CHUNK_SIZE = 10;

    /**
     * TickerStats constructor.
     * @param Math $math
     */
    public function __construct(Math $math)
    {
        $this->math = $math;
    }

    /**
     * Returns an array of the $n day returns for $tick
     * if $m is provided, calculates all the returns for [$n,$m)
     *   $m must be > $n if provided
     * Return array looks like (if only m is provided, the same format is used but with only one item in result):
     * [
     *  '3' => ['2010-01-01' => 0.034,'2010-01-02' => 0.045], // 3 day return
     *  '4' => ['2010-01-01' => 0.145,'2010-01-02' => -0.8], // 4 day return
     *  ...
     * ]
     * @param TickerCollection $tickerCollection
     * @param int $n
     * @param int $m
     * @param callable $chunk
     * @return null|array
     */
    public function getNDayReturns(TickerCollection $tickerCollection, $n, $m = null, callable $chunk = null)
    {
        $len = $tickerCollection->length();
        // TODO do this all better
        if ($n >= $len || $n < 1)
        {
            throw new \InvalidArgumentException("Invalid range start $n");
        }
        if ($m !== null ) {
            if ($m < $n) {
                throw new \InvalidArgumentException("Invalid range ($n,$m)");
            }
        } else {
            $m = $n + 1;
        }

        if ($chunk)
        {
            // TODO this is copy pasted in getNDayAverageReturns
            $i = $n;

            for (; $i < $m; $i += self::DEFAULT_CHUNK_SIZE) {
                $end = $i + self::DEFAULT_CHUNK_SIZE;
                $end = $end > $m ? $m : $end;
                $chunk($this->getNDayReturns($tickerCollection,$i,$end));
            }
            return null;
        }

        $out = [];
        // TODO do this better
        // create empty array
        for ($i = $n; $i < $m; $i++)
        {
            $out[$n] = [];
        }

        // TODO cache this so can get multiple stats
        // $j - the $j-th day forward to look to calculate the return
        for ($j = $n; $j < $m; $j++)
        {
            $iterateTo = $len - $j;
            // $i - the $i-th current price data row
            for ($i = 0; $i < $iterateTo; $i++)
            {
                $currentTicker = $tickerCollection[$i];
                $futureTicker = $tickerCollection[$i + $j];
                $out[$j][$currentTicker->getDate()] = $this->math->percentChange($currentTicker->getAdjClose(),$futureTicker->getAdjClose());
            }
        }


        return $out;
    }

    /**
     * Returns [
     *  '3' => 4.5, // 3 day average return
     * '4' => 1.2, ...]
     * @param TickerCollection $tickerCollection
     * @param int $n
     * @param int|null $m
     * @param callable $chunk
     * @return array
     */
    public function getNDayAverageReturns(TickerCollection $tickerCollection, $n, $m = null, callable $chunk = null)
    {
        // TODO this is copypasta do this better
        if ($m !== null ) {
            if ($m < $n) {
                throw new \InvalidArgumentException("Invalid range ($n,$m)");
            }
        } else {
            $m = $n + 1;
        }

        if ($chunk)
        {
            $i = $n;

            for (; $i < $m; $i += self::DEFAULT_CHUNK_SIZE) {
                $end = $i + self::DEFAULT_CHUNK_SIZE;
                $end = $end > $m ? $m : $end;
                $chunk($this->getNDayAverageReturns($tickerCollection,$i,$end));
            }
        } else
        {
            $out = $nDayReturns = $this->getNDayReturns($tickerCollection,$n,$m);

            foreach($nDayReturns as $numDays => $returns)
            {
                $out[$numDays] = $this->math->average($returns);
            }

            return $out;
        }
    }
}