<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Record
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\TelegramUser $telegramUser
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VisitHistory[] $visitHistories
 * @property-read int|null $visit_histories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record query()
 * @mixin \Eloquent
 */
class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id', 'service_id', 'address_id', 'user_id', 'transfer', 'status', 'time', 'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visitHistories()
    {
        return $this->hasMany(VisitHistory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
