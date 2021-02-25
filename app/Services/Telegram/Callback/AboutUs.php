<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class AboutUs extends CallbackQuery
{
    /**
     * Service constructor.
     * @param Request $request
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->deleteMessage();
        new \App\Services\Telegram\Commands\AboutUs($request, true);
    }
}