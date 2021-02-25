<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AddressesReports
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|AddressesReports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddressesReports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddressesReports query()
 * @mixin \Eloquent
 */
class AddressesReports extends Model
{
    use HasFactory;

    protected $table = 'addresses_reports';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report ()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address ()
    {
        return $this->belongsTo(Address::class);
    }
}
