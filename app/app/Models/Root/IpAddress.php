<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Root\IpAddress
 *
 * @property-read Collection|Action[] $action
 * @property-read int|null $action_count
 * @method static Builder|IpAddress newModelQuery()
 * @method static Builder|IpAddress newQuery()
 * @method static Builder|IpAddress query()
 * @mixin \Eloquent
 */
class IpAddress extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function action (): HasMany
    {
        return $this->hasMany(Action::class);
    }
}
