<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupService
 *
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder|GroupService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupService query()
 * @mixin \Eloquent
 */
class GroupService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service ()
    {
        return $this->belongsTo(Service::class);
    }
}
