<?php

namespace Gbucket\Lib;

use Pimple\Container;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;


/**
 * A class to facilitate access to the configuration values
 */
class Config
{

    /**
     * An array of config variables
     *
     * @var array
     */
    protected $parsedConfig;

    /**
     * @param Container $container
     * @param string $fileName
     */
    public function __construct($fileName = 'config.yml')
    {

        $this->parseConfig($fileName);
    }


    /**
     * Convert YAML config file to an array
     *
     * @param string $fileName
     * @return void
     */
    private function parseConfig($file)
    {
        try {
		    $value = Yaml::parseFile($file);

		}catch (ParseException $exception) {

		    printf('Unable to parse the YAML string: %s', $exception->getMessage());
		}
    }


}
