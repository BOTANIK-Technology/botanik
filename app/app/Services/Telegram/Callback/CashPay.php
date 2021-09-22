<?php

namespace App\Services\Telegram\Callback;

use App\Helpers\Yclients\YclientsException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CashPay extends CallbackQuery
{
    /**
     * CashPay constructor.
     * @param Request $request
     * @throws YclientsException|GuzzleException
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
