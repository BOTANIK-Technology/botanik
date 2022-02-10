<?php

namespace App\Services\Telegram\Callback;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceTimetable;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Illuminate\Support\Carbon;

class DatesService extends CallbackQuery
{
    /**
     * DatesService constructor.
     * @param Request $request
     * @param null $month
     */
    public function __construct(Request $request, $month = null)
    {
        parent::__construct($request);
        $this->back = 'Address_'.parent::getServiceID();
        parent::setMasterID(null);
        parent::setAddressID();
        return parent::editMessage('🕛 '.__('Выберете дату'), $this->serviceDates(parent::getServiceID(), $month));
    }

    public function serviceDates($service_id, $month)
    {

        switch ($month) {
            case 'DateNext':
                $first_day = new Carbon('first day of next month');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DatesService_'.parent::getAddressID()],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateLater_'.parent::getAddressID()]
                ];
                $month = ServiceTimetable::getNextMonthBot();
                break;
            case 'DateLater':
                $first_day = new Carbon('first day of 2 months');
                $dates[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DateNext_'.parent::getAddressID()],
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
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateNext_'.parent::getAddressID()]
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
        Log::info('Month:', $month);
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
        return parent::buildInlineKeyboard($dates);

    }

    private function getNameOfMonth (Carbon $date) {
        return ['text' => Date::parse($date->toFormattedDateString())->format('F'), 'callback_data' => '-'];
    }
}
