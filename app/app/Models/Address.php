<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Address
 *
 * @property-read Collection|Notice[] $notices
 * @property-read int|null $notices_count
 * @property-read Collection|Record[] $records
 * @property-read int|null $records_count
 * @property-read Collection|Service[] $services
 * @property-read int|null $services_count
 * @property-read Collection|UserTimetable[] $timetables
 * @property-read int|null $timetables_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property-read Collection|VisitHistory[] $visitHistories
 * @property-read int|null $visit_histories_count
 * @method static Builder|Address newModelQuery()
 * @method static Builder|Address newQuery()
 * @method static Builder|Address query()
 * @mixin Eloquent
 */
class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'address'
    ];

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'services_addresses');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_addresses');
    }

    /**
     * @return HasMany
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(UserTimetable::class);
    }

    /**
     * @return HasMany
     */
    public function visitHistories(): HasMany
    {
        return $this->hasMany(VisitHistory::class);
    }

    /**
     * @return HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    /**
     * @return HasMany
     */
    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

}
