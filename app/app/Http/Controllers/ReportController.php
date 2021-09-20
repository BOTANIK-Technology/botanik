<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Catalog;
use App\Models\CatalogReport;
use App\Models\Report;
use App\Models\TypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Default params for view
     *
     * @var array
     */
    public $params = [];

    /**
     * View name
     *
     * @var string
     */
    public $view = 'report';


    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request) : void
    {
        $this->params['addresses_page'] = $request->addresses_page ?? $this->params['addresses_page'] = 1;
        $this->params['types_page'] = $request->types_page ?? $this->params['types_page'] = 1;
        $this->params['services'] = TypeService::select('type', 'id')->simplePaginate(6, ['*'], 'types_page');
        $this->params['addresses'] = Address::select('address', 'id')->simplePaginate(6, ['*'], 'addresses_page');
        isset($request->sort) ? $this->params['sort'] = $request->sort : $this->params['sort'] = 'today';

        $this->params['start_date'] = $request->start_date ?? '';
        $this->params['end_date']   = $request->end_date ?? '';

        $this->params['reports'] = $this->getReport($this->params['sort']);
        $this->params['types_page'] = $request->types_page ?? 1;
        $this->params['addresses_page'] = $request->addresses_page ?? 1;

        if($request->has('catalog') && $request->catalog) {
            $this->params['products'] = Catalog::select('title', 'id')->simplePaginate(6, ['*'], 'products_page');
            $this->params['products_page'] = $request->products_page ?? 1;
            $this->params['productsReports'] = $this->getReport($this->params['sort'], null, true);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getView (Request $request)
    {
        $this->setParams($request);
        return view($this->view, $this->params);
    }

    /**
     * @param string $sort
     * @param null $excel
     * @param bool $catalog
     * @return array|string
     */
    private function getReport (string $sort = '', $excel = null, $catalog = false)
    {
        if (!$catalog) {
            $report = new Report();
            $report->refreshReports();
        } else {
            $report = new CatalogReport();
        }

        switch ($sort) {
            case 'today':
                return $report->today($excel);
            case 'week':
                return $report->week($excel);
            case 'month':
                return $report->month($excel);
            case 'year':
                return $report->year($excel);
            case 'all':
                return $report->allTime($excel);
            case 'custom':
                return $report->customDate($this->params['start_date'], $this->params['end_date'], $excel);
            default:
                return [];
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index (Request $request)
    {
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download (Request $request)
    {
        $request->has('catalog_excel') ? $catalog = true : $catalog = false;
        $this->params['start_date'] = $request->start_date ?? '';
        $this->params['end_date']   = $request->end_date ?? '';

        $file_path = $this->getReport($request->sort, $request->business, $catalog);

        Log::debug("Закачка файлов: " . $file_path);

        if (file_exists($file_path)) {
            return \Response::download($file_path, $request->file, [
                'Content-Length: '. filesize($file_path)
            ]);
        }
        else {
            exit('Requested file does not exist on our server!');
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCatalog (Request $request)
    {
        $request->merge(['catalog_excel' => true]);
        return $this->download($request, true);
    }

}
