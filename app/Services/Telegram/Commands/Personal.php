<?php

namespace App\Services\Telegram\Commands;

use Illuminate\Http\Request;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Personal extends Command
{
    /**
     * Personal constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        parent::sendMessage('🗝 Личный кабинет', $this->getShares());
    }

    /**
     * @return InlineKeyboardMarkup
     */
    private function getShares()
    {
        $array[] = [['text' => '⌨️ Мои записи', 'callback_data' => 'PersonalRecords_']];

        if (parent::hasPackage('pro'))
            $array[] = [['text' => 'Баланс баллов', 'callback_data' => 'PersonalBalance_']];

        $array[] = [['text' => '< НАЗАД', 'callback_data' => 'Delete_']];

        return parent::buildInlineKeyboard($array);
    }
}
