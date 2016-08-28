<?php

namespace a15lam\Workspace;

use a15lam\Workspace\Utility\Config;
use a15lam\Workspace\Utility\Logger;

class Workspace
{
    protected static $config = null;

    protected static $logger = null;

    protected static function setConfig()
    {
        static::$config = new Config(__DIR__ . '/../config.php');
    }

    protected static function setLogger()
    {
        static::$logger = new Logger(
            __DIR__ . '/../storage/logs/',
            static::config()->get('log_level', Logger::WARNING),
            static::config()->get('timezone')
        );
    }

    public static function config()
    {
        if (static::$config === null) {
            static::setConfig();
        }

        return static::$config;
    }

    public static function log()
    {
        if (static::$logger === null) {
            static::setLogger();
        }

        return static::$logger;
    }
}