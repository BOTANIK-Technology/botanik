<?php

namespace App\Services\Telegram\Callback;


use App\Helpers\DatesHelper;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Models\UserTimetable;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Illuminate\Support\Carbon;

class DatesMaster extends CallbackQuery
{
    /**
     * DatesMaster constructor.
     * @param Request $request
     * @param null $month
     * @throws GuzzleException
     */
    public function __construct(Request $request, $month = null)
    {
        parent::__construct($request);
        $master_id = parent::setMasterID();
        $this->back = 'Master_'.parent::getAddressID();
        return parent::editMessage('🕛 '.__('Выберете дату'), $this->masterDate($master_id, $month));
    }

    private function masterDate($master_id, $month): array
    {
        $date = DatesHelper::MasterDates($master_id, parent::getServiceID(), parent::getAddressID(), $month);
        return parent::buildInlineKeyboard($date);
    }

}
