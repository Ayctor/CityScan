<?php

namespace CityScan;

use GuzzleHttp\Client;

/**
 * Class CityScan
 * @package CityScan
 */
class CityScan
{
    private string $api_key;

    private string $client_key;

    private string $environment;

    private \GuzzleHttp\Client $client;

    private static array $base_urls = [
        'preprod' => 'https://preprod.cityscan.fr/api/',
        'prod' => 'https://www.cityscan.fr/api/',
    ];

    /**
     * CityScan constructor.
     * @param $api_key
     * @param string $environment Destination environment : prod or preprod
     * @throws \Exception
     */
    public function __construct(string $api_key, string $client_key = '', string $environment = 'prod')
    {
        $this->api_key = $api_key;
        $this->client_key = $client_key;
        $this->environment = $environment;

        if (!array_key_exists($environment, static::$base_urls)) {
            throw new \Exception('The environment variable is incorrect, it must be one of the following values: ' . implode(', ', array_keys(static::$base_urls)));
        }

        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
            'timeout' => 10,
            'http_errors' => false,
            'headers' => [
                'ApiKey' => $this->api_key,
            ],
        ]);
    }

    private function getBaseUrl(): string
    {
        return static::$base_urls[$this->environment];
    }

    /**
     * @param $method
     * @param $uri
     * @param $params
     * @return \stdClass Json data
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function request($method, $uri, $params): \stdClass
    {
        $body_params = ['json' => array_merge($params, ['apiKey' => $this->api_key])];

        // If it's a report, we extend the timeout to 2 minutes
        if ($uri == 'widget/report') {
            $body_params = [
                'json' => array_merge($params, ['clientKey' => $this->client_key]),
                'timeout' => 120,
            ];
        }

        $response = $this->client->request($method, $uri, $body_params);

        $json_res = json_decode((string)$response->getBody());

        if (!isset($json_res->status)) {
            throw new \Exception('No response', 500);
        }

        if ($json_res->status) {
            throw new \Exception($json_res->message, $json_res->error);
        }

        return $json_res->content;
    }

    /**
     * @param $method
     * @param $uri
     * @param $params
     * @return \stdClass Json data
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function newRequest($method, $uri, $params = []): \stdClass
    {

        $response = $this->client->request($method, $uri, $params);

        $json_res = json_decode((string)$response->getBody());

        if (!isset($json_res->status)) {
            throw new \Exception('No response', 500);
        }

        if ($json_res->status) {
            throw new \Exception($json_res->message, $json_res->error);
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
     * @return \stdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function activateAddressByRoad($road, $postal_code, $city, $external_id = null): \stdClass
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
     * @return \stdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function activateAddressByGPS($latitude, $longitude, $external_id = null): \stdClass
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
     * @return \stdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function correctAddressByRoad($road, $postal_code, $city, $id, $isExternal = false): \stdClass
    {
        $params = [
            'road' => $road,
            'postalCode' => $postal_code,
            'city' => $city,
        ];

        if ($isExternal) {
            $params['externalAddressId'] = $id;
        } else {
            $params['oldAddressId'] = $id;
        }

        return $this->request('POST', 'address/correction', $params)->activation;
    }

    /**
     * Activate an address by it's road, postal code and city
     *
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $external_id External ID
     * @return \stdClass Json data on the address
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function correctAddressByGPS($latitude, $longitude, $id, $isExternal = false): \stdClass
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($isExternal) {
            $params['externalAddressId'] = $id;
        } else {
            $params['oldAddressId'] = $id;
        }

        return $this->request('POST', 'address/correction', $params)->activation;
    }

    /**
     * Generate a report for an address
     *
     * @param string|int $id Id of the addresse
     * @param bool $isExternal Define if the addresse id is external or not
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function report($id, $isExternal = false): \stdClass
    {
        if ($isExternal) {
            $params = [
                'externalAddressId' => $id,
            ];
        } else {
            $params = [
                'addressId' => $id,
            ];
        }

        return $this->request('POST', 'widget/report', $params);
    }

    /**
     * Desactivate an address
     *
     * @param string|int $id Id of the addresse
     * @param bool $isExternal Define if the addresse id is external or not
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deactivateAddress($id, $isExternal = false): \stdClass
    {
        if ($isExternal) {
            $params = [
                'externalAddressId' => $id,
            ];
        } else {
            $params = [
                'addressId' => $id,
            ];
        }

        return $this->request('POST', 'address/deactivation', $params);
    }

    /**
     * Reactivate addresses
     *
     * @param array $ids
     * @param bool $isExternal Define if the address id is external or not
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reactivateAddresses($ids, $isExternal = false): \stdClass
    {
        if ($isExternal) {
            $params = [
                'extAddressIds' => $ids,
            ];
        } else {
            $params = [
                'addressIds' => $ids,
            ];
        }

        return $this->request('POST', 'addresses/reactivate', $params);
    }

    /**
     * Get all active addresses
     *
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActives(): \stdClass
    {
        return $this->newRequest('GET', 'addresses/active');
    }

    /**
     * Get all activated addresses between two dates
     *
     * @param string|null $start
     * @param string|null $end
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActivated($start = null, $end = null): \stdClass
    {
        $params = [];
        $params['query'] = [];
        if (!is_null($start)) {
            $params['query']['start'] = $start;
        }
        if (!is_null($end)) {
            $params['query']['end'] = $end;
        }
        return $this->newRequest('GET', 'addresses/activated', $params);
    }

    /**
     * Get all billed addresses between two dates
     *
     * @param string|null $start
     * @param string|null $end
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBilled($start = null, $end = null): \stdClass
    {
        $params = [];
        $params['query'] = [];
        if (!is_null($start)) {
            $params['query']['start'] = $start;
        }
        if (!is_null($end)) {
            $params['query']['end'] = $end;
        }
        return $this->newRequest('GET', 'addresses/billed', $params);
    }

    /**
     * Get all deactivated addresses between two dates
     *
     * @param string|null $start
     * @param string|null $end
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDeactivated($start = null, $end = null): \stdClass
    {
        $params = [];
        $params['query'] = [];
        if (!is_null($start)) {
            $params['query']['start'] = $start;
        }
        if (!is_null($end)) {
            $params['query']['end'] = $end;
        }
        return $this->newRequest('GET', 'addresses/deactivated', $params);
    }

    /**
     * Get all addresses
     *
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAll(): \stdClass
    {
        return $this->newRequest('GET', 'addresses/all');
    }
}
