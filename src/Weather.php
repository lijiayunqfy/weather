<?php

namespace Overtrue\Weather;

use GuzzleHttp\Client;
use Overtrue\Weather\Exceptions\HttpException;
use Overtrue\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @Notes:
     * @return \GuzzleHttp\Client
     * @author: Lijianyun
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }


    /**
     * @Notes:
     * @param array $options
     * @author: Lijianyun
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }


    /**
     * @Notes:
     * @param $city
     * @param string $type
     * @param string $format
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Overtrue\Weather\Exceptions\HttpException
     * @throws \Overtrue\Weather\Exceptions\InvalidArgumentException
     * @author: Lijianyun
     */
    protected function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'http://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => \strtolower($format),
            'extensions' => \strtolower($type),
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @Notes:
     * @param $city
     * @param $format
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Overtrue\Weather\Exceptions\HttpException
     * @author: Lijianyun
     */
    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    /**
     * @Notes:
     * @param $city
     * @param $format
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Overtrue\Weather\Exceptions\HttpException
     * @author: Lijianyun
     */
    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }
}
