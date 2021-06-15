<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Root\ActionName
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Root\Action[] $actions
 * @property-read int|null $actions_count
 * @method static \Illuminate\Database\Eloquent\Builder|ActionName newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionName newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionName query()
 * @mixin \Eloquent
 */
class ActionName extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions ()
    {
        return $this->hasMany(Action::class, 'id', 'action_name_id');
    }
}
