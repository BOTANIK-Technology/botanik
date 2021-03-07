<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use App\Traits\RelationHelper;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use function Symfony\Component\Translation\t;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read Collection|FeedBack[] $feedBacks
 * @property-read int|null $feed_backs_count
 * @property-read Collection|Information[] $information
 * @property-read int|null $information_count
 * @property-read Collection|Notice[] $notices
 * @property-read int|null $notices_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|Record[] $records
 * @property-read int|null $records_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read Collection|Service[] $services
 * @property-read int|null $services_count
 * @property-read Collection|UserTimetable[] $timetables
 * @property-read int|null $timetables_count
 * @property-read Collection|VisitHistory[] $visitHistories
 * @property-read int|null $visit_histories_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions, RelationHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'created_by', 'updated_by', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function addresses (): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'users_addresses');
    }

    /**
     * @return BelongsToMany
     */
    public function services (): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'users_services');
    }

    /**
     * @return HasMany
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(UserTimetable::class);
    }

    /**
     * @return HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    /**
     * @return HasMany
     */
    public function visitHistories(): HasMany
    {
        return $this->hasMany(VisitHistory::class);
    }

    /**
     * @return HasMany
     */
    public function information(): HasMany
    {
        return $this->hasMany(Information::class);
    }

    /**
     * @return HasMany
     */
    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    /**
     * @return HasMany
     */
    public function feedBacks(): HasMany
    {
        return $this->hasMany(FeedBack::class);
    }

    /**
     * Count of profit amount
     *
     * @return int
     */
    public function profit(): int
    {
        if ( $this->records->isEmpty() )
            return 0;
        else {
            $now = \Carbon\Carbon::now();
            $profit = 0;
            foreach ($this->records as $record) {
                if ($record->status && $now->greaterThan($record->date.' '.$record->time))
                    $profit += $record->service->price;
            }
            return $profit;
        }
    }

    /**
     * Count of completed records
     *
     * @return int
     */

    public function completedRecords (): int
    {
        if ( $this->records->isEmpty() )
            return 0;
        else {
            $now = \Carbon\Carbon::now();
            $count = 0;
            foreach ($this->records as $record)
                if ($now->greaterThan($record->date.' '.$record->time))
                    $count++;
            return $count;
        }
    }

    /**
     * @param array $timetables
     * @param array $addresses
     * @param array $services
     */
    public function attachTimetables(array $timetables, array $addresses, array $services)
    {
        foreach ($timetables as $k => $timetable) {
            $table = new UserTimetable();
            foreach ($timetable as $day => $time)
                $table->$day = json_encode($time);
            $table->address_id = $addresses[$k];
            if (!empty($services))
                $table->service_id = $services[$k];
            $this->timetables()->save($table);
        }
    }

    /**
     * @param int $service_id
     * @param int $address_id
     * @return bool|string
     */
    public function canRecord (int $service_id, int $address_id)
    {
        $address = $this->addresses->where('id', $address_id)->first();
        if (empty($address))
            return __('Специалист').' '.$this->name.' '.__('не привязан к выбранному адресу.');

        $service = $this->services->where('id', $service_id)->first();
        if (empty($service))
            return __('Специалист').' '.$this->name.' '.__('не привязан к выбранной услуге.');

        $address = $service->addresses->where('id', $address_id)->first();
        if (empty($address))
            return __('Услуга').' "'.$service->name.'" '.__('не привязана к выбранному адресу.');

        return true;
    }

    /**
     * @param $services
     * @param $addresses
     * @return array|string
     */
    public static function relationServicesAddresses(array $services, array $addresses)
    {
        $array = [];
        foreach ($services as $k => $service_id) {

            try {
                $service = Service::find($service_id);
                if ($service->addresses->contains('id', $addresses[$k])) {
                    $array[] = $service->id;
                } else {
                    $address = Address::find($addresses[$k]);
                    return __('Адрес').' "'.$address->address.'"" '.__('не привязан к услуге').' "'.$service->name.'".';
                }
            } catch (\Exception $e) {
               return $e->getMessage().' '.$e->getLine();
            }
        }
        return $array;
    }

    /**
     * @return array|false
     */
    public function getArrayOfAddresses()
    {
        if ($this->addresses->isEmpty())
            return false;
        $array = [];
        foreach ($this->addresses as $addr)
            $array[$addr->id] = $addr->address;
        return $array;
    }

    public function attachCustom (string $relation, array $array, bool $rewrite = false)
    {
        $duplicates = collect($array)->toBase()->duplicates();
        if ($duplicates->count()) {
            foreach (array_keys($duplicates->toArray()) as $key) {
                unset($array[$key]);
            }
        }

        if ($rewrite)
            $this->$relation()->detach($this->getIds($relation));
        $this->$relation()->attach($array);
    }
}
