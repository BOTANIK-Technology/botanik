<?php

namespace App\Services\Telegram\Callback;

use App\Models\Share;
use Illuminate\Http\Request;

class ShareView extends CallbackQuery
{
    /**
     * ShareView constructor.
     * @param Request $request
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'Delete_';
        $this->view();
    }

    /**
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    private function view() {
        $share = Share::find($this->getCallbackID());

        if (!$share)
            return;

        if (!is_null($share->button))
            $keyboard = parent::buildInlineKeyboard([[['text' => 'Подробнее', 'url' => $share->button]]]);
        else
            $keyboard = parent::buildInlineKeyboard();

        $mess = '<b>'.$share->title.'</b>'."\n\n".$share->text;

        if (is_null($share->img)) parent::sendMessage($mess, $keyboard);
        else  parent::sendPhoto($share->img, $mess, $keyboard);
    }
}