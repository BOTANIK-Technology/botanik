<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class PersonalRecords extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'Personal_';
        parent::editMessage(__('Мои записи'), $this->getRecords());
    }

    private function getRecords ()
    {
        if (!isset($this->user->records) || $this->user->records->isEmpty())
            return parent::buildInlineKeyboard();

        $records = [];
        foreach ($this->user->records as $record)
            $records[] = [['text' => __('Запись #').$record->id, 'callback_data' => 'PersonalRecord_'.$record->id]];

        return parent::buildInlineKeyboard($records);
    }
}