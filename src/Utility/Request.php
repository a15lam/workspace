<?php

namespace a15lam\Workspace\Utility;


class Request
{
    /**
     * @var mixed|null
     */
    protected $response = null;

    /**
     * @var array|mixed
     */
    protected $info = [];

    /**
     * @var array 
     */
    protected $defaultOptions = [
        CURLOPT_HEADER => 1,
        CURLOPT_RETURNTRANSFER => 1
    ];

    /**
     * Request constructor.
     * @param       $url
     * @param array $curlOptions
     */
    public function __construct($url, $curlOptions=[])
    {
        $options = $this->defaultOptions + $curlOptions;
        $ch = curl_init($url);
        foreach ($options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $this->response = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        @curl_close($ch);
    }

    /**
     * @return mixed|null
     */
    public function getStatusCode()
    {
        return ArrayFunc::get($this->info, 'http_code');
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getResponseCookies($key=null, $default=null)
    {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $this->response, $matches);
        $cookies = [];
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        if(!empty($key)){
            return ArrayFunc::get($cookies, $key, $default);
        }

        return $cookies;
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        $headerSize = ArrayFunc::get($this->info, 'header_size');
        $headerStr = substr($this->response, 0, $headerSize);
        $headerArray  =[];

        if ($headerStr) {
            $rawHeaders = explode("\r\n", $headerStr);

            if (!empty($rawHeaders)) {
                $first = true;

                foreach ($rawHeaders as $line) {
                    if (empty($line)) {
                        continue; // fix for curl returning blank lines in header string
                    }
                    //	Skip the first line (HTTP/1.x response)
                    if ($first || preg_match('/^HTTP\/[0-9\.]+ [0-9]+/', $line)) {
                        $first = false;
                        continue;
                    }

                    $parts = explode(':', $line, 2);

                    if (!empty($parts)) {
                        $headerArray[trim($parts[0])] = count($parts) > 1 ? trim($parts[1]) : null;
                    }
                }
            }
        }

        return $headerArray;
    }

    /**
     * @return bool|mixed|string
     */
    public function getResponseBody()
    {
        $headerSize = ArrayFunc::get($this->info, 'header_size');
        $body =  substr($this->response, $headerSize);
        $contentType = ArrayFunc::get($this->info, 'content_type');

        //	Attempt to auto-decode any JSON response
        if (!empty($body) && false !== stripos($contentType,'application/json',0)) {
            try {
                if (false !== ($json = @json_decode($body, true))) {
                    $body = $json;
                }
            } catch (\Exception $ex) {
                //	do nothing
            }
        }

        return $body;
    }
}