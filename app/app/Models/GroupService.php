<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\GroupService
 *
 * @property-read Service $service
 * @method static Builder|GroupService newModelQuery()
 * @method static Builder|GroupService newQuery()
 * @method static Builder|GroupService query()
 * @mixin Eloquent
 */
class GroupService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function service (): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
