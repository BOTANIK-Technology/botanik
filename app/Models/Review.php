<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Review
 *
 * @property-read \App\Models\TelegramUser $telegramUsers
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @mixin \Eloquent
 */
class Review extends Model
{
    use HasFactory;
    protected $fillable = ['telegram_user_id', 'stars', 'text'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function telegramUsers()
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }

}
