<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use Illuminate\Http\Request;

class PersonalRecordDate extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $date = parent::setDate();

        $this->back = 'PersonalDatesService_'.parent::getRecordID();

        $buttons[] = [['text' => __('Да'), 'callback_data' => 'PersonalEditConfirm_'.$date]];

        return parent::editMessage(__('Сохранить дату?'), parent::buildInlineKeyboard($buttons));
    }
}
