<?php

namespace App\Helpers\Beauty;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BeautyProApi
{
    const BASE_URI = 'https://api.aihelps.com/v1/';
    private Client $guzzle;

    /**
     * BeautyProApi constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->guzzle = new Client([
            'base_uri' => self::BASE_URI,
            'timeout'  => 2.0,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }

    /**
     * @param array $filed
     * @return array
     */
    public function getClients(array $filed = []): array
    {
        if(empty($filed)) {
            $filed = [
                'firstname',
                'middlename',
                'lastname',
                'gender',
                'phone',
                'email'
            ];
        }

        $params = ['fields' => $filed];

        return $this->request('clients', $params);
    }

    protected function exception (GuzzleException $e): array
    {
        return [
            'errors' =>
                json_decode(
                    $e->getResponse()->getBody()->getContents(),
                    JSON_OBJECT_AS_ARRAY
                )
        ];
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @return array
     */
    protected function request(string $url, $parameters = [], $method = 'GET'): array
    {
        try {
            $response = $this->guzzle->request(
                $method,
                $url,
                [
                    'form_params' => $parameters
                ]
            );

            return json_decode($response->getBody()->getContents());

        } catch (GuzzleException $e) {
            return $this->exception($e);
        }
    }
}
