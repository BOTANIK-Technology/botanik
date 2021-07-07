<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TypeService
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeedBack[] $feedBacks
 * @property-read int|null $feed_backs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder|TypeService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypeService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypeService query()
 * @mixin \Eloquent
 */
class TypeService extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'yclients_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedBacks()
    {
        return $this->hasMany(FeedBack::class);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getByYClientsId($id)
    {
        return self::query()
            ->where('yclients_id', '=', $id)
            ->first();
    }
}
