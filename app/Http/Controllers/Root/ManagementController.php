<?php

namespace App\Http\Controllers\Root;

use App\Facades\ConnectService;
use App\Models\Root\Business;
use App\Models\Root\Package;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManagementController extends Controller
{
    /**
     * Default params for view
     *
     * @var array
     */
    public array $params = [];

    /**
     * View name
     *
     * @var string
     */
    public $view = 'root.management';


    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $this->params['countItems'] = Business::count();
        $this->params['load'] = $request->load ?? 5;
        $this->params['table'] = Business::take($this->params['load'])->get();
        if ( $this->params['table']->isEmpty() ) $this->params['table'] = false;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    private function getView(Request $request)
    {
        $this->setParams($request);

//        dd($this);
        return view($this->view, $this->params);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function window(Request $request)
    {
        $this->params['business'] = Business::find($request->id) ?? false;
        switch ($request->modal) {
            case 'chart':
                $this->params['chart'] = $this->params['business'] ? $this->params['business']->getChart() : false;
                break;
            default:
                $this->params['packages'] = Package::orderBy('id', 'DESC')->get() ?? false;
        }
        $this->params['modal'] = $request->modal;
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws ValidationException
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'business_name' => 'required|string|min:1|max:255',
            'bot_name'      => 'required|string|min:3|max:32',
            'package'       => 'required|integer',
            'tg_token'      => 'required|string|max:255',
            'pay_token'     => 'nullable|string|max:255',
            'logo'          => 'nullable|file|image',
            'catalog'       => 'required|boolean'
        ]);


        $business = Business::find($request->id);
        $business->update([
            'name'       => $request->input('business_name'),
            'bot_name'   => $request->input('bot_name'),
            'package_id' => $request->input('package'),
            'token'      => $request->input('tg_token'),
            'pay_token'  => $request->input('pay_token'),
            'catalog'    => $request->input('catalog')
        ]);

        $path = $request->has('logo') ? $request->file('logo')->store('logos', 'public') : false;
        if ($path)
            $business->changeLogo($path);

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function pause(Request $request): JsonResponse
    {
        $result = Business::changeStatus($request->id);
        if (is_array($result))
            return response()->json(['errors' => $result], 500);
        else
            return response()->json(['ok' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            Business::find($request->id)->delete();
            return response()->json(['ok' => true]);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['message' => $e->getMessage()]], 501);
        }
    }

    public function webhook (Request $request): JsonResponse
    {
        $business = Business::findOrFail($request->id);
        try {
            return response()->json(['response' => $business->setWebhook()]);
        } catch (GuzzleException $e) {
            return response()->json(['errors' => ['message' => $e->getMessage()]], 501);
        }
    }
}
