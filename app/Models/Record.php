<?php

namespace App\Models;

use App\Traits\JsonFieldTrait;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Record
 *
 * @property-read Address $address
 * @property-read Payment|null $payment
 * @property-read Service $service
 * @property-read TelegramUser $telegramUser
 * @property-read User $user
 * @property-read int|null $visit_histories_count
 * @method static Builder|Record newModelQuery()
 * @method static Builder|Record newQuery()
 * @method static Builder|Record query()
 * @mixin Eloquent
 */
class Record extends Model
{
    use HasFactory;


    protected $fillable = [
        'telegram_user_id',
        'service_id',
        'address_id',
        'user_id',
        'yclients_id',
        'pay_type',
        'beauty_id',
        'transfer',
        'status',
        'time',
        'date'
    ];

    protected $appends = [
        'finishTime'
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getService()
    {
        return Service::find($this->service_id);
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    /**
     * @return HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public static function isTimeFree($record)
    {
        return self::where('date', $record->date)
            ->where('time', $record->time)
            ->where('service_id', $record->service_id)
            ->where('address_id', $record->address_id)
            ->count();
    }

    public function getFinishTimeAttribute()
    {
        $minutes = $this->service->interval->minutes * 60 ;

        return Carbon::parse(strtotime($this->date . ' ' . $this->time) + $minutes)->setTimezone('Europe/Kiev')->format('H:i');

    }
}
