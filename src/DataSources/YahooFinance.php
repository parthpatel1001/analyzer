<?php

namespace Analyzer\DataSources;

use Analyzer\Helpers\Config;
use Analyzer\Models\TickerCollection;
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

    /**
     * @var array-
     */
    private static $tickerHelpers = array(
        'S&P500' => '%5EGSPC' // ^GSPC
    );

    /**
     * YahooFinance constructor.
     * @param GuzzleClient $guzzle
     * @param Filesystem $filesystem
     */
    public function __construct(GuzzleClient $guzzle, Filesystem $filesystem)
    {
        $this->guzzle = $guzzle;
        $this->fileSystem = $filesystem;

    }

    /**
     * @param string $ticker
     * @return string
     */
    static function ticker(string $ticker)
    {
        if (isset(self::$tickerHelpers[$ticker])) {
            return self::$tickerHelpers[$ticker];
        }

        throw new \InvalidArgumentException("No ticker helper found for $ticker");
    }

    /**
     * Returns the historical price data as a csv string for $ticker
     * @param string $ticker
     * @param bool $fromCache
     * @param bool $saveToCache
     * @param bool $return
     * @return TickerCollection
     */
    public function getData($ticker, $fromCache = true, $saveToCache = true, $return = true)
    {
        if ($fromCache && $this->fileSystem->has($ticker))
        {
            return $this->present($ticker,$this->fileSystem->read($ticker));
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
            return $this->present($ticker,$data);
        }

    }

    /**
     * @param string $ticker
     * @param string $data
     * @return TickerCollection
     */
    public function present(string $ticker,string $data, bool $strict = False)
    {
        // get the rows except the first one (first one is column labels)
        $rows = explode("\n",$data);
        array_shift($rows);
        // yahoo finance data is neweest to oldest, reverse this before presenting
        $rows = array_reverse($rows);
        $out = [];

        foreach ($rows as $row)
        {
            $columns = explode(',',$row);
            if (count($columns) == 7)
            {
                array_push($out,$columns);
            } else if ($strict)
            {
                throw new \InvalidArgumentException("Invalid row in $ticker data: $row");
            }
        }

        return new TickerCollection($ticker, $out);
    }
}