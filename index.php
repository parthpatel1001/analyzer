<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

// TODO fix all of this

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Analyzer\DataSources\YahooFinance;
use Analyzer\Helpers\Math;
use Analyzer\Models\TickerStats;
use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;


$request = Request::createFromGlobals();




$config =new Config(__DIR__ . '/config/config.json');
$guzzleClient = new GuzzleClient(['base_uri' => $config->get('yahooFinanceBaseUrl')]);
$fileSystem = new Filesystem(new LocalAdapter(__DIR__ .'/' .$config->get('dataDir')));
$yahooFinance = new YahooFinance($guzzleClient,$fileSystem);
$tickerStats = new TickerStats(new Math());


$tickers = $request->get('tickers',[]);
$i = (int) $request->get('i',0);
$m = $request->get('m',null); $m = $m ? (int) $m : null;
$out = [];

foreach($tickers as $ticker)
{
    $tickerStats->getNDayAverageReturns(
        $yahooFinance->getData($ticker, true, true, true),
        $i,
        $m,
        function ($avgReturns) use ($ticker,&$out) {
            foreach ($avgReturns as $nDayAvgReturn => $return) {
                if (!isset($out[$ticker])) {
                    $out[$ticker] = [];
                }
                $out[$ticker][$nDayAvgReturn] = $return;
            }
        }
    );
}

$response = new Response(json_encode($out));
$response->headers->set('Content-Type', 'application/json');
$response->send();
