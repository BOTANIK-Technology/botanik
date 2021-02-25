<?php

namespace App\Models;

use App\Traits\TimelineReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * App\Models\Report
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AddressesReports[] $addressesReports
 * @property-read int|null $addresses_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TypesReports[] $typesReports
 * @property-read int|null $types_reports_count
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @mixin \Eloquent
 */
class Report extends Model
{
    use HasFactory, TimelineReport;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function typesReports ()
    {
        return $this->hasMany(TypesReports::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addressesReports ()
    {
        return $this->hasMany(AddressesReports::class);
    }

    /**
     * Refresh past "daily" reports.
     * Create reports of the past few days.
     * Create primary reports if table is empty.
     *
     * @return void
     */
    public function refreshReports() : void
    {

        if ($this->all()->count() == 0) {
            $first_record = Record::orderBy('created_at', 'asc')->first();
            if (!$first_record)
                return;
            $dates = UserTimetable::generateDateRange(Carbon::parse($first_record->created_at), Carbon::today(), false, 'Y-m-d', false);
        }

        else {
            $last_report = $this->orderByDesc('created_at')->first();
            $last_report_date = Carbon::parse($last_report->created_at);
            if($last_report_date->format('Y-m-d') == Carbon::today()->format('Y-m-d'))
                return;
            $dates = UserTimetable::generateDateRange($last_report_date->addDay(), Carbon::today(), false, 'Y-m-d', false);
        }

        foreach ($dates as $date) {

            $field = [];

            $records = Record::whereDate('created_at', $date)->get();
            if (!$records->isEmpty())
                $field = $this->getRecordData($field, $records, $date);

            $records = Record::where('status', 1)->where('date', $date)->get();
            if (!$records->isEmpty())
                $field = $this->getTotalData($field, $records, $date);

            $feeds = FeedBack::whereDate('created_at', $date)->get();
            if (!$feeds->isEmpty())
                $field = $this->getFeedbackData($field, $feeds, $date);

            if (empty($field))
                $this->createEmptyReport(Carbon::parse($date));

            else
                $this->createReports($field);

        }

        $reports = $this->whereColumn('created_at', 'updated_at')->get();
        if (!$reports->isEmpty()) {
            foreach ($reports as $report) {

                $field = [];

                $records = Record::whereDate('created_at', $report->created_at)->get();
                if (!$records->isEmpty())
                    $field = $this->getRecordData($field, $records, $report->created_at);

                $records = Record::where('status', 1)->where('date', $report->created_at)->get();
                if (!$records->isEmpty())
                    $field = $this->getTotalData($field, $records, $report->created_at);

                $feeds = FeedBack::whereDate('created_at', $report->created_at)->get();
                if (!$feeds->isEmpty())
                    $field = $this->getFeedbackData($field, $feeds, $report->created_at);

                if (empty($field))
                    $this->createEmptyReport(Carbon::parse($report->created_at));

                else
                    $this->createReports($field);

                $report->delete();
            }
        }

    }

    /**
     * Create empty reports if no records and reviews exist
     *
     * @param Carbon|null $date
     */
    private function createEmptyReport(Carbon $date = null)
    {
        $now = Carbon::now()->format('Y-m-d');
        if (is_null($date))
            $date = $now;

        $field = [
            'total' => 0,
            'records' => 0,
            'feeds' => 0,
            'updated_at' => $now,
            'created_at' => $date
        ];

        $report = $this->create($field);

        foreach (TypeService::all() as $type) {
            $typeReport = new TypesReports();
            $typeReport->total = 0;
            $typeReport->records = 0;
            $typeReport->feeds = 0;
            $typeReport->type_service_id = $type->id;
            $typeReport->created_at = $date;
            $typeReport->updated_at = $now;
            $report->typesReports()->save($typeReport);
        }

        foreach (Address::all() as $address) {
            $addressReport = new AddressesReports();
            $addressReport->total = 0;
            $addressReport->records = 0;
            $addressReport->address_id = $address->id;
            $addressReport->created_at = $date;
            $addressReport->updated_at = $now;
            $report->addressesReports()->save($addressReport);
        }
    }

    /**
     * @param array $field
     * @param $feeds
     * @param $date
     * @return array
     */
    private function getFeedbackData(array $field, $feeds, $date) : array
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        foreach ($feeds as $feed) {

            isset($field['types'][$feed->service->type_service_id][$date]['feeds']) ?
                $field['types'][$feed->service->type_service_id][$date]['feeds'] += 1 :
                $field['types'][$feed->service->type_service_id][$date]['feeds']  = 1;

        }
        return $field;
    }

    /**
     * @param array $field
     * @param $records
     * @param $date
     * @return array
     */
    private function getRecordData (array $field, $records, $date) : array
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        foreach ($records as $record) {

            isset($field['types'][$record->service->type_service_id][$date]['records']) ?
                $field['types'][$record->service->type_service_id][$date]['records'] += 1 :
                $field['types'][$record->service->type_service_id][$date]['records']  = 1;

            isset($field['addresses'][$record->address_id][$date]['records']) ?
                $field['addresses'][$record->address_id][$date]['records'] += 1 :
                $field['addresses'][$record->address_id][$date]['records']  = 1;
        }


        return $field;
    }

    /**
     * @param array $field
     * @param $records
     * @return array
     */
    private function getTotalData(array $field, $records, $date) : array
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        foreach ($records as $record) {

            isset($field['types'][$record->service->type_service_id][$date]['total']) ?
                $field['types'][$record->service->type_service_id][$date]['total'] += $record->service->price :
                $field['types'][$record->service->type_service_id][$date]['total']  = $record->service->price;

            isset($field['addresses'][$record->address_id][$date]['total']) ?
                $field['addresses'][$record->address_id][$date]['total'] += $record->service->price :
                $field['addresses'][$record->address_id][$date]['total']  = $record->service->price;
        }

        return $field;
    }

    /**
     * @param $field
     */
    private function createReports ($field)
    {
        $current_date = Carbon::now()->format('Y-m-d');
        $reports = [];

        foreach ($field['types'] as $type_id => $item) {
            foreach ($item as $date => $data) {

                if ( !$report = $this->where( 'created_at', Carbon::parse($date)->format('Y-m-d') )->first() ) {
                    $reports[] = $report = $this->create([
                        'total' => 0,
                        'records' => 0,
                        'feeds' => 0,
                        'created_at' => Carbon::parse($date)->format('Y-m-d'),
                        'updated_at' => $current_date
                    ]);
                }
                $typeReport = new TypesReports();
                $typeReport->total = $data['total'] ?? 0;
                $typeReport->records = $data['records'] ?? 0;
                $typeReport->feeds = $data['feeds'] ?? 0;
                $typeReport->created_at = Carbon::parse($date)->format('Y-m-d');
                $typeReport->updated_at = $current_date;
                $typeReport->type_service_id = $type_id;
                $report->typesReports()->save($typeReport);

            }
        }

        if (isset($field['addresses'])) {
            foreach ($field['addresses'] as $address_id => $item) {
                foreach ($item as $date => $data) {

                    if ( !$report = $this->where( 'created_at', Carbon::parse($date)->format('Y-m-d') )->first() ) {
                        $reports[] = $report = $this->create([
                            'total' => 0,
                            'records' => 0,
                            'feeds' => 0,
                            'created_at' => Carbon::parse($date)->format('Y-m-d'),
                            'updated_at' => $current_date
                        ]);
                    }
                    $addressReport = new AddressesReports();
                    $addressReport->total = $data['total'] ?? 0;
                    $addressReport->records = $data['records'] ?? 0;
                    $addressReport->address_id = $address_id;
                    $addressReport->created_at =  Carbon::parse($date)->format('Y-m-d');
                    $addressReport->updated_at = $current_date;
                    $report->addressesReports()->save($addressReport);
                }
            }
        }

        foreach ($reports as $report) {
            foreach ($report->typesReports as $typeReport) {
                $report->total += $typeReport->total;
                $report->records += $typeReport->records;
                $report->feeds += $typeReport->feeds;
            }
            $report->save();
        }

    }

    /**
     * Array with reports data for view.
     *
     * @param $reports
     * @return array
     */
    private function buildReportArray ($reports) : array
    {
        $collection = [
            'total' => 0,
            'records' => 0,
            'feeds' => 0,
            'typesReports' => [],
            'addressesReports' => []
        ];
        $typesReports = [];
        $addressesReports = [];

        if (!empty($reports)) {

            foreach ($reports as $report) {
                $collection['total'] += $report->total;
                $collection['records'] += $report->records;
                $collection['feeds'] += $report->feeds;

                foreach ($report->typesReports as $rep) {

                    isset($typesReports[$rep->type_service_id]['total']) ?
                        $typesReports[$rep->type_service_id]['total'] += $rep->total :
                        $typesReports[$rep->type_service_id]['total'] = $rep->total;

                    isset($typesReports[$rep->type_service_id]['records']) ?
                        $typesReports[$rep->type_service_id]['records'] += $rep->records :
                        $typesReports[$rep->type_service_id]['records'] = $rep->records;

                    isset($typesReports[$rep->type_service_id]['feeds']) ?
                        $typesReports[$rep->type_service_id]['feeds'] += $rep->feeds :
                        $typesReports[$rep->type_service_id]['feeds'] = $rep->feeds;

                }

                foreach ($report->addressesReports as $rep) {

                    isset($addressesReports[$rep->address_id]['total']) ?
                        $addressesReports[$rep->address_id]['total'] += $rep->total :
                        $addressesReports[$rep->address_id]['total'] = $rep->total;

                    isset($addressesReports[$rep->address_id]['records']) ?
                        $addressesReports[$rep->address_id]['records'] += $rep->records :
                        $addressesReports[$rep->address_id]['records'] = $rep->records;

                }
            }

        }

        $collection['typesReports'] = $typesReports;
        $collection['addressesReports'] = $addressesReports;
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

        $addresses = Address::all();
        $services = TypeService::all();

        $columns = $this->generateExelColumns(count($services));
        $last_column = array_pop($columns);
        $sheet->setCellValueExplicit('A2', 'Доход (₴)', 's');
        $sheet->setCellValueExplicit('A3', 'Активность записей', 's');
        $sheet->setCellValueExplicit('A4', 'Отзывы', 's');
        $row = 1;

        foreach ($services as $k => $service) {
            $sheet->setCellValueExplicit($columns[$k].$row, $service->type, 's');
            for ($j = 2; $j < 5; $j++) {
                switch ($j) {
                    case 2:
                        $sheet->setCellValue($columns[$k].$j, $data['typesReports'][$service->id]['total'] ?? 0);
                        break;
                    case 3:
                        $sheet->setCellValue($columns[$k].$j, $data['typesReports'][$service->id]['records'] ?? 0);
                        break;
                    case 4:
                        $sheet->setCellValue($columns[$k].$j, $data['typesReports'][$service->id]['feeds'] ?? 0);
                        break;
                }
            }
        }

        $sheet->setCellValueExplicit($last_column.'1', 'Все услуги', 's');
        $sheet->setCellValue($last_column.'2', $data['total']);
        $sheet->setCellValue($last_column.'3', $data['records']);
        $sheet->setCellValue($last_column.'4', $data['feeds']);


        foreach ($columns as $column)
            $sheet->getColumnDimension($column)->setWidth(15);

        $sheet->setCellValueExplicit('A7', 'Доход (₴)', 's');
        $sheet->setCellValueExplicit('A8', 'Активность записей', 's');
        $row = 6;

        $columns = $this->generateExelColumns(count($addresses));
        $last_column = array_pop($columns);
        foreach ($addresses as $k => $address) {
            $sheet->setCellValueExplicit($columns[$k].$row, $address->address, 's');
            for ($j = 7; $j < 9; $j++) {
                switch ($j) {
                    case 7:
                        $sheet->setCellValue($columns[$k].$j, $data['addressesReports'][$address->id]['total'] ?? 0);
                        break;
                    case 8:
                        $sheet->setCellValue($columns[$k].$j, $data['addressesReports'][$address->id]['records'] ?? 0);
                        break;
                }
            }
        }

        $sheet->setCellValueExplicit($last_column.'6', 'Все адреса', 's');
        $sheet->setCellValue($last_column.'7', $data['total']);
        $sheet->setCellValue($last_column.'8', $data['records']);

        foreach ($columns as $column)
            $sheet->getColumnDimension($column)->setWidth(15);

        $path = $fs->path('/').$slug.'/'.$name.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return strval($path);
    }

    /**
     * @param int $needle
     * @param int $start
     * @return array
     */
    public static function generateExelColumns (int $needle, int $start = 1) : array
    {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']; //26 chars

        if ($start != 0)
            $output = array_slice($columns, $start);
        else
            $output = $columns;

        $count = count($output);

        if ($needle < $count)
            return array_slice($output, 0, $needle+1);

        $needle = $needle + 1 - $count;
        $first_letter = 0;
        $second_letter = 0;
        for($i = 0; $i < $needle; $i++) {
            if ( ($i !== 0) && ($i % 25 == 0) ) { // 26 chars from 0 index = 25
                $first_letter++;
                $second_letter = 0;
            }
            $output[] = $columns[$first_letter].$columns[$second_letter];
            $second_letter++;
        }

        return $output;
    }

    /**
     * @return int
     */
    public static function allTimeTotal() : int
    {
        if (!$totals = self::select('total')->where('total', '>', '0')->get())
            return 0;

        $total = 0;
        foreach ($totals as $val)
            $total += $val->total;

        return $total;
    }

}
