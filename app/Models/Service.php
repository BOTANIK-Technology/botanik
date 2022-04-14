<?php

namespace App\Models;

use App\Traits\RelationHelper;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Service
 *
 * @property-read Collection|Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read Collection|Record[] $records
 * @property-read int|null $records_count
 * @property-read TypeService $typeServices
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Service newModelQuery()
 * @method static Builder|Service newQuery()
 * @method static Builder|Service query()
 * @property-read Interval $interval
 * @property-read ServiceTimetable|null $timetable
 * @property-read GroupService|null $group
 * @property-read Prepayment|null $prepayment
 * @property-read Collection|UserTimetable[] $userTimetables
 * @property-read int|null $user_timetables_count
 */
class Service extends Model
{
    use HasFactory, RelationHelper;

    /** @var array $fillable */
    protected $fillable = [
        'yclients_id',
        'beauty_id',
        'type_service_id',
        'name',
        'price',
        'cash_pay',
        'bonus_pay',
        'online_pay',
        'interval_id'
    ];

    protected $guarded = ['id'];

    protected $appends = ['intervalFields', 'rangeFields', 'paymentList'];

    public function getUserList()
    {
        $res = [];
        $slots = UsersSlots::where('service_id', $this->id)->without('services')->get();
        foreach ($slots as $slot) {
            $res[] = $slot->user()->without('service')->first();
        }
        return $res;
    }

    public function getPaymentListAttribute()
    {
        return [
            'cash_pay'   => $this->cash_pay,
            'bonus_pay'  => $this->bonus_pay,
            'online_pay' => $this->online_pay,
        ];
    }

    public function getIntervalFieldsAttribute()
    {
        return [
            'hours'   => $this->interval ? $this->interval->hoursField : 0,
            'minutes' => $this->interval ? $this->interval->minutesField : 0,
        ];
    }

    public function getRangeFieldsAttribute()
    {
        $range = $this->getRangeInterval();
        return [
            'hours'   => $range ? $range->hoursField : 0,
            'minutes' => $range ? $range->minutesField : 0,
        ];
    }

    public function getRangeInterval()
    {
        return Interval::where('minutes', $this->range)->first();
    }

    /**
     * @return BelongsToMany
     */
    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'services_addresses');
    }

    /**
     * @return BelongsTo
     */
    public function typeServices(): BelongsTo
    {
        return $this->belongsTo(TypeService::class, 'type_service_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function group(): HasOne
    {
        return $this->hasOne(GroupService::class);
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(TypeService::class, 'type_service_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function prepayment(): HasOne
    {
        return $this->hasOne(Prepayment::class);
    }

    public function isWorkDay(Carbon $date, Carbon $comparison = null): bool
    {
        if (!is_null($comparison) && $comparison->greaterThan($date)) {
            return false;
        }

        $date = $date->format('Y-m-d');
        $res = false;
        foreach ($this->timetables as $timetable) {
            if (in_array($date, array_keys($timetable->schedule))) {
                $res = true;
                continue;
            }
        }

        return $res;
    }

    public function getFreeTimes(string $date, $ignore_time = null): array
    {
        $check_hours = Carbon::parse($date)->isToday();
        $now = Carbon::now();



        // Расписание сервиса
        $year = Carbon::parse($date)->year;
        $month = strtolower( Carbon::parse($date)->format('F') );
        $timeSlot = $this->timetables()
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$timeSlot) {
            return [];
        }
        $times = $timeSlot->schedule[$date] ?? [];
        if (!$times) {
            return [];
        }

        // Список уже забронированных услуг с длительностью
        $booked_array = ServiceTimetable::getBookedTimes($this, $date);

        if (!$booked_array) {
            $booked_array = [];
        }

        // массив-карта слотов услуги
        $timeMap = [];
        $masterEndSlot = count($times) - 1;

        //сначала заполним единицами (доступно все)
        for ($i = 0; $i <= $masterEndSlot; $i++) {
            // если запись на сегодня - отбросим уже прошедшие слоты
            if ($check_hours && !Carbon::parse($times[$i])->greaterThanOrEqualTo($now)) {
                $timeMap[$i] = 0;
            }
            else {
                $timeMap[$i] = 1;
            }
        }

        // Длительность проверяемой услуги
        $duration = $this->interval->minutes + $this->range;

        // Число слотов по 30мин в проверяемой услуге
        $serviceSlotsCount = intdiv($duration, 30);
        if ($duration % 30) {
            $serviceSlotsCount++;
        }

        // Перебираем все созданные услуги и отмечаем недоступные из-них слоты
        foreach ($booked_array as $book => $bookDuration) {
            //Если прилетел слот для игнорирования (при правке даты уже созданной записи) - то мы его игнорируем
            if ($book == $ignore_time) {
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
        return $free;
    }


    /**
     * @return HasOne
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(ServiceTimetable::class);
    }


    /**
     * @return BelongsTo
     */
    public function interval(): BelongsTo
    {
        return $this->belongsTo(Interval::class);
    }


    /**
     * @return HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    /**
     * @param array $addresses
     */
    public function attachAddresses($addresses = []): void
    {
        $this->addresses()->attach($addresses);
    }

    /**
     * @param array $addresses
     */
    public function rewriteAddresses($addresses = []): void
    {
        $this->addresses()->detach($this->getIds('addresses'));
        $this->attachAddresses($addresses);
    }


    public function attachTimetable(array $timeTables = []): void
    {
        foreach ($timeTables as $year => $monthTable) {
            foreach ($monthTable as $month => $schedule) {
                if (!empty($schedule)) {
                    ServiceTimetable::create([
                        'service_id' => $this->id,
                        'year'       => $year,
                        'month'      => $month,
                        'schedule'   => $schedule
                    ]);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function updateTimetable(array $timetables = []): void
    {
        if ($this->timetables) {
            foreach ($this->timetables as $tableRecord) {
                $tableRecord->delete();
            }
        }
        $this->attachTimetable($timetables[0]);
    }

    /**
     * @param array $group
     */
    public function updateGroup($group = []): void
    {
        if (isset($this->group)) {
            $this->group()->update($group);
        }
        else {
            $this->group()->create($group);
        }
    }

    /**
     * @param array $prepayment
     */
    public function updatePrepayment($prepayment = []): void
    {
        if (isset($this->prepayment)) {
            $this->prepayment()->update($prepayment);
        }
        else {
            $this->prepayment()->create($prepayment);
        }
    }

    /**
     * @param array $prepayment
     */
    public function deletePrepayment(): void
    {
        $this->prepayment()->delete();
    }

    /**
     * @return bool|\Illuminate\Support\Collection
     */
    public static function withoutTimetable()
    {
        $services = self::all();
        if ($services->isEmpty()) {
            return false;
        }
        $collection = collect();
        foreach ($services as $service) {
            if (empty($service->timetable)) {
                $collection->add($service);
            }
        }
        return $collection;
    }

    /**
     * @param $address_id
     * @return bool|string
     */
    public function canRecord($address_id)
    {
        $addr = $this->addresses->where('id', $address_id);
        if (empty($addr)) {
            return __('Услуга') . ' "' . $this->name . '" ' . __('не привязана к выбранному адресу.');
        }
        return true;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getByYClientsId($id)
    {
        return self::query()
            ->where('yclients_id', '=', $id)
            ->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getByBeautyId($id)
    {
        return self::query()
            ->where('beauty_id', '=', $id)
            ->first();
    }

}
