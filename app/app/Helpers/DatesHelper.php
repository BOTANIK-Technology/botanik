<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserTimetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class DatesHelper
{
    public static function masterDates($master_id, $service_id, $address_id, $monthButton)
    {

        switch ($monthButton) {
            case 'DateNext':
                $first_day = new Carbon('first day of next month');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DatesMaster_'.$master_id],
                    self::getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateLater_'.$master_id]
                ];
                $month = UserTimetable::getNextMonthBot();
                break;
            case 'DateLater':
                $first_day = new Carbon('first day of 2 months');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DateNext_'.$master_id],
                    self::getNameOfMonth($first_day),
                    ['text' => ' ', 'callback_data' => '-']
                ];
                $month = UserTimetable::getMonthLaterBot();
                break;
            default:
                $first_day = Carbon::now();
                $date[] = [
                    ['text' => ' ', 'callback_data' => '-'],
                    self::getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateNext_'.$master_id]
                ];
                $month = UserTimetable::getCurrentMonthBot();
        }

        $days = []; //name of the days of the week
        foreach (UserTimetable::getDays() as $day) {
            $days[] = ['text' => $day, 'callback_data' => '-'];
        }
        $date[] = $days;
        unset($days);

        $master = User::find($master_id);
        $master_days = [];
        $i = 1;
        $first_day = Carbon::parse($first_day->format('Y-m-d 00:00:00'));

        foreach ($month as $k => $day) {
            $isWork = UserTimetable::isWorkDay($master, $address_id, $service_id, Carbon::parse($k), $first_day);
            if ($isWork) {
                $master_days[] = ['text' => $day, 'callback_data' => 'Time_'.$k];
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
        return $date;
    }

    public static function getNameOfMonth (Carbon $date): array
    {
        return ['text' => Date::parse($date->toFormattedDateString())->format('F'), 'callback_data' => '-'];
    }
}
