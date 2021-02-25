<?php

namespace App\Services\Telegram;


use Illuminate\Http\Request;

class OnlinePayProduct extends TelegramAPI
{
    public function __construct(Request $request, $payload)
    {
        parent::__construct($request);
        $id = (substr($payload, strpos($payload, '_', 1)+1, 100));
        $product = \App\Models\Catalog::find($id);
        if ($product && $product->count > 0) {
            $product->writeToReport();
            $this->getResponse($request);
        } else {
            $this->getResponse($request, false, __('Продукт не найден.'));
        }
    }

    public function getResponse (Request $request, $ok = true, $message = null)
    {
        return $this->bot->answerPreCheckoutQuery($request->input('pre_checkout_query.id'), $ok, $message);
    }
}