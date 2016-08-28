<?php

namespace a15lam\Workspace;

use a15lam\Workspace\Utility\Config;
use a15lam\Workspace\Utility\Logger;

class Workspace
{
    /** @var Config|null  */
    protected static $config = null;

    /** @var Logger|null  */
    protected static $logger = null;

    /** @var string  */
    protected static $configInfo = __DIR__ . '/../config.php';

    /** @var string  */
    protected static $logPath = __DIR__ . '/../storage/logs/';

    /**
     * Sets the config class
     */
    protected static function setConfig()
    {
        static::$config = new Config(static::$configInfo);
    }

    /**
     * Sets the logger class
     */
    protected static function setLogger()
    {
        static::$logger = new Logger(
            static::$logPath,
            static::config()->get('log_level', Logger::WARNING),
            static::config()->get('timezone')
        );
    }

    /**
     * @return \a15lam\Workspace\Utility\Config
     */
    public static function config()
    {
        if (static::$config === null) {
            static::setConfig();
        }

        return static::$config;
    }

    /**
     * @return \a15lam\Workspace\Utility\Logger
     */
    public static function log()
    {
        if (static::$logger === null) {
            static::setLogger();
        }

        return static::$logger;
    }
}