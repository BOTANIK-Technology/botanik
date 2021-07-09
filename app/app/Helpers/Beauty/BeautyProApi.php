<?php

namespace App\Helpers\Beauty;

use App\Models\Api;
use Carbon\Carbon;
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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $params = [
            'base_uri' => self::BASE_URI,
            'timeout'  => 2.0,
        ];

        $this->guzzle = new Client($params);
        $response = $this->getBearer($config['application_id'], $config['application_secret'], $config['database_code']);
        $token = $response['access_token'];

        if (!empty($token)) {
            $this->token = $token;
            $params['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }
        $this->guzzle = new Client($params);

        if (
            isset($config['expires_at']) &&
            isset($config['access_token']) &&
            Carbon::parse($config['access_token'])->greaterThan(Carbon::now())
        )
            return $config['access_token'];

        if (
            !isset($config['application_id']) ||
            !isset($config['application_secret']) ||
            !isset($config['database_code'])
        )
            return [
                'errors' => [
                    __('Заполните настройки для API интеграции.')
                ]
            ];

        if (isset($response['status']) && ($response['status'] != 'pending' || $response['status'] != 'refused')) {
            $model = Api::where('slug', 'beauty')->first();
            $data = array_merge($config, [
                'access_token' => $response['access_token'],
                'expires_at' => $response['expires_at']
            ]);

            $model->updateConfig($data);
        }

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
        // We not have accvess

        return [
            "success" => true,
            "data" => []
        ];


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
        $params['headers'] = [
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $params = array_merge($params, ['fields' => $filed]);

        return $this->request('clients', $params);
    }

    /**
     * @return array
     */
    public function getStaff(): array
    {
        return $this->request('employees?fields=name');
    }

    /**
     * @param array $clients
     * @return array
     */
    public function addClients(array $clients): array
    {
        // We not have access
        return [];
    }

    /**
     * @param array $staffs
     * @return array
     */
    public function addStaff(array $staffs): array
    {
        // We not have access
        return [];
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
