<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use App\Traits\RelationHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeedBack[] $feedBacks
 * @property-read int|null $feed_backs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Information[] $information
 * @property-read int|null $information_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notice[] $notices
 * @property-read int|null $notices_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Record[] $records
 * @property-read int|null $records_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTimetable[] $timetables
 * @property-read int|null $timetables_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VisitHistory[] $visitHistories
 * @property-read int|null $visit_histories_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addresses ()
    {
        return $this->belongsToMany(Address::class, 'users_addresses');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services ()
    {
        return $this->belongsToMany(Service::class, 'users_services');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timetables()
    {
        return $this->hasMany(UserTimetable::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visitHistories()
    {
        return $this->hasMany(VisitHistory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function information()
    {
        return $this->hasMany(Information::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notices()
    {
        return $this->hasMany(Notice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedBacks()
    {
        return $this->hasMany(FeedBack::class);
    }

    /**
     * Count of profit amount
     *
     * @return int
     */
    public function profit()
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

    public function completedRecords ()
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
