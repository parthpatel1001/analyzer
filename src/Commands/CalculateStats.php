<?php

namespace Analyzer\Commands;


use Analyzer\DataSources\YahooFinance;
use Analyzer\Models\TickerStats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CalculateStats
 * @package Analyzer\Commands
 */
class CalculateStats extends Command
{
    /**
     * @var YahooFinance
     */
    private $yahooFinance;

    /**
     * @var TickerStats
     */
    private $tickerStats;

    /**
     * CalculateStats constructor.
     * @param YahooFinance $yahooFinance
     * @param TickerStats $tickerStats
     */
    public function __construct(YahooFinance $yahooFinance, TickerStats $tickerStats)
    {
        parent::__construct();
        $this->yahooFinance = $yahooFinance;
        $this->tickerStats = $tickerStats;
    }

    public function configure()
    {
        $this->setName('tickers:calculateStats')
            ->setDescription('Calculates stats on provided tickers')
            ->addArgument('type',InputArgument::REQUIRED,'All|Average')
            ->addArgument('range',InputArgument::REQUIRED,'Range i,m')
            ->addArgument('tickers',InputArgument::IS_ARRAY,'Tickers to load seperated by spaces');
    }

    /**
     * @param array $tickers
     * @param OutputInterface $output
     * @param int $i
     * @param int|null $m
     */
    private function nDayReturns(array $tickers, OutputInterface $output, int $i, int $m = null)
    {
        foreach($tickers as $ticker)
        {
            $this->tickerStats->getNDayReturns(
                $this->yahooFinance->getData($ticker, true, false, true),
                $i,
                $m,
                function ($getNDayReturns) use ($ticker,$output)
                {
                    foreach ($getNDayReturns as $n => $returns)
                    {
                        foreach($returns as $startDate => $return)
                        {
                            $r = $return*100;
                            $output->writeln("$ticker $n day $startDate $r%");
                        }

                    }
                }
            );
        }
    }

    /**
     * @param array $tickers
     * @param OutputInterface $output
     * @param int $i
     * @param int|null $m
     */
    private function nDayAverageReturns(array $tickers, OutputInterface $output, int $i, int $m = null)
    {
        foreach($tickers as $ticker)
        {
            $this->tickerStats->getNDayAverageReturns(
                $this->yahooFinance->getData($ticker, true, true, true),
                $i,
                $m,
                function ($avgReturns) use ($ticker,$output) {
                    foreach ($avgReturns as $nDayAvgReturn => $return) {
                        $r = $return*100;
                        $output->writeln("$ticker avg $nDayAvgReturn day $r%");
                    }
                }
            );
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidArgumentException|\InvalidArgumentException
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $type = strtoupper($input->getArgument('type'));
        $range = explode(',',$input->getArgument('range'));
        $i = $range[0];
        $m = isset($range[1]) ? $range[1] : null;
        $tickers = $input->getArgument('tickers');


        switch($type) {
            case 'ALL':
                $this->nDayReturns($tickers,$output,$i,$m);
                break;
            case 'AVG':
                $this->nDayAverageReturns($tickers,$output,$i,$m);
                break;
            default:
                throw new InvalidArgumentException("Invalid argument type $type");
        }
    }
}