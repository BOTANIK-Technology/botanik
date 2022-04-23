<?php

namespace App\Services\Telegram\Callback;

use App\Helpers\DatesHelper;
use App\Models\Service;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     */
    public function __construct(Request $request, $month = null)
    {
        parent::__construct($request);
        $this->back = 'Address_'.parent::getServiceID();
        parent::setMasterID(null);
        parent::setAddressID();
        return parent::editMessage('🕛 '.__('Выберете дату'), $this->serviceDates( $month));
    }

    public function serviceDates($month): array
    {
        Log::debug('Confirm', [$this->user->telegramSession]);
        $date = DatesHelper::serviceDates(parent::getServiceID(), parent::getAddressID(), $month);
        return parent::buildInlineKeyboard($date);

    }

}
