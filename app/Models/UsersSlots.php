<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class UsersSlots
 * @package App\Models
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $service_id
 * @property integer $address_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UsersSlots extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_slots';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service_id',
        'address_id',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    const RuleList = [

        'service_id' => [],

        'address_id' => [],

    ];

    public function timetables(){
        return $this->hasMany(UsersTimetables::class);
    }

    public function service(){
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function address(){
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}