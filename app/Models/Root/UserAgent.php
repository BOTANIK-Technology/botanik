<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Root\UserAgent
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Root\Action[] $action
 * @property-read int|null $action_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent query()
 * @mixin \Eloquent
 */
class UserAgent extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function action ()
    {
        return $this->hasMany(Action::class);
    }
}
