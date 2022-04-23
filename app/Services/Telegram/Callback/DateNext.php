<?php

namespace App\Services\Telegram\Callback;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class DateNext extends CallbackQuery
{
    /**
     * DateNext constructor.
     * @param Request $request
     * @throws GuzzleException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if( is_null(parent::getMasterID()) ) {
            return new DatesService($request, 'DateNext');
        }
        return new DatesMaster($request, 'DateNext');
    }
}
