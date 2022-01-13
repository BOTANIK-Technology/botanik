<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class PersonalDateNext extends CallbackQuery
{
    /**
     * PersonalDateNext constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        return new PersonalDatesService($request, 'PersonalDateNext');
    }
}
