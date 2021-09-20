<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TelegramSession
 *
 * @property-read \App\Models\TelegramUser $telegramUser
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramSession query()
 * @mixin \Eloquent
 */
class TelegramSession extends Model
{
    use HasFactory;

    protected $fillable = ['telegram_user_id', 'type', 'service', 'address', 'master', 'record', 'date', 'time', 'stars', 'data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
