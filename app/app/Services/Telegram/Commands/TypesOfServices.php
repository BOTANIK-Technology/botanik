<?php

namespace App\Services\Telegram\Commands;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Models\TypeService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TypesOfServices extends Command
{
    /**
     * TypesService constructor.
     * @param Request $request
     * @param bool $back
     * @throws GuzzleException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        return $this->sendMessage(__('Выберете тип услуги для записи'), $this->getServices());
    }

    /**
     * @return array
     */
    public function getServices()
    {
        $types = TypeService::all();
        $array = [];
        foreach ($types as $type) {
            $array[] = [['text' => $type->type, 'callback_data' => 'Service_'.$type->id]];
        }
        return parent::buildInlineKeyboard($array);
    }
}
