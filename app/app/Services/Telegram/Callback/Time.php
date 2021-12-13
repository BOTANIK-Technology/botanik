<?php

namespace App\Services\Telegram\Callback;

use App\Models\User;
use App\Models\UserTimetable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class Time extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $date = parent::setDate();

        if (!is_null(parent::getMasterID()) && !empty(parent::getMasterID())) {
            $this->back = 'DatesMaster_' . parent::getMasterID();
            $buttons = $this->masterTime(parent::getMasterID(), parent::getServiceID(), parent::getAddressID(), $date);
        } else {
            $this->back = 'DatesService_' . parent::getServiceID();
            $buttons = $this->serviceTime(parent::getServiceID(), $date);
        }
        return parent::editMessage(__('Выберете время'), $buttons);
    }

    private function serviceTime(int $service_id, string $date)
    {
        $service = Service::find($service_id);
        $times = $service->timetable->getFreeTimes($date);
        return $this->getButtons($times);
    }

    private function masterTime(int $master_id, int $service_id, int $address_id, string $date)
    {
        $master = User::find($master_id);
        $times = UserTimetable::getFreeTimes($master, $address_id, $service_id, $date);
        Log::info('Time: ', $times);
        $timeNew = [];
        $max = count($times) - 1;
        for ($i = 0; $i <= $max; $i++) {
            $timeNew[] = $times[$i];
            $timeNew[] = str_replace(':00', ':30', $times[$i] );
            Log::info($i . ' - Time: ', $timeNew);
        }
        $timeNew[] = $times[$max];
        Log::info($max . ' - Time: ', $timeNew);
        return $this->getButtons($timeNew);
    }

    private function getButtons($times)
    {
        $buttons = [];
        if (empty($times))
            $buttons[] = [['text' => __('Нет свободных ячеек.'), 'callback_data' => '-']];
        else
            foreach ($times as $time)
                $buttons[] = [['text' => $time, 'callback_data' => 'ConfirmRecord_' . $time]];

        return parent::buildInlineKeyboard($buttons);
    }


}
