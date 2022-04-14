<?php

namespace App\Helpers\Beauty\Old;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BeautyProApi
{
    const BASE_URI = 'https://api.aihelps.com/v1/';

    private Client $guzzle;
    private string $token;

    /**
     * BeautyProApi constructor.
     * @param string $token
     */
    public function __construct(string $token = '')
    {
        $params = [
            'base_uri' => self::BASE_URI,
            'timeout'  => 2.0,
        ];

        if (!empty($token)) {
            $this->token = $token;
            $params['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        $this->guzzle = new Client($params);
    }

    /**
     * @param string $application_id
     * @param string $application_secret
     * @param int $database_code
     * @return array
     */
    public function getBearer (string $application_id, string $application_secret, int $database_code): array
    {
        return $this->request(
            'auth/database',
            [
                'application_id' => $application_id,
                'application_secret' => $application_secret,
                'database_code' => $database_code
            ],
            'query'
        );
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
     * @param string $params_type
     * @param string $method
     * @return array
     */
    protected function request(string $url, array $parameters = [], string $params_type = 'form_params', string $method = 'GET'): array
    {
        try {
            Log::debug("Beauty URL: " . $url);
            $response = $this->guzzle->request(
                $method,
                $url,
                [
                    $params_type => $parameters
                ]
            );

            return json_decode($response->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);

        } catch (GuzzleException $e) {
            return $this->exception($e);
        }
    }
}
