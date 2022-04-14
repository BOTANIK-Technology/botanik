<?php

namespace App\Services\Telegram\Callback;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Product extends CallbackQuery
{
    /**
     * Product constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'Catalog_';
        $this->view();
    }

    /**
     * @throws GuzzleException
     */
    private function view() {

        $product = \App\Models\Catalog::find($this->getCallbackID());
        $product->writeToReport(true);

        if (!$product)
            return;

        $this->bot->sendInvoice(
            $this->chat_id,
            $product->title,
            $product->text,
            json_encode([
                "handler" => 'OnlinePayProduct',
                "product_id" => $product->id,
            ]),
            $this->pay_token,
            $this->chat_id,
            'UAH',
            [
                ['label' => $product->title, 'amount' => $product->price * 100],
            ],
            false,
            $product->img,
            200,
            200,
            200
        );
    }
}
