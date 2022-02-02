<?php

namespace App\Http\Controllers;

use App\Models\ServiceTimetable;
use App\Models\UserTimetable;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Default params for view
     *
     * @var array
     */
    public array $params = [];

    public function setParams(Request $request)
    {
        $this->params['current_month'] = $request->has('current_month') ? $request->input('current_month') : mb_strtolower(Carbon::now()->format('F'));

        $this->params['daysWeek'] = ServiceTimetable::getDays();
        $this->params['daysMonth'] = UserTimetable::getFullDaysOfMonth($this->params['current_month']);
        $this->params['allMonth'] = UserTimetable::getMonthList($this->params['current_month']);
        $this->params['times'] = ServiceTimetable::getHours();
    }
}
