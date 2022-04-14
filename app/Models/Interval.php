<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Interval
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder|Interval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interval query()
 */
class Interval extends Model
{
    use HasFactory;

//    public function __construct(string $baseName = null)
//    {
//        if ($baseName){
//            $this->table = $baseName . '.intervals';
//        }
//        parent::__construct();
//    }

    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function services ()
    {
        return $this->hasMany(Service::class);
    }
}
