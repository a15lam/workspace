<?php

namespace a15lam\Workspace\Utility;

class Config
{
    /** @var array|mixed  */
    protected $config = [];

    /**
     * Config constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if(is_array($config)){
            $this->config = $config;
        } elseif (file_exists($config)){
            $this->config = include "$config";
        } else {
            throw new \InvalidArgumentException(
                'Invalid config/config path provided. Expects a valid config file path or array.'
            );
        }
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key = null, $default = null)
    {
        if(!empty($key)){
           return ArrayFunc::get($this->config, $key, $default);
        } 
        
        return $this->config;
    }
}