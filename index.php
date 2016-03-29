<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Analyzer\DataSources\YahooFinance;
use Analyzer\Helpers\Math;
use Analyzer\Models\TickerStats;
use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;


require 'vendor/autoload.php';
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$container = new \Slim\Container($configuration);
$app = new \Slim\App($container);
//$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer("web/");

/**
 * Index Page
 */
$app->get('/', function (Request $request, Response $response) {
    return $this->view->render($response, "index.html");
});

/**
 * GET /n_day_average_returns
 */
$app->get("/api/n_day_average_returns", function (Request $request, Response $response) {
    $config =new Config(__DIR__ . '/config/config.json');
    $guzzleClient = new GuzzleClient(['base_uri' => $config->get('yahooFinanceBaseUrl')]);
    $fileSystem = new Filesystem(new LocalAdapter(__DIR__ .'/' .$config->get('dataDir')));
    $yahooFinance = new YahooFinance($guzzleClient,$fileSystem);
    $tickerStats = new TickerStats(new Math());
    $params = $request->getQueryParams();

    $tickers = array_key_exists('tickers', $params) ? $params['tickers'] : [];
    $i = array_key_exists('i', $params) ? (int) $params['i'] : [];
//$m = (int) $request->get('m',null) ?: null;
    $out = [];

    foreach($tickers as $ticker)
    {
        $tickerStats->getNDayReturns(
            $yahooFinance->getData($ticker, true, true, true),
            $i,
            null,
            function ($avgReturns) use ($ticker,&$out) {
                foreach ($avgReturns as $nDayAvgReturn => $return) {
                    if (!isset($out[$ticker])) {
                        $out[$ticker] = [];
                    }
                    $out[$ticker] = $return;
                }
            }
        );
    }

    return $response->withJson($out);
});

$app->run();