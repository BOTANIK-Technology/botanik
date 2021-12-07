<?php

namespace App\Models;

use App\Traits\Timetable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\ServiceTimetable
 *
 * @property-read Service $service
 * @method static Builder|ServiceTimetable newModelQuery()
 * @method static Builder|ServiceTimetable newQuery()
 * @method static Builder|ServiceTimetable query()
 * @mixin Eloquent
 */
class ServiceTimetable extends Model
{
    use HasFactory, Timetable;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @param Carbon $date
     * @param Carbon|null $comparison
     * @return bool
     */
    public function isWorkDay (Carbon $date, Carbon $comparison = null) : bool
    {
        if (!is_null($comparison) && $comparison->greaterThan($date))
            return false;

        $date = mb_strtolower($date->format('l'));

        if (isset($this->$date))
            return true;

        return false;
    }

    /**
     * @param string $date
     * @return array
     */
    public function getFreeTimes (string $date) : array
    {
        $check_hours = Carbon::parse($date)->isToday();
        $l = mb_strtolower(Carbon::parse($date)->format('l'));

        if (is_null($this->$l)) return [];
        else $times = json_decode($this->$l);

        $booked_array = self::getBookedTimes($this->service, $date);

        $free = [];
        $comparison = false;
        foreach ($times as $time) {

            if ($check_hours) {
                if (!Carbon::parse($time)->greaterThan(Carbon::now())) {
                    continue;
                }
            }

            if ($booked_array) {
                foreach ($booked_array as $booked) {
                    if ($booked == $time) {
                        if ($this->service->range > 0) {
                            $comparison = Carbon::parse($booked)->addMinutes($this->service->interval->minutes)->addMinutes($this->service->range);
                        } else {
                            $comparison = Carbon::parse($booked)->addMinutes($this->service->interval->minutes);
                        }
                        break;
                    }
                }
            }

            if ($comparison === false || Carbon::parse($time)->greaterThanOrEqualTo($comparison))
            {
                $free[] = $time;
            }

        }

        return $free;
    }

}
