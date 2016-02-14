<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Analyzer\Commands\LoadTickers;
use Symfony\Component\Console\Application;
use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

$config =new Config(__DIR__ . '/config/config.json');
$application = new Application();

$application->add(
    new LoadTickers(
        $config,
        new GuzzleClient(
            [
                'base_uri' => $config->get('yahooFinanceBaseUrl')
            ]
        ),
        new Filesystem(
            new LocalAdapter(__DIR__ .'/' .$config->get('dataDir'))
        )
    )
);
$application->run();

/**
 * http://ichart.finance.yahoo.com/table.csv?s=WU&ignore=.csv
 * http://ichart.finance.yahoo.com/table.csv?s=%5EGSPC&ignore=.csv
 */