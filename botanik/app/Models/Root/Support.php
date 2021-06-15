<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Root\Support
 *
 * @property int $id
 * @property int $user_id
 * @property int $business_id
 * @property string $title
 * @property string $text
 * @property string|null $img
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Support newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support query()
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Support whereUserId($value)
 * @mixin \Eloquent
 */
class Support extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}
