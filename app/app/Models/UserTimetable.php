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
use function PHPUnit\Framework\greaterThanOrEqual;

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
        'service_id',
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
    public static function userSchedule(User $user, Carbon $date = null)
    {
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
    public static function isWorkDay(User $user, int $address_id, int $service_id, Carbon $date, Carbon $comparison = null): bool
    {
//        Log::info('isWorkDay', [$date, $comparison]);
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
    public static function getTimes(User $user, int $address_id, int $service_id, string $date)
    {
        $date = mb_strtolower(Carbon::parse($date)->format('l'));

        $table = $user->timetables->where('address_id', $address_id)->where('service_id', $service_id)->first();

        Log::info('getTimes: ', $table->toArray());
        $times = false;
        if ($table && !is_null($table->$date)) {
            $times = json_decode($table->$date, true);
        }
        return $times;
    }

    /**
     * @param User $user
     * @param int $address_id
     * @param int $service_id
     * @param string $date
     * @return array
     */
    public static function getFreeTimes(User $user, int $address_id, int $service_id, string $date,$ignore_time = null): array
    {
        $check_hours = Carbon::parse($date)->isToday();
        $now = Carbon::now();


        // Расписание мастера
        $times = self::getTimes($user, $address_id, $service_id, $date);

        if (!$times) {

            return [];
        }


        // параметры услуги
        $table = $user->timetables->where('address_id', $address_id)->where('service_id', $service_id)->first();

        // Список уже забронированных услуг с длительностью
        $booked_array = self::getBookedTimes($user, $date);

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
        $duration = $table->service->interval->minutes + $table->service->range;

        // Число слотов по 30мин в проверяемой услуге
        $serviceSlotsCount = intdiv($duration, 30);
        if ($duration % 30) {
            $serviceSlotsCount++;
        }
Log::info('User-servise: ', [$duration, $serviceSlotsCount]);


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

Log::info('booked: ', [$bookDuration, $bookSlotsCount]);
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
}
