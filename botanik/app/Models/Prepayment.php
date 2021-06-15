<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Prepayment
 *
 * @property-read Service $service
 * @method static Builder|Prepayment newModelQuery()
 * @method static Builder|Prepayment newQuery()
 * @method static Builder|Prepayment query()
 * @mixin Eloquent
 */
class Prepayment extends Model
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
