<?php

namespace Analyzer\Models;

use ArrayAccess;

/**
 * Class TickerCollection
 * @package Analyzer\Models
 */
class TickerCollection implements ArrayAccess
{

    /**
     * @var string
     */
    private $ticker;
    /**
     * @var array
     */
    private $data;
    /**
     * @var array
     */
    private $cacheDataWithValueObjects;

    /**
     * @var int
     */
    private $cacheLength;

    /**
     * TickerCollection constructor.
     * @param string $ticker
     * @param array $data
     */
    public function __construct($ticker, array $data)
    {
        $this->ticker = $ticker;
        $this->data = $data;
        $this->cacheDataWithValueObjects = array();
        $this->cacheLength = count($this->data);
    }

    /**
     * @return int
     */
    public function length()
    {
        return $this->cacheLength;
    }

    public function each(callable $do)
    {
        $length = $this->length();
        for ($i = 0; $i < $this->length(); $i++)
        {
            $do($this[$i]);
        }
    }
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return Ticker
     */
    public function offsetGet($offset)
    {
        if (!isset($this->cacheDataWithValueObjects[$offset]))
        {
            if(!isset($this->data[$offset])) {
                throw new \InvalidArgumentException("Offset $offset does not exist");
            }
            $this->cacheDataWithValueObjects[$offset] = Ticker::getFromRowArrayData($this->data[$offset]);
        }

        return $this->cacheDataWithValueObjects[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        // TODO use a better exception here
        throw new \InvalidArgumentException('Can not set values on TickerCollection');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        // TODO use a better exception here
        throw new \InvalidArgumentException('Can not unset values on TickerCollection');
    }


}