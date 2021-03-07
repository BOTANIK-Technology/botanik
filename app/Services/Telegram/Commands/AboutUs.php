<?php

namespace App\Services\Telegram\Commands;


use App\Models\Information;
use Illuminate\Http\Request;

class AboutUs extends Command
{
    /**
     * AboutUs constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        parent::sendMessage('Выберете раздел, который Вас интересует', $this->getInfo());
    }

    /**
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    private function getInfo() {
        $infos = Information::all();
        $array = [];
        foreach ($infos as $info) {
            $array[] = [['text' => $info->title, 'callback_data' => 'InfoView_'.$info->id]];
        }
        $array[] = [['text' => '< НАЗАД', 'callback_data' => 'Delete_']];
        return parent::buildInlineKeyboard($array);
    }
}
