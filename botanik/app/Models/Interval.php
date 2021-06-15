<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Interval
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder|Interval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interval query()
 * @mixin \Eloquent
 */
class Interval extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services ()
    {
        return $this->hasMany(Service::class);
    }
}
