<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use App\Models\Service;
use App\Models\User;
use App\Models\UserTimetable;
use Illuminate\Http\Request;
use App\Models\ServiceTimetable;
use Jenssegers\Date\Date;
use Illuminate\Support\Carbon;

class PersonalDatesService extends CallbackQuery
{
    /**
     * DatesService constructor.
     * @param Request $request
     * @param null $month
     */
    public function __construct(Request $request, $month = null)
    {
        parent::__construct($request);
        $record = Record::find(parent::setRecordID());
        parent::setMasterID($record->user_id);
        $this->back = 'PersonalRecordEdit_'.parent::getRecordID();

        if(!empty($record->user_id)) {
            $dates = $this->masterDate($record, $month);
        } else {
            $dates = $this->serviceDates($record->service_id, $month);
        }

        return parent::editMessage(__('Выберете дату'), $dates);
    }

    private function serviceDates($service_id, $month)
    {
        switch ($month) {
            case 'PersonalDateNext':
                $first_day = new Carbon('first day of next month');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'PersonalDatesService_'.parent::getRecordID()],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'PersonalDateLater_'.parent::getRecordID()]
                ];
                $month = ServiceTimetable::getNextMonthBot();
                break;
            case 'PersonalDateLater':
                $first_day = new Carbon('first day of 2 months');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'PersonalDateNext_'.parent::getRecordID()],
                    $this->getNameOfMonth($first_day),
                    ['text' => ' ', 'callback_data' => '-']
                ];
                $month = ServiceTimetable::getMonthLaterBot();
                break;
            default:
                $first_day = Carbon::now();
                $dates[] = [
                    ['text' => ' ', 'callback_data' => '-'],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'PersonalDateNext_'.parent::getRecordID()]
                ];
                $month = ServiceTimetable::getCurrentMonthBot();
        }

        $days = [];
        foreach (ServiceTimetable::getDays() as $day) //name of the days of the week
            $days[] = ['text' => $day, 'callback_data' => '-'];
        $dates[] = $days;

        $service = Service::find($service_id);

        $i = 1;
        $days = [];
        foreach ($month as $k => $day) {

            if ($service->timetable->isWorkDay(Carbon::parse($k), $first_day)) {
                $days[] = ['text' => $day, 'callback_data' => 'PersonalRecordDate_'.$k];
            } else {
                $days[] = ['text' => ' ', 'callback_data' => '-'];
            }

            if ($i % 7 == 0) {
                $dates[] = $days;
                $days = [];
            }

            $i++;
        }
        $i--;
        while ($i % 7 != 0) {
            $days[] = ['text' => ' ', 'callback_data' => '-'];
            $i++;
        }
        $dates[] = $days;
        return parent::buildInlineKeyboard($dates);

    }

    private function masterDate($record , $month)
    {
        $master_id = $record->user_id;
        switch ($month) {
            case 'PersonalDateNext':
                $first_day = new Carbon('first day of next month');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'PersonalDatesService_'.parent::getRecordID()],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'PersonalDateLater_'.parent::getRecordID()]
                ];
                $month = UserTimetable::getNextMonthBot();
                break;
            case 'PersonalDateLater':
                $first_day = new Carbon('first day of 2 months');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'PersonalDateNext_'.parent::getRecordID()],
                    $this->getNameOfMonth($first_day),
                    ['text' => ' ', 'callback_data' => '-']
                ];
                $month = UserTimetable::getMonthLaterBot();
                break;
            default:
                $first_day = Carbon::now();
                $date[] = [
                    ['text' => ' ', 'callback_data' => '-'],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'PersonalDateNext_'.parent::getRecordID()]
                ];
                $month = UserTimetable::getCurrentMonthBot();
        }

        $days = []; //name of the days of the week
        foreach (UserTimetable::getDays() as $day)
            $days[] = ['text' => $day, 'callback_data' => '-'];
        $date[] = $days;
        unset($days);

        $master = User::find($master_id);
        $master_days = [];
        $i = 1;
        foreach ($month as $k => $day) {

            if (UserTimetable::isWorkDay($master, $record->address_id, $record->service_id, Carbon::parse($k), $first_day)) {
                $master_days[] = ['text' => $day, 'callback_data' => 'PersonalRecordDate_'.$k];
            } else {
                $master_days[] = ['text' => ' ', 'callback_data' => '-'];
            }

            if ($i % 7 == 0) {
                $date[] = $master_days;
                $master_days = [];
            }

            $i++;
        }
        $i--;
        while ($i % 7 != 0) {
            $master_days[] = ['text' => ' ', 'callback_data' => '-'];
            $i++;
        }
        $date[] = $master_days;
        return parent::buildInlineKeyboard($date);
    }


    private function getNameOfMonth (Carbon $date) {
        return ['text' => Date::parse($date->toFormattedDateString())->format('F'), 'callback_data' => '-'];
    }
}
