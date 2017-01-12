<?php

namespace a15lam\Workspace;

use a15lam\Workspace\Utility\Config;
use a15lam\Workspace\Utility\Logger;
use Dotenv\Dotenv;

class Workspace
{
    /** @var Config|null */
    protected $config = null;

    /** @var Logger|null */
    protected $logger = null;

    protected static $projectRoot = __DIR__ . '/../';

    protected static $configFile = 'config.php';

    protected static $dotenvLoaded = false;

    /**
     * @param $path
     */
    protected static function setDotenv($path)
    {
        if (false === static::$dotenvLoaded && file_exists($path . '.env')) {
            $dotenv = new Dotenv($path);
            $dotenv->load();
            static::$dotenvLoaded = true;
            static::log()->debug('Loading .env file.');
        }
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return array|bool|false|int|null|string
     */
    public static function env($key, $default = null)
    {
        static::setDotenv(static::$projectRoot);
        if (!static::$dotenvLoaded) {
            throw new \RuntimeException('Dotenv configs are not loaded. Failed to get value for key [' . $key . ']');
        }

        $value = getenv($key);
        if (false === $value) {
            return $default;
        } else {
            if ($value === 'true' || $value === 'TRUE') {
                return true;
            } elseif ($value === 'false' || $value === 'FALSE') {
                return false;
            } elseif (is_numeric($value)) {
                return (1 * $value);
            } else {
                return $value;
            }
        }
    }

    /**
     * @return \a15lam\Workspace\Utility\Config
     */
    public static function config()
    {
        $configFile = static::$projectRoot . static::$configFile;

        return new Config($configFile);
    }

    /**
     * @return \a15lam\Workspace\Utility\Logger
     */
    public static function log()
    {
        $config = static::config();

        $path = $config->get('log_path');
        if (empty($path)) {
            throw new \RuntimeException('No log_path is defined in configuration.');
        }
        $level = $config->get('log_level', Logger::WARNING);
        $timezone = $config->get('timezone', 'America/New_York');
        $debug = $config->get('debug', false);

        return new Logger($path, $level, $timezone, $debug);
    }
}