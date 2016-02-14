<?php

namespace Analyzer\Helpers;


use InvalidArgumentException;

class Config
{
    /**
     * @var string
     */
    private $configFileLocation;

    /**
     * @var array
     */
    static $configData;

    public function __construct(string $configFileLocation)
    {
        $this->configFileLocation = $configFileLocation;
    }

    private function getConfigData()
    {
        if (!self::$configData) {
            self::$configData = json_decode(file_get_contents($this->configFileLocation),true);
        }

        return self::$configData;
    }

    public function get(string $name = null)
    {
        $configData = $this->getConfigData();

        if ($name === null) {
            return $configData;
        }

        $current = self::$configData;

        foreach (explode('.',$name) as $key)
        {
            if (!isset($current[$key])) {
                throw new InvalidArgumentException("Could not find key in config, $name $key ");
            }
            $current = $current[$key];
        }

        return $current;

    }
}