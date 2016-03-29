<?php

namespace Analyzer\Models;

/**
 * Class Ticker
 * @package Analyzer\Models
 */
class Ticker
{
    /**
     * @var string
     */
    private $date;
    /**
     * @var float
     */
    private $open;
    /**
     * @var float
     */
    private $high;
    /**
     * @var float
     */
    private $low;
    /**
     * @var float
     */
    private $close;
    /**
     * @var float
     */
    private $volume;
    /**
     * @var float
     */
    private $adjClose;

    /**
     * Ticker constructor.
     * @param string $date
     * @param float $open
     * @param float $high
     * @param float $low
     * @param float $close
     * @param float $volume
     * @param float $adjClose
     */
    public function __construct(
        $date,
        $open,
        $high,
        $low,
        $close,
        $volume,
        $adjClose)
    {
        $this->date = $date;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
        $this->volume = $volume;
        $this->adjClose = $adjClose;
    }

    public static function getFromRowArrayData(array $data)
    {
        return new self($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6]);
    }
    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getOpen()
    {
        return $this->open;
    }

    /*
     * @return float
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @return float
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @return float
     */
    public function getClose()
    {
        return $this->close;
    }

    /**
     * @return float
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @return float
     */
    public function getAdjClose()
    {
        return $this->adjClose;
    }

    public function debugDump()
    {
        return
            $this->getDate() . " "
            . $this->getOpen() . " "
            . $this->getHigh() . " "
            . $this->getLow() . " "
            . $this->getClose() . " "
            . $this->getVolume() . " "
            . $this->getAdjClose();
    }
}