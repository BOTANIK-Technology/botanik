<?php

namespace App\Helpers\Beauty;

use App\Models\Api;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BeautyPro
{
    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    public static function apiCall (string $method, $params = []): array
    {
        $token = self::auth();

        if(is_array($token))
            return $token;

        $api = new BeautyProApi($token);

        if (method_exists($api,$method)) {
            if (empty($params))
                return $api->$method();
            return $api->$method($params);
        }

        return ['errors' => ['message' => 'The method does not exist.']];
    }

    /**
     * @return array|string
     */
    public static function auth ()
    {
        $config = self::getConfig();
        $api = new BeautyProApi();

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

        $response = $api->getBearer($config['application_id'], $config['application_secret'], $config['database_code']);
        Log::debug("BeautyPro response:", $response);
        if (
            isset($response['status']) &&
            (
                $response['status'] == 'pending' ||
                $response['status'] == 'refused'
            )
        )
            return [
                'errors' => [
                    '1. Call this method with specified application_id, application_secret and database_code will create access request. (ALREADY DONE)',
                    '2. User login into desktop or web application, go to Settings->Marketplace, select needed application and press Grant access (in marketplace only public applications and applications that asked for access are shown).',
                    '3. Call this method once again with specified application_id, application_secret and database_code will return access token information.'
                ]
            ];

        $model = Api::where('slug', 'beauty')->first();
        $data = array_merge($config, [
            'access_token' => $response['access_token'],
            'expires_at' => $response['expires_at']
        ]);

        $model->updateConfig($data);

        return $response['access_token'];
    }

    /**
     * @return array
     */
    public static function getConfig (): array
    {
        $data = self::getData();
        return json_decode($data->config, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return Api|Builder|Model|object|null
     */
    public static function getData ()
    {
        return Api::where('slug', 'beauty')->firstOrFail();
    }
}
