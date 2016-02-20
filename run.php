<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Analyzer\Commands\CalculateStats;
use Analyzer\Commands\LoadTickers;
use Analyzer\DataSources\YahooFinance;
use Analyzer\Helpers\Math;
use Analyzer\Models\TickerStats;
use Symfony\Component\Console\Application;
use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

$config =new Config(__DIR__ . '/config/config.json');
$guzzleClient = new GuzzleClient(['base_uri' => $config->get('yahooFinanceBaseUrl')]);
$fileSystem = new Filesystem(new LocalAdapter(__DIR__ .'/' .$config->get('dataDir')));
$yahooFinance = new YahooFinance($guzzleClient,$fileSystem);
$tickerStats = new TickerStats(new Math());

$application = new Application();

$application->add(new LoadTickers($yahooFinance));
$application->add(new CalculateStats($yahooFinance,$tickerStats));

$application->run();

/**
 * http://ichart.finance.yahoo.com/table.csv?s=WU&ignore=.csv
 * http://ichart.finance.yahoo.com/table.csv?s=%5EGSPC&ignore=.csv
 */