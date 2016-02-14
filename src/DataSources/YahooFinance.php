<?php

namespace Analyzer\DataSources;

use Analyzer\Helpers\Config;
use GuzzleHttp\Client as GuzzleClient;
use League\Flysystem\Filesystem;


class YahooFinance
{
    /**
     * GuzzleClient
     */
    private $guzzle;

    /**
     * @var Filesystem
     */
    private $fileSystem;

//    const BASE_URL = 'http://ichart.finance.yahoo.com/table.csv?';
//    's=_TICKER_&ignore=.csv'

    private static $tickerHelpers = array(
        'S&P500' => '%5EGSPC' // ^GSPC
    );

    public function __construct(GuzzleClient $guzzle, Filesystem $filesystem)
    {
        $this->guzzle = $guzzle;
        $this->fileSystem = $filesystem;

    }

    static function ticker($ticker)
    {
        if (isset(self::$tickerHelpers[$ticker])) {
            return self::$tickerHelpers[$ticker];
        }

        throw new \InvalidArgumentException("No ticker helper found for $ticker");
    }

    public function getData(string $ticker, bool $fromCache = True, bool $saveToCache = True, bool $return = True)
    {
        if ($fromCache && $this->fileSystem->has($ticker)) {
            return $this->present($this->fileSystem->read($ticker));
        }

        $data = (string) $this->guzzle->request('GET', '', [
            'query' => [
                's'      => $ticker,
                'ignore' => '.csv'
            ]
        ])->getBody();

        if ($saveToCache)
        {
            $this->fileSystem->put($ticker,$data);
        }

        if ($return)
        {
            return $this->present($data);
        }

    }

    public function present($data)
    {
        return $data;
    }
}