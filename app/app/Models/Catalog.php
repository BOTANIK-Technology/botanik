<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\Catalog
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CatalogReport[] $catalogReport
 * @property-read int|null $catalog_report_count
 * @method static \Illuminate\Database\Eloquent\Builder|Catalog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Catalog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Catalog query()
 * @mixin \Eloquent
 */
class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'yclients_id',
        'title',
        'text',
        'img',
        'price',
        'count',
        'article',
    ];

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function catalogReport ()
    {
        return $this->hasMany(CatalogReport::class);
    }

    public function writeToReport ($visit = false) : bool
    {
        try {
            $report = CatalogReport::whereDate('created_at', Carbon::today()->format('Y-m-d'))->first();
            if (!$report) {
                $report = new CatalogReport();
                if ($visit) {
                    $report->visits = 1;
                    $report->sales = 0;
                    $report->total = 0;
                } else {
                    $report->visits = 1;
                    $report->sales = 1;
                    $report->total = $this->price;
                    $this->count -= 1;
                    $this->save();
                }
                $this->catalogReport()->save($report);
            } else {
                if ($visit) {
                    $report->visits += 1;
                } else {
                    $report->sales += 1;
                    $report->total += $this->price;
                    $this->count -= 1;
                    $this->save();
                }
                $report->save();
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage().' *** createOrder method on line '. $e->getLine() .' in '.$e->getFile());
            return false;
        }

        return true;

    }
}
