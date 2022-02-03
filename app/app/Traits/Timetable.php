<?php

namespace App\Traits;

use App\Models\Record;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DatePeriod;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;

trait Timetable
{
    /**
     * @var array
     */
    public static array $hours = [
        '00:00',
        '00:30',
        '01:00',
        '01:30',
        '02:00',
        '02:30',
        '03:00',
        '03:30',
        '04:00',
        '04:30',
        '05:00',
        '05:30',
        '06:00',
        '06:30',
        '07:00',
        '07:30',
        '08:00',
        '08:30',
        '09:00',
        '09:30',
        '10:00',
        '10:30',
        '11:00',
        '11:30',
        '12:00',
        '12:30',
        '13:00',
        '13:30',
        '14:00',
        '14:30',
        '15:00',
        '15:30',
        '16:00',
        '16:30',
        '17:00',
        '17:30',
        '18:00',
        '18:30',
        '19:00',
        '19:30',
        '20:00',
        '20:30',
        '21:00',
        '21:30',
        '22:00',
        '22:30',
        '23:00',
        '23:30',
    ];

    public static array $intervals = [
        '30 minutes',
        '45 minutes',
        '1 hour',
        '2 hours',
        '3 hours',
        '4 hours',
        '5 hours',
        '6 hours',
        '8 hours',
        '12 hours',
        '24 hours',
    ];

    private static array $months = [
        'january'   => 'январь',
        'february'  => 'февраль',
        'march'     => 'март',
        'april'     => 'апрель',
        'may'       => 'май',
        'june'      => 'июнь',
        'july'      => 'июль',
        'august'    => 'август',
        'september' => 'сентябрь',
        'october'   => 'октябрь',
        'november'  => 'ноябрь',
        'december'  => 'декабрь',
    ];

    public static array $days = [
        'monday'    => 'ПН',
        'tuesday'   => 'ВТ',
        'wednesday' => 'СР',
        'thursday'  => 'ЧТ',
        'friday'    => 'ПТ',
        'saturday'  => 'СБ',
        'sunday'    => 'ВС'
    ];

    public static function getMonthList($current = null)
    {
        if ($current) {
            $res = [];
            foreach (self::$months as $key => $name) {
                if (Carbon::parse($current)->lessThanOrEqualTo(Carbon::parse($key))) {
                    $res[$key] = $name;
                }
            }
        }
        else {
            $res = self::$months;
        }
        return $res;
    }

    public static function getYearList(): array
    {
        return [
            date('Y'),
            date('Y', strtotime('+1 year')),
            date('Y', strtotime('+2 year')),
            date('Y', strtotime('+3 year')),
        ];
    }

    /**
     * @return array
     */
    public static function getHours(): array
    {
        return self::$hours;
    }

    /**
     * @return array
     */
    public static function getDays(): array
    {
        return self::$days;
    }

    /**
     * @return array
     */
    public static function getDaysEn(): array
    {
        return array_keys(self::$days);
    }

    /**
     * @return Collection
     */
    public static function getDaysRu(): Collection
    {
        return collect(array_values(self::$days));
    }

    /**
     * @return Collection
     */
    public static function getMonths(): Collection
    {
        return collect(self::$months);
    }

    /**
     * @return Collection
     */
    public static function getMonthsEn(): Collection
    {
        return collect(array_keys(self::$months));
    }

    /**
     * @return array
     */
    public static function getMonthsRu(): array
    {
        return array_values(self::$months);
    }

    /**
     * Returned schedule for view
     *
     * @return array
     */
    public function schedule(): array
    {
        return [
            $this->days['monday']    => is_null($this->monday) ? null : json_decode($this->monday),
            $this->days['tuesday']   => is_null($this->tuesday) ? null : json_decode($this->tuesday),
            $this->days['wednesday'] => is_null($this->wednesday) ? null : json_decode($this->wednesday),
            $this->days['thursday']  => is_null($this->thursday) ? null : json_decode($this->thursday),
            $this->days['friday']    => is_null($this->friday) ? null : json_decode($this->friday),
            $this->days['saturday']  => is_null($this->saturday) ? null : json_decode($this->saturday),
            $this->days['sunday']    => is_null($this->sunday) ? null : json_decode($this->sunday),
        ];
    }

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @param bool $F
     * @param string $format
     * @param bool $d_index
     * @return array
     */
    public static function generateDateRange(Carbon $start_date, Carbon $end_date, $F = true, string $format = 'Y-m-d', bool $d_index = true): array
    {
        $dates = [];
        if ($F) {
            $dates[] = Date::parse($start_date)->format('F');
        }

        if ($d_index) {
            for ($date = $start_date; $date->lte($end_date); $date->addDay())
                $dates[$date->format('d')] = $date->format($format);
        }
        else {
            for ($date = $start_date; $date->lte($end_date); $date->addDay())
                $dates[] = $date->format($format);
        }

        return $dates;
    }

    /**
     * @param bool $F
     * @param bool $now
     * @return array
     */
    public static function getCurrentMonth($F = true, $now = true): array
    {
        $now ? $start = Carbon::now() : $start = new Carbon('first monday of this month');
        $end = Carbon::now()->endOfMonth();
        return self::generateDateRange($start, $end, $F);
    }

    /**
     * @param bool $F
     * @return array
     * @throws Exception
     */
    public static function getNextMonth($F = true): array
    {
        $start = new Carbon('first day of next month');
        $end = new Carbon('last day of next month');
        return self::generateDateRange($start, $end, $F);
    }

    /**
     * @param bool $F
     * @return array
     * @throws Exception
     */
    public static function getMonthLater($F = true): array
    {
        $start = new Carbon('first day of 2 months');
        $end = new Carbon('last day of 2 months');
        return self::generateDateRange($start, $end, $F);
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getThreeMonth(): array
    {
        return [self::getCurrentMonth(), self::getNextMonth(), self::getMonthLater()];
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getNextMonthBot(): array
    {
        $start = new Carbon('first day of next month');
        $end = new Carbon('last day of next month');
        if (!$start->isMonday()) {
            $start = self::getCurrentStart();
        }
        return self::generateDateRangeBot($start, $end);
    }

    /**
     * @param int $later
     * @return array
     * @throws Exception
     */
    public static function getMonthLaterBot($later = 2): array
    {
        $start = new Carbon('first day of ' . $later . ' months');
        $end = new Carbon('last day of ' . $later . ' months');
        if ($start->format('l') != 'monday') {
            $start = self::getCurrentStart($later - 1, $later);
        }
        return self::generateDateRangeBot($start, $end);
    }

    /**
     * @param string $start
     * @param string $end
     * @return DatePeriod
     */
    public static function getMondays($start = 'this', $end = 'next'): DatePeriod
    {
        return new DatePeriod(
            Carbon::parse('first monday of ' . $start . ' month'),
            CarbonInterval::week(),
            Carbon::parse('first monday of ' . $end . ' month')
        );
    }

    /**
     * @param string $start
     * @param string $end
     * @return \Date
     */
    public static function getCurrentStart($start = 'this', $end = 'next'): \Date
    {
        $mondays = self::getMondays($start, $end);
        $arr = [];
        foreach ($mondays as $monday)
            $arr[] = $monday;
        return \Date::parse($arr[count($arr) - 1]);
    }

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @return array
     */
    public static function generateDateRangeBot(Carbon $start_date, Carbon $end_date): array
    {
        $range = [];
        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $range[$date->format('Y-m-d')] = $date->format('d');
        }
        return $range;
    }

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @return array
     */
    public static function generateFullDateRange(Carbon $start_date, Carbon $end_date): array
    {
        $range = [];
        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $range[$date->format('Y-m-d')] = ['day' => $date->format('l'), 'number' => $date->format('d')];
        }
        return $range;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getCurrentMonthBot(): array
    {
        $start = new Carbon('first day of this month');
        $end = Carbon::now()->endOfMonth();
        return self::generateDateRangeBot($start, $end);
    }

    /**
     * @param string $month
     * @return array
     */
    public static function getDaysOfMonth(string $month = ''): array
    {
        $start = new Carbon('first day of ' . $month);
        $end = Carbon::parse($start->toDateString())->endOfMonth();

        return self::generateDateRangeBot($start, $end);
    }

    /**
     * @param string $month
     * @return array
     */
    public static function getFullDaysOfMonth(string $month = ''): array
    {
        $start = new Carbon('first day of ' . $month);
        $end = Carbon::parse($start->toDateString())->endOfMonth();

        return self::generateFullDateRange($start, $end);
    }

    /**
     * @param $model
     * @param string $Y_m_d
     * @return array|bool
     */
    public static function getBookedTimes($model, string $Y_m_d)
    {
        /** @var Record $records */
        $records = $model->records->where('date', $Y_m_d);

        if ($records->isEmpty()) {
            return false;
        }

        $times = [];
        $first = $records->first();


        if (isset($first->service->group)) {

            $needle = $first->service->group->quantity;
            $duplicates = $records->toBase()->countBy('time')->all();

            foreach ($duplicates as $time => $count) {
                if ($count >= $needle) {
                    $times[$time] = $first->service->interval->minutes + $first->service->range;
                }
            }
        }
        else {
            foreach ($records as $record) {
                $times[$record->time] = $record->service->interval->minutes + $record->service->range;
            }
        }

        //  в массиве время записи и длительность услуги в минутах
        return $times;
    }

    /**
     * @param array $timetable
     * @return array
     */
    public static function getChecked(array $timetable): array
    {
        $checked = [];

        foreach ($timetable as $day_of_week => $times) {
            foreach ($times as $time) {
                $checked[] = $day_of_week . '-' . $time;
            }
        }
        return $checked;
    }
}
