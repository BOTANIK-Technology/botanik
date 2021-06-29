<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use \App\Traits\Timetable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Timetable
 *
 * @property-read Address $address
 * @property-read User $user
 * @method static Builder|UserTimetable newModelQuery()
 * @method static Builder|UserTimetable newQuery()
 * @method static Builder|UserTimetable query()
 * @mixin Eloquent
 * @property-read Service $service
 */
class UserTimetable extends Model
{

    use HasFactory, Timetable;

    protected $fillable = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'address_id',
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @param User $user
     * @param Carbon|null $date
     * @return false|array
     */
    public static function userSchedule (User $user, Carbon $date = null) {
        if (is_null($date))
            $date = Carbon::now()->format('l');
        else
            $date = $date->format('l');
        $date = mb_strtolower($date);
        if ($user->timetables) {
            $table = [];
            foreach ($user->timetables as $tab)
                if (isset($tab->$date)) {
                    $table['times'][] = json_decode($tab->$date);
                    $table['address'] = $tab->address->address;
                    $table['service'] = $tab->service->name;
                }
            if (empty($table)) return false;
            else return $table;
        }
        return false;
    }

    /**
     * @param User $user
     * @param int $address_id
     * @param int $service_id
     * @param Carbon $date
     * @param Carbon|null $comparison
     * @return bool
     */
    public static function isWorkDay (User $user, int $address_id, int $service_id, Carbon $date, Carbon $comparison = null) : bool
    {
        if (!is_null($comparison) && $comparison->greaterThan($date))
            return false;

        if (empty($user->timetables))
            return false;

        $date = mb_strtolower($date->format('l'));
        foreach ($user->timetables as $tab) {
            if ($tab->address_id == $address_id && $tab->service_id == $service_id) {
                if (isset($tab->$date)) return true;
                else return false;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @param string $date
     * @param int $address_id
     * @param int $service_id
     * @return array|bool
     */
    public static function getTimes (User $user, int $address_id, int $service_id, string $date)
    {
        $date = mb_strtolower(Carbon::parse($date)->format('l'));

        $table = $user->timetables->where('address_id', $address_id)->where('service_id', $service_id)->first();
        if ($table && !is_null($table->$date)) {
            return json_decode($table->$date);
        }
        return false;
    }

    /**
     * @param User $user
     * @param int $address_id
     * @param int $service_id
     * @param string $date
     * @return array
     */
    public static function getFreeTimes (User $user, int $address_id, int $service_id, string $date) : array
    {
        $check_hours = Carbon::parse($date)->isToday();

        $times = self::getTimes($user, $address_id, $service_id, $date);

        if (!$times) {

            return [];
        }



        $table = $user->timetables->where('address_id', $address_id)->where('service_id', $service_id)->first();

        $booked_array = self::getBookedTimes($user, $date);

        if (!$booked_array) {

            $booked_array = [];
        }

        $free = [];
        $comparison = false;
        foreach ($times as $time) {

            if ($check_hours) {
                if (!Carbon::parse($time)->greaterThan(Carbon::now())) {
                    continue;
                }
            }

            foreach ($booked_array as $booked) {
                if ($booked == $time) {
                    if ($table->service->range > 0) {
                        $comparison = Carbon::parse($booked)->add($table->service->interval->value)->addMinutes($table->service->range);
                    } else {
                        $comparison = Carbon::parse($booked)->add($table->service->interval->value);
                    }
                    break;
                }
            }

            if (!$comparison || Carbon::parse($time)->greaterThanOrEqualTo($comparison)) {
                $free[] = $time;
            }
        }

        return $free;
    }
}
