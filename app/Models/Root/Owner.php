<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Root\Owner
 *
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Root\Business $business
 * @method static \Illuminate\Database\Eloquent\Builder|Owner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereFio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Owner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business ()
    {
        return $this->belongsTo(Business::class);
    }
}
