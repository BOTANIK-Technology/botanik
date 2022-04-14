<?php

namespace App\Models\Root;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Root\Package
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property-read Collection|Business[] $businesses
 * @property-read int|null $businesses_count
 * @method static Builder|Package newModelQuery()
 * @method static Builder|Package newQuery()
 * @method static Builder|Package query()
 * @method static Builder|Package whereId($value)
 * @method static Builder|Package whereName($value)
 * @method static Builder|Package whereSlug($value)
 * @mixin Eloquent
 */
class Package extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function businesses ()
    {
        return $this->hasMany(Business::class);
    }
}
