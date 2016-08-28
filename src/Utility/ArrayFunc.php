<?php

namespace a15lam\Workspace\Utility;

class ArrayFunc
{
    /**
     * @param array $array
     * @param       $key
     * @param null  $default
     *
     * @return mixed|null
     */
    public static function get(array $array, $key, $default = null)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $topKey = array_shift($keys);

            return (isset($array[$topKey])) ? static::get($array[$topKey], implode('.', $keys), $default) : $default;
        } else {
            return (isset($array[$key])) ? $array[$key] : $default;
        }
    }

    /**
     * NOTE: This overwrites existing key and sub-key values.
     *
     * @param array $array
     * @param       $key
     * @param       $value
     */
    public static function set(array & $array, $key, $value)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $topKey = array_shift($keys);

            if (!isset($array[$topKey]) || !is_array($array[$topKey])) {
                $array[$topKey] = [];
            }

            static::set($array[$topKey], implode('.', $keys), $value);
        } else {
            $array[$key] = $value;
        }
    }
}