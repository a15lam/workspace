<?php
namespace a15lam\Workspace\Utility;


class Logger
{
    /*********************************
     * Enumerations
     *********************************/

    const ERROR   = 4;
    const WARNING = 3;
    const INFO    = 2;
    const DEBUG   = 1;
    const FILE_NAME    = 'main.log';

    /** @var null|string  */
    protected $logFile = null;
    
    /** @var int  */
    protected $logLevel = 0;

    /**
     * Set this to true to completely silence the logger
     *
     * @type bool
     */
    public static $silent = false;

    /**
     * Logger constructor.
     * @param string $logPath
     * @param integer $defaultLogLevel
     * @param string $timezone
     */
    public function __construct($logPath, $defaultLogLevel = 0, $timezone = null)
    {
        if(is_file($logPath)){
            $this->logFile = $logPath;
        } elseif (is_dir($logPath)){
            $this->logFile = rtrim($logPath, '/') . '/' . static::FILE_NAME;
        } else {
            throw new \InvalidArgumentException('Invalid log path/file provided. [' . $logPath . ']');
        }
        $this->logLevel = $defaultLogLevel;
        if(!empty($timezone)) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Writes to log file.
     *
     * @param int    $level
     * @param string $msg
     *
     * @return bool
     * @throws \Exception
     */
    protected function write($level, $msg)
    {
        if (static::$silent) {
            return false;
        }

        if (!$this->isAllowed($level)) {
            return false;
        }

        $time = date('Y-m-d H:i:s', time());

        $msg = "[" . $time . "][" . static::getLevelName($level) . "] " . $msg . PHP_EOL;
        $fh = fopen($this->logFile, 'a');

        if (!fwrite($fh, $msg)) {
            throw new \Exception('Failed to write to log file at ' . $this->logFile);
        }

        return true;
    }

    /**
     * Checks to see if log level is allowed by config.
     *
     * @param int $level
     *
     * @return bool
     */
    protected function isAllowed($level)
    {
        if ($level >= $this->logLevel) {
            return true;
        }

        return false;
    }

    /**
     * Gets the log level name by value
     *
     * @param int $value
     *
     * @return null|string
     */
    protected static function getLevelName($value)
    {
        $map = array_flip((new \ReflectionClass(self::class))->getConstants());

        return (array_key_exists($value, $map) ? $map[$value] : null);
    }

    /**
     * Logs warning messages.
     *
     * @param string $msg
     *
     * @return bool
     * @throws \Exception
     */
    public function warn($msg)
    {
        return $this->write(static::WARNING, $msg);
    }

    /**
     * Logs error messages.
     *
     * @param string $msg
     *
     * @return bool
     * @throws \Exception
     */
    public function error($msg)
    {
        return $this->write(static::ERROR, $msg);
    }

    /**
     * Logs info messages.
     *
     * @param string $msg
     *
     * @return bool
     * @throws \Exception
     */
    public function info($msg)
    {
        return $this->write(static::INFO, $msg);
    }

    /**
     * Logs debug messages.
     *
     * @param string $msg
     *
     * @return bool
     * @throws \Exception
     */
    public function debug($msg)
    {
        return $this->write(static::DEBUG, $msg);
    }
}