<?php

namespace App\Models;

use App\Facades\ConnectService;
use App\Jobs\SendNotice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Collection\Collection;

/**
 * App\Models\Notice
 *
 * @property-read \App\Models\Address $addresses
 * @property-read \App\Models\Role $roles
 * @property-read \App\Models\User $users
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice query()
 * @mixin \Eloquent
 */
class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'seen',
        'user_id',
        'role_id',
        'address_id',
        'message',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addresses()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roles()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @param User $user
     *
     * @return Collection|false
     */
    public static function getNotice(User $user)
    {
        $notices = $user->notices->sortDesc();
        foreach ($user->roles as $role)
            $notices = $notices->merge(self::where('role_id', $role->id)->orderBy('id', 'desc')->get());

        if ($user->hasRole('admin'))
            foreach ($user->addresses as $address)
                $notices = $notices->merge(self::where('address_id', $address->id)->orderBy('id', 'desc')->get());

        return self::makeSeen($notices);
    }

    /**
     * @param array $notices
     * @return array|false
     */
    public static function makeSeen( $notices)
    {
        if ($notices->isEmpty())
        {
            return false;
        }
        foreach ($notices as $notice) {
            if ($notice->seen == false) {
                $notice->seen = true;
                $notice->save();
            }
        }
        return $notices;
    }

    /**
     * @param string $business_db
     * @param array $params
     * @param int $minutes
     */
    public static function sendNotice(string $business_db, array $params, $minutes = 1) : void
    {
        if (ConnectService::prepareJob()) {

            SendNotice::dispatch(
                $business_db,
                $params
            )->delay(now()->addMinutes($minutes));

            ConnectService::dbConnect($business_db);
        }
    }
}
