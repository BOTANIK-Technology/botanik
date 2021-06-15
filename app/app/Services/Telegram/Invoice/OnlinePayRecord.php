<?php

namespace App\Services\Telegram;


use Illuminate\Http\Request;

class OnlinePayRecord extends TelegramAPI
{
    public function __construct(Request $request, $payload)
    {
        parent::__construct($request);
        $id = (substr($payload, strpos($payload, '_', 1)+1, 100));
        if (parent::getServiceID() == $id) {
            $record = parent::createRecord(true, true);
            if ($record) {
                $this->getResponse($request);
            } else {
                $this->getResponse($request, false, __('Ошибка 501. Запись не создана.'));
            }
        }
        $this->getResponse($request, false, __('Информация устарела. Пожалуйста, выполните запись заново.'));
    }

    public function getResponse (Request $request, $ok = true, $message = null)
    {
        return $this->bot->answerPreCheckoutQuery($request->input('pre_checkout_query.id'), $ok, $message);
    }
}