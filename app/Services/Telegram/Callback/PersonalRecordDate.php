<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use Illuminate\Http\Request;

class PersonalRecordDate extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        parent::setDate();
        $record = Record::find(parent::getRecordID());
        $this->back = 'PersonalDatesService_'.parent::getRecordID();

        if ( !is_null($record->user_id) && !empty($record->user_id) ) {
            $buttons = $this->masterTime($record);
        } else {
            $buttons = $this->serviceTime($record);
        }

        return parent::editMessage(__('Выберите время'), $buttons);
    }

    private function serviceTime ($record)
    {
        $service = \App\Models\Service::find($record->service_id);
        $times = $service->timetable->getFreeTimes($record->date,$record->time);
        return $this->getButtons($times);
    }

    private function masterTime($record)
    {
        $master = \App\Models\User::find($record->user_id);
        $times = \App\Models\UserTimetable::getFreeTimes($master, $record->address_id, $record->service_id, $record->date,$record->time);
        return $this->getButtons($times);
    }

    private function getButtons ($times)
    {
        $buttons = [];
        if (empty($times))
            $buttons[] = [['text' => __('Нет свободных ячеек.'), 'callback_data' => '-']];
        else
            foreach ($times as $time)
                $buttons[] = [['text' => $time, 'callback_data' => 'PersonalRecordTime_' . $time]];

        return parent::buildInlineKeyboard($buttons);
    }
}
