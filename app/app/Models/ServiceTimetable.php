<?php

namespace App\Models;

use App\Traits\Timetable;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
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
    public function getFreeTimes(string $date,$ignore_time = null): array
    {
        $check_hours = Carbon::parse($date)->isToday();
        $now = Carbon::now();
        $weekDay = strtolower(Carbon::now()->locale('en')->dayName );


        // Расписание сервиса
        $times = json_decode($this->$weekDay, true);
        Log::info('Service times', [$weekDay, $times]);

        if (!$times) {
            return [];
        }


        // Список уже забронированных услуг с длительностью
        $booked_array = self::getBookedTimes($this->service, $date);

        if (!$booked_array) {
            $booked_array = [];
        }
        Log::info('Service Booked', $booked_array);
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
        $duration = $this->service->interval->minutes + $this->service->range;

        // Число слотов по 30мин в проверяемой услуге
        $serviceSlotsCount = intdiv($duration, 30);
        if ($duration % 30) {
            $serviceSlotsCount++;
        }
        Log::info('Service-servise: ', [$duration, $serviceSlotsCount]);

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
    /**
     * @param string $date
     * @return array
     */
//    public function getFreeTimes (string $date) : array
//    {
//        $check_hours = Carbon::parse($date)->isToday();
//        $l = mb_strtolower(Carbon::parse($date)->format('l'));
//
//        if (is_null($this->$l)) return [];
//        else $times = json_decode($this->$l);
//
//        $booked_array = self::getBookedTimes($this->service, $date);
//
//        $free = [];
//        $comparison = false;
//        foreach ($times as $time) {
//
//            if ($check_hours) {
//                if (!Carbon::parse($time)->greaterThan(Carbon::now())) {
//                    continue;
//                }
//            }
//
//            if ($booked_array) {
//                foreach ($booked_array as $booked) {
//                    if ($booked == $time) {
//                        if ($this->service->range > 0) {
//                            $comparison = Carbon::parse($booked)->addMinutes($this->service->interval->minutes)->addMinutes($this->service->range);
//                        } else {
//                            $comparison = Carbon::parse($booked)->addMinutes($this->service->interval->minutes);
//                        }
//                        break;
//                    }
//                }
//            }
//
//            if ($comparison === false || Carbon::parse($time)->greaterThanOrEqualTo($comparison))
//            {
//                $free[] = $time;
//            }
//
//        }
//
//        return $free;
//    }

}
