<?php

namespace App\Helpers\Beauty;

use App\Models\Api;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BeautyPro
{
    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    public static function apiCall (string $method, $params = []): array
    {
        $config = self::getConfig();
        $api = new BeautyProApi($config['partner_token']);

        if (method_exists($api,$method)) {
            if (empty($params))
                return $api->$method();
            return $api->$method($params);
        }

        return [];
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
