<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class PersonalDateLater extends CallbackQuery
{
    /**
     * PersonalDateLater constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        return new PersonalDatesService($request, 'PersonalDateLater');
    }
}
