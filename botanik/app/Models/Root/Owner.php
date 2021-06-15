<?php

namespace App\Models\Root;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Root\Owner
 *
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Business $business
 * @method static Builder|Owner newModelQuery()
 * @method static Builder|Owner newQuery()
 * @method static Builder|Owner query()
 * @method static Builder|Owner whereCreatedAt($value)
 * @method static Builder|Owner whereEmail($value)
 * @method static Builder|Owner whereFio($value)
 * @method static Builder|Owner whereId($value)
 * @method static Builder|Owner wherePassword($value)
 * @method static Builder|Owner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Owner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function business (): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
