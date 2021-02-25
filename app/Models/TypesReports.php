<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TypesReports
 *
 * @property-read \App\Models\Report $report
 * @property-read \App\Models\TypeService $typeService
 * @method static \Illuminate\Database\Eloquent\Builder|TypesReports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypesReports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypesReports query()
 * @mixin \Eloquent
 */
class TypesReports extends Model
{
    use HasFactory;

    protected $table = 'types_reports';
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
    public function typeService ()
    {
        return $this->belongsTo(TypeService::class);
    }
}
