<?php

namespace App\Traits;

trait JsonFieldTrait
{
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
