<?php

namespace App\Services\Telegram\Commands;

use Illuminate\Http\Request;
use App\Models\TypeService;

class TypesOfServices extends Command
{
    /**
     * TypesService constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        return $this->sendMessage(__('Выберите тип услуги для записи'), $this->getServices());
    }

    /**
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
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