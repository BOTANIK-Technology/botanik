<?php

namespace App\Models;

use App\Traits\TimetableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Timetables
 * @package App\Models
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $service_id
 * @property integer $address_id
 * @property integer $year
 * @property string $month
 *  * @property string $schedule
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Timetables extends Model
{

    use HasFactory, TimetableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timetables';

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
        'user_id' => [],

        'service_id' => [],

        'address_id' => [],

        'year' => [],

        'month' => [],
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }




}
