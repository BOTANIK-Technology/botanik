<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VisitHistory
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Record $record
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\TelegramUser $telegramUser
 * @property-read \App\Models\TypeService $typeService
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|VisitHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitHistory query()
 * @mixin \Eloquent
 */
class VisitHistory extends Model
{

    protected $guarded = ['id'];

    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class);
    }

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
    public function typeService()
    {
        return $this->belongsTo(TypeService::class);
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
    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
