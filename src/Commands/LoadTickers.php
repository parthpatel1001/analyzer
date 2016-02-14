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
    /**
     * @var Config
     */
    private $config;
    /**
     * @var GuzzleClient
     */
    private $guzzleClient;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Config $config, GuzzleClient $guzzleClient, Filesystem $filesystem)
    {
        parent::__construct();
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
        $this->filesystem = $filesystem;
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

    private function getYahooFinanceInstance()
    {

        return new YahooFinance(
            $this->guzzleClient,
            $this->filesystem
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tickers = $input->getArgument('tickers');
        $yahooFinance = $this->getYahooFinanceInstance();
        foreach($tickers as $ticker)
        {
            $yahooFinance->getData($ticker,false,True,False);
            $output->writeln("Saved data for ticker $ticker");
        }
    }

}