<?php

namespace a15lam\Workspace;

use a15lam\Workspace\Utility\Config;
use a15lam\Workspace\Utility\Logger;

class Workspace
{
    /** @var Config|null  */
    protected $config = null;

    /** @var Logger|null  */
    protected $logger = null;

    public function __construct(array $config)
    {
        if(!isset($config['config_file'])){
            throw new \InvalidArgumentException('No config_file provided in Workspace config.');
        }
        $this->setConfig($config['config_file']);
        if(isset($config['log_path'])){
            $this->setLogger($config['log_path']);
        }
    }

    /**
     * Sets the config class
     *
     * @param $file
     */
    protected function setConfig($file)
    {
        if(!is_file($file)) {
            throw new \InvalidArgumentException('Config file not found in [' . $file . ']');
        }
        $this->config = new Config($file);
    }

    /**
     * Sets the logger class
     */
    protected function setLogger($path)
    {
        if(!is_dir($path)){
            throw new \InvalidArgumentException('Log path not found for [' . $path . ']');
        }
        $this->logger = new Logger(
            $path,
            $this->config->get('log_level', Logger::WARNING),
            $this->config->get('timezone')
        );
    }

    /**
     * @return \a15lam\Workspace\Utility\Config
     */
    public function config()
    {
        return $this->config();
    }

    /**
     * @return \a15lam\Workspace\Utility\Logger
     */
    public function log()
    {
        return $this->logger;
    }
}