<?php

namespace App\Models;

use App\Traits\TimelineReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * App\Models\CatalogReport
 *
 * @property-read \App\Models\Catalog $catalog
 * @method static \Illuminate\Database\Eloquent\Builder|CatalogReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CatalogReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CatalogReport query()
 * @mixin \Eloquent
 */
class CatalogReport extends Model
{
    use HasFactory, TimelineReport;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function catalog ()
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * @param $reports
     * @return array
     */
    private function buildReportArray ($reports) : array
    {
        $collection = [
            'total' => 0,
            'sales' => 0,
            'visits' => 0,
            'reports' => []
        ];

        if (!$reports->isEmpty()) {

            foreach ($reports as $report) {
                $collection['total'] += $report->total;
                $collection['sales'] += $report->sales;
                $collection['visits'] += $report->visits;
                $collection['reports'][$report->catalog_id]['total'] = $report->total;
                $collection['reports'][$report->catalog_id]['sales'] = $report->sales;
                $collection['reports'][$report->catalog_id]['visits'] = $report->visits;
            }

        }

        return $collection;
    }

    /**
     * @param string $name
     * @param string $slug
     * @param array $data
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function buildExcel (string $name, string $slug, $data = []) : string
    {
        if (empty($data))
            return '';

        $fs = \Storage::disk('public');
        $fs->deleteDir($slug);

        $fs->makeDirectory('/'.$slug);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(20);

        $products = Catalog::all();

        $columns = Report::generateExelColumns(count($products));
        $last_column = array_pop($columns);
        $sheet->setCellValueExplicit('A2', 'Доход (₴)', 's');
        $sheet->setCellValueExplicit('A3', 'Заказы', 's');
        $sheet->setCellValueExplicit('A4', 'Посещения', 's');
        $row = 1;

        foreach ($products as $k => $product) {
            $sheet->setCellValueExplicit($columns[$k].$row, $product->title, 's');
            for ($j = 2; $j < 5; $j++) {
                switch ($j) {
                    case 2:
                        $sheet->setCellValue($columns[$k].$j, $data['reports'][$product->id]['total'] ?? 0);
                        break;
                    case 3:
                        $sheet->setCellValue($columns[$k].$j, $data['reports'][$product->id]['sales'] ?? 0);
                        break;
                    case 4:
                        $sheet->setCellValue($columns[$k].$j, $data['reports'][$product->id]['visits'] ?? 0);
                        break;
                }
            }
        }

        $sheet->setCellValueExplicit($last_column.'1', 'Все товары', 's');
        $sheet->setCellValue($last_column.'2', $data['total']);
        $sheet->setCellValue($last_column.'3', $data['sales']);
        $sheet->setCellValue($last_column.'4', $data['visits']);

        foreach ($columns as $column)
            $sheet->getColumnDimension($column)->setWidth(15);

        $path = $fs->path('/').$slug.'/'.$name.'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return strval($path);
    }
}
