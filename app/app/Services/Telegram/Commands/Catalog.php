<?php

namespace App\Services\Telegram\Commands;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class Catalog extends Command
{
    /**
     * Catalog constructor.
     * @param Request $request
     * @param bool $back
     * @throws GuzzleException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        if (is_null($this->pay_token)) {
            parent::sendMessage('Каталог временно недоступен.');
        } else {
            parent::sendMessage('Выберете товар, который Вас интересует', $this->getCatalog());
        }
    }

    /**
     */
    private function getCatalog() {
        $catalog = \App\Models\Catalog::all();
        $array = [];
        foreach ($catalog as $item)
            if ($item->count > 0)
                $array[] = [['text' => $item->title, 'callback_data' => 'Product_'.$item->id]];
        $array[] = [['text' => '< НАЗАД', 'callback_data' => 'Delete_']];
        return parent::buildInlineKeyboard($array);
    }
}
