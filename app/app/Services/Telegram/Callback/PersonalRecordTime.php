<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use Illuminate\Http\Request;

class PersonalRecordTime extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $time = parent::setTime();

        $this->back = 'PersonalRecordEditTime_'.parent::getRecordID();


        $buttons[] = [['text' => __('Да'), 'callback_data' => 'PersonalEditConfirm_'.$time]];

        return parent::editMessage(__('Сохранить  время?'), parent::buildInlineKeyboard($buttons));
    }
}
