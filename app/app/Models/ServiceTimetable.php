<?php

namespace App\Models;


use App\Traits\TimetableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use HasFactory, TimetableTrait;

    protected $table = 'services_timetables';
    protected $casts = [
        'schedule' => 'array'
    ];

    protected $fillable = [
        'service_id',
        'year',
        'month',
        'schedule'
    ];
}
