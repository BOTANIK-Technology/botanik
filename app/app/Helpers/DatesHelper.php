<?php

namespace App\Helpers;

use App\Models\Service;
use App\Models\ServiceTimetable;
use App\Models\User;
use App\Models\UsersSlots;
use App\Models\UsersTimetables;
use App\Models\UserTimetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class DatesHelper
{
    public static function getNameOfMonth (Carbon $date): array
    {
        return ['text' => Date::parse($date->toFormattedDateString())->format('F'), 'callback_data' => '-'];
    }

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
            $isWork = $master->isWorkDay($address_id, $service_id, Carbon::parse($k), $first_day);
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



        public static function masterTimes($master_id, $service_id, $address_id, $date)
    {
        /** @var User $master */
        $master = User::find($master_id);
        $times = $master->getTimesForDate($date);
    }

    /**
     * @param User $user
     * @param int $address_id
     * @param int $service_id
     * @param string $date
     * @param null $ignore_time
     * @return array
     */
    public static function getFreeMasterTimes(User $user, int $address_id, int $service_id, string $date, $ignore_time = null)
    {
        $check_hours = \Carbon\Carbon::parse($date)->isToday();
        $now = Carbon::now();


        // Расписание мастера
        $times = $user->getTimesForDate($address_id, $service_id, $date);
        if (!$times) {
            return [];
        }


        // параметры услуги
        $slot = $user->slots()->where('address_id', $address_id)
            ->where('service_id', $service_id)
            ->first();

        // Список уже забронированных услуг с длительностью
        $booked_array = UserTimetable::getBookedTimes($user, $date);



        if (!$booked_array) {
            $booked_array = [];
        }

        // массив-карта слотов мастера
        $timeMap = [];
        $masterEndSlot = count($times) - 1;

        //сначала заполним единицами (доступно все)
        for ($i = 0; $i <= $masterEndSlot; $i++) {
            // если запись на сегодня - отбросим уже прошедшие слоты
            if ($check_hours && !Carbon::parse($times[$i])->greaterThanOrEqualTo($now)) {
                $timeMap[$i] = 0;
            } else {
                $timeMap[$i] = 1;
            }
        }

        // Длительность проверяемой услуги
        $duration = $slot->service->interval->minutes + $slot->service->range;

        // Число слотов по 30мин в проверяемой услуге
        $serviceSlotsCount = intdiv($duration, 30);
        if ($duration % 30) {
            $serviceSlotsCount++;
        }

        // Перебираем все созданные услуги и отмечаем недоступные из-них слоты
        foreach ($booked_array as $book => $bookDuration) {

            //Если прилетел слот для игнорирования (при правке даты уже созданной записи) - то мы его игнорируем
            if($book == $ignore_time) {
                continue;
            }
            // получим число слотов в текущей услуге
            $bookSlotsCount = intdiv($bookDuration, 30);
            if ($bookDuration % 30) {
                $bookSlotsCount++;
            }

            // Слот, соответствующий началу текущей услуги
            $timeBegin = array_search($book, $times);

            //уберем недоступные в процессе выполнения текущей услуги слоты
            for ($i = 0; $i < $bookSlotsCount; $i++) {
                $timeMap[$timeBegin + $i] = 0;
            }


            for ($i = 0; $i < $serviceSlotsCount; $i++) {
                // уберем слоты перед текущей услугой - в которые мы не сможем втиснуться по времени
                if ($i <= $timeBegin) {
                    $timeMap[$timeBegin - $i] = 0;
                }
            }
        }

        for ($i = 0; $i < $serviceSlotsCount; $i++) {
            // Уберем слоты с конца рабочего дня
            if ($i < $masterEndSlot) {
                $timeMap[$masterEndSlot - $i] = 0;
            }
        }

        // Перенесем карту слотов в формат времени
        $free = [];
        foreach ($timeMap as $key => $value) {
            if ($value) {
                $free[] = $times[$key];
            }
        }
        Log::info('timeMap', $timeMap);
        return $free;
    }

    public static function serviceDates($service_id, $address_id, $monthButton)
    {

        switch ($monthButton) {
            case 'DateNext':
                $first_day = new Carbon('first day of next month');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DatesService_'.$address_id],
                    self::getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateLater_'.$address_id]
                ];
                $month = ServiceTimetable::getNextMonthBot();
                break;
            case 'DateLater':
                $first_day = new Carbon('first day of 2 months');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DateNext_'.$address_id],
                    self::getNameOfMonth($first_day),
                    ['text' => ' ', 'callback_data' => '-']
                ];
                $month = ServiceTimetable::getMonthLaterBot();
                break;
            default:
                $first_day = Carbon::now();
                $dates[] = [
                    ['text' => ' ', 'callback_data' => '-'],
                    self::getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateNext_'.$address_id]
                ];
                $month = ServiceTimetable::getCurrentMonthBot();
        }

        $days = [];
        $first_day = Carbon::parse($first_day->format('Y-m-d 00:00:00'));
        foreach (ServiceTimetable::getDays() as $day) //name of the days of the week
        {
            $days[] = ['text' => $day, 'callback_data' => '-'];
        }
        $dates[] = $days;

        /** @var Service $service */
        $service = Service::find($service_id);

        $i = 1;
        $days = [];
        foreach ($month as $k => $day) {

            if ($service->isWorkDay(Carbon::parse($k), $first_day)) {
                $days[] = ['text' => $day, 'callback_data' => 'Time_'.$k];
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
        return $dates;
    }

}
