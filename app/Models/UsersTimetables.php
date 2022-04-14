<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class UsersTimetables
 * @package App\Models
 *
 * @property integer $id
 * @property integer $users_slots_id
 * @property integer $year
 * @property string $month
 * @property string $schedule
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UsersTimetables extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_timetables';

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

        'users_slots_id',

        'year',

        'month',

        'schedule',

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
        'schedule' => 'array'
    ];

    const RuleList = [

        'users_slots_id' => [],

        'year' => [],

        'month' => [],

        'schedule' => [],

    ];

    public function slot()
    {
        return $this->hasOne(UsersSlots::class);
    }


}
