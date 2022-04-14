<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Root\Action
 *
 * @property int $id
 * @property string $ip
 * @property int|null $user_id
 * @property int|null $business_id
 * @property string $user_agent
 * @property string $location
 * @property int $button_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Root\ActionName $actionName
 * @property-read \App\Models\Root\IpAddress $ipAddress
 * @property-read \App\Models\Root\UserAgent $userAgent
 * @method static \Illuminate\Database\Eloquent\Builder|Action newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action query()
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereButtonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUserId($value)
 * @mixin \Eloquent
 */
class Action extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actionName ()
    {
        return $this->belongsTo(ActionName::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ipAddress ()
    {
        return $this->belongsTo(IpAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAgent ()
    {
        return $this->belongsTo(UserAgent::class);
    }
}
