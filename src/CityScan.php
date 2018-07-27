<?php

namespace CityScan;

use GuzzleHttp\Client;

/**
 * Class CityScan
 * @package CityScan
 */
class CityScan
{

    /**
     * @var string Api key
     */
    private $api_key;

    /**
     * @var string Environment
     */
    private $environment;

    /**
     * @var \GuzzleHttp\Client Api client
     */
    private $client;

    /**
     * @var array Base URLs for each environments
     */
    private static $base_urls = [
        'preprod' => 'https://preprod.cityscan.fr/api/',
        'prod' => 'https://www.cityscan.fr/api/',
    ];

    /**
     * CityScan constructor.
     * @param $api_key
     * @param string $environment Destination environment : prod or preprod
     * @throws \Exception
     */
    public function __construct($api_key, $environment = 'prod')
    {
        $this->api_key = $api_key;
        $this->environment = $environment;

        if (!array_key_exists($environment, static::$base_urls)) {
            throw new \Exception('The environment variable is incorrect, it must be one of the following values: ' . implode(', ', array_keys(static::$base_urls)));
        }

        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
            'timeout' => 10,
        ]);
    }

    /**
     * @return mixed
     */
    private function getBaseUrl()
    {
        return static::$base_urls[$this->environment];
    }

    /**
     * @param $method
     * @param $uri
     * @param $params
     * @return \StdClass Json data
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function request($method, $uri, $params)
    {
        $params_with_auth = array_merge($params, ['apiKey' => $this->api_key]);

        $response = $this->client->request($method, $uri, ['json' => $params_with_auth]);

        $json_res = json_decode((string)$response->getBody());

        if ($json_res->status) {
            throw new \Exception($json_res->message, $json_res->errorCode);
        }

        return $json_res->content;
    }

    /**
     * Activate an address by it's road, postal code and city
     *
     * @param string $road Number and street name
     * @param string|int $postal_code Postal code
     * @param string $city City
     * @param string $external_id External ID
     * @return \StdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function activateAddressByRoad($road, $postal_code, $city, $external_id = null)
    {
        $params = [
            'road' => $road,
            'postalCode' => $postal_code,
            'city' => $city,
        ];

        if ($external_id) {
            $params['externalAddressId'] = $external_id;
        }

        return $this->request('POST', 'address/activation', $params)->activation;
    }

    /**
     * Activate an address by it's road, postal code and city
     *
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $external_id External ID
     * @return \StdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function activateAddressByGPS($latitude, $longitude, $external_id = null)
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($external_id) {
            $params['externalAddressId'] = $external_id;
        }

        return $this->request('POST', 'address/activation', $params)->activation;
    }

    /**
     * Correct an address by it's road, postal code and city
     *
     * @param string $road Number and street name
     * @param string|int $postal_code Postal code
     * @param string $city City
     * @param string $external_id External ID
     * @return \StdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function correctAddressByRoad($road, $postal_code, $city, $external_id = null)
    {
        $params = [
            'road' => $road,
            'postalCode' => $postal_code,
            'city' => $city,
        ];

        if ($external_id) {
            $params['externalAddressId'] = $external_id;
        }

        return $this->request('POST', 'address/activation', $params)->activation;
    }

    /**
     * Activate an address by it's road, postal code and city
     *
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $external_id External ID
     * @return \StdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function correctAddressByGPS($latitude, $longitude, $external_id = null)
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($external_id) {
            $params['externalAddressId'] = $external_id;
        }

        return $this->request('POST', 'address/activation', $params)->activation;
    }

    /**
     * Desactivate an address
     *
     * @param string|int $id Id of the addresse
     * @param bool $isExternal Define if the addresse id is external or not
     * @return \StdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deactivateAddress($id, $isExternal = false)
    {
        if ($isExternal) {
            $params = [
                'externalAddressId' => $id
            ];
        } else {
            $params = [
                'addressId' => $id
            ];
        }

        return $this->request('POST', 'address/deactivation', $params);
    }

}