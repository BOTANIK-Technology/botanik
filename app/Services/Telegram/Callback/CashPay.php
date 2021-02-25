<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class CashPay extends CallbackQuery
{
    /**
     * CashPay constructor.
     * @param Request $request
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = $this->createRecord();
        if ($record) {
            parent::deleteMessage();
            parent::getMenu(__('Спасибо! Активные записи доступны в <b>личном кабинете</b>.'));
        }
    }
}