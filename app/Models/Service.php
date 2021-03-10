<?php

namespace App\Models;

use App\Traits\RelationHelper;
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
 * @mixin Eloquent
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

    protected $guarded = ['id'];

    /**
     * @return BelongsToMany
     */
    public function addresses (): BelongsToMany
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
     * @return HasOne
     */
    public function prepayment(): HasOne
    {
        return $this->hasOne(Prepayment::class);
    }

    /**
     * @return HasOne
     */
    public function timetable (): HasOne
    {
        return $this->hasOne(ServiceTimetable::class);
    }

    /**
     * @return HasMany
     */
    public function userTimetables (): HasMany
    {
        return $this->hasMany(UserTimetable::class);
    }

    /**
     * @return BelongsTo
     */
    public function interval (): BelongsTo
    {
        return $this->belongsTo(Interval::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_services');
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
    public function attachAddresses($addresses = []) : void
    {
        $this->addresses()->attach($addresses);
    }

    /**
     * @param array $addresses
     */
    public function rewriteAddresses($addresses = []) : void
    {
        $this->addresses()->detach($this->getIds('addresses'));
        $this->attachAddresses($addresses);
    }

    /**
     * @param array $timetable
     */
    public function attachTimetable($timetable = []) : void
    {
        $table = new ServiceTimetable();
        foreach ($timetable as $day => $time)
            $table->$day = json_encode($time);
        $this->timetable()->save($table);
    }

    /**
     * @param array $timetable
     * @throws Exception
     */
    public function updateTimetable($timetable = []) : void
    {
        if (isset($this->timetable))
            $this->timetable->delete();
        $this->attachTimetable($timetable);
    }

    /**
     * @param array $group
     */
    public function updateGroup($group = []) : void
    {
        if (isset($this->group)) {
            $this->group()->update($group);
        } else {
            $this->group()->create($group);
        }
    }

    /**
     * @param array $prepayment
     */
    public function updatePrepayment ($prepayment = []) : void
    {
        if (isset($this->prepayment)) {
            $this->prepayment()->update($prepayment);
        } else {
            $this->prepayment()->create($prepayment);
        }
    }

    /**
     * @return bool|\Illuminate\Support\Collection
     */
    public static function withoutTimetable ()
    {
        $services = self::all();
        if ($services->isEmpty())
            return false;
        $collection = collect();
        foreach ($services as $service)
            if (empty($service->timetable))
                $collection->add($service);
        if ($collection->isEmpty())
            return false;
       return $collection;
    }

    /**
     * @param $address_id
     * @return bool|string
     */
    public function canRecord ($address_id)
    {
        $addr = $this->addresses->where('id', $address_id);
        if (empty($addr))
            return __('Услуга').' "'.$this->name.'" '.__('не привязана к выбранному адресу.');
        return true;
    }


}
