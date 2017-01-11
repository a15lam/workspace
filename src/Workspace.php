<?php

namespace a15lam\Workspace;

use a15lam\Workspace\Utility\Config;
use a15lam\Workspace\Utility\Logger;
use Dotenv\Dotenv;

class Workspace
{
    /** @var Config|null  */
    protected $config = null;

    /** @var Logger|null  */
    protected $logger = null;

    protected static $configInfo = __DIR__ . '/../config.php';

    protected static $logPath = __DIR__ . '/../storage/logs/';

    protected static $dotenvPath = __DIR__ . '/../';

    protected static $dotenvLoaded = false;

    public function __construct(array $config = [])
    {
        $configFile = (isset($config['config_file'])) ? $config['config_file'] : static::$configInfo;
        $this->setConfig($configFile);
        $defaultLogPath = $this->config->get('log_path', static::$logPath);
        $logPath = (isset($config['log_path'])) ? $config['log_path'] : $defaultLogPath;
        $this->setLogger($logPath);
    }

    public static function setDotenv($path)
    {
        if(false === static::$dotenvLoaded && file_exists($path . '.env')){
            $dotenv = new Dotenv($path);
            $dotenv->load();
            static::$dotenvLoaded = true;
            static::log()->debug('Loading .env file.');
        }
    }

    public static function env($key, $default = null)
    {
        static::setDotenv(static::$dotenvPath);
        if(!static::$dotenvLoaded){
            throw new \RuntimeException('Dotenv configs are not loaded. Failed to get value for key [' . $key .']');
        }

        $value = getenv($key);
        if(false === $value){
            return $default;
        } else {
            if($value === 'true' || $value === 'TRUE'){
                return true;
            } elseif ($value === 'false' || $value === 'FALSE'){
                return false;
            } elseif (is_numeric($value)){
                return (1 * $value);
            } else {
                return $value;
            }
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
            $this->config->get('timezone'),
            $this->config->get('debug', false)
        );
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param string $file
     * @return \a15lam\Workspace\Utility\Config
     */
    public static function config($file = null)
    {
        $config = [];
        if(!empty($file)){
            $config['config_file'] = $file;
        }
        $ws = new static($config);
        return $ws->getConfig();
    }

    /**
     * @param string $path
     * @param string $configFile
     * @return \a15lam\Workspace\Utility\Logger
     */
    public static function log($path = null, $configFile = null)
    {
        $config = [];
        if(!empty($configFile)){
            $config['config_file'] = $configFile;
        }
        if(!empty($path)){
            $config['log_path'] = $path;
        }

        $ws = new static($config);
        return $ws->getLogger();
    }
}