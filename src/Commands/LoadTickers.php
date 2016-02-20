<?php

namespace Analyzer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Analyzer\DataSources\YahooFinance;
use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

class LoadTickers extends Command
{
    private $yahooFinance;

    public function __construct(YahooFinance $yahooFinance)
    {
        parent::__construct();
        $this->yahooFinance = $yahooFinance;
    }

    protected function configure()
    {
        $this->setName('tickers:load')
            ->setDescription('Loads ticker file data, ignores cache')
            ->addArgument(
                'tickers',
                InputArgument::IS_ARRAY,
                'Tickers to load seperated by spaces'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tickers = $input->getArgument('tickers');
        foreach($tickers as $ticker)
        {
            $this->yahooFinance->getData($ticker,false,True,False);
            $output->writeln("Saved data for ticker $ticker");
        }
    }

}