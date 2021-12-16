<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Models\ServiceAddress;
use App\Models\ServiceTimetable;
use App\Models\TypeService;
use App\Models\Address;
use App\Models\Service;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;


class ServiceController extends Controller
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
    public $view = 'service.page';


    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $this->params['countService'] = Service::count();
        $this->params['countTypes'] = TypeService::count();
        $this->params['countAddresses'] = Address::count();
        $this->params['types'] = TypeService::all();
        $this->params['services'] = Service::all();
        $this->params['addresses'] = Address::all();
        $this->params['view'] = $request->get('view', 'services');
        $this->params['load'] = $request->get('load', 10);
        $this->params['load_types'] = $request->get('load_types', 10);
        $this->params['load_addresses'] = $request->get('load_addresses', 10);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function getView(Request $request)
    {
        if (!isset($this->params['timetables'])) {
            $this->params['timetables'] = [];
        }
        if (!isset($this->params['checked'])) {
            $this->params['checked'] = [];
        }
        $this->setParams($request);

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
     * @return Factory|View|void
     */
    public function window(Request $request)
    {
        $id = $request->id ?? $request->service_id;
        $business = $request->business;
        $modal = $request->modal;

        $this->params['modal'] = $modal;
        switch ($modal) {
            case 'delete':
            case 'view':
            case 'edit':
            case 'create':
            case 'timetable':
                break;
            default:
                abort(404);
        }
        $serviceView = Service::find($id);
        $serviceRequest = Service::find($request->service_id);

        switch ($modal) {
            case 'delete':
                $this->params['view_service'] = Service::find($id);
                break;
            case 'timetable':
                $this->params['times'] = ServiceTimetable::getHours();
                $this->params['days'] = ServiceTimetable::getDays();
                $this->params['service_id'] = $request->service_id;

                if ($serviceRequest) {
                    $this->params['type_id'] = $serviceRequest->type_service_id;
                    $this->setTimetableCookies($serviceRequest, true);
                }
                break;
            case 'view':
                $this->params['view_service'] = $serviceView;

                if ($serviceView) {
                    $this->params['view_service_type'] = TypeService::find($serviceView->type_service_id);
                }
                break;
            case 'create':
            case 'edit':
                $this->params['types_select'] = TypeService::all();
                $this->params['addresses'] = Address::all();
                $this->params['view_service'] = $serviceView;

                if ($serviceView) {
                    $this->params['view_service_type'] = TypeService::find($serviceView->type_service_id);
                }

                if ($this->params['addresses']->isEmpty()) {
                    $this->params['addresses'] = 0;
                }

                if (!$request->input('no_cookie')) {
                    $this->setTimetableCookies($serviceView);
                }
                break;
        }

        return $this->getView($request);
    }


    private function setTimetableCookies($view_service, $checked = false)
    {
        if (!$view_service)
            return;

        if (!$view_service->timetable)
            return;

        $days = ServiceTimetable::getDaysEn();
        $timetable = [];
        foreach ($days as $day) {
            if (!is_null($view_service->timetable->$day)) {
                $timetable[$day] = json_decode($view_service->timetable->$day);
            }
        }

        switch ($checked) {
            case false:
                $this->params['timetables']['timetable-' . $view_service->id] = $timetable;
//                setcookie('timetable-'.$view_service->id, json_encode($timetable), ['samesite' => 'Lax', 'path' => '/'.$slug.'/services/']);
                break;
            case true:
                $this->params['checked']['checked-' . $view_service->id] = ServiceTimetable::getChecked($timetable);
//                setcookie('checked-'.$view_service->id, json_encode(ServiceTimetable::getChecked($timetable)), ['samesite' => 'Lax', 'path' => '/'.$slug.'/services/']);
                break;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addType(Request $request): JsonResponse
    {
        try {
            $type = TypeService::create(['type' => $request->service]);
            return response()->json(['id' => $type->id, 'type' => $type->type], 201);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAddress(Request $request): JsonResponse
    {
        try {
            $address = Address::create(['address' => $request->address]);
            return response()->json(['id' => $address->id, 'address' => $address->address], 201);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteService($business, $id, Request $request): JsonResponse
    {
        try {
            Service::find($id)->delete();
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = $this->validateService($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $service = new Service();
            $type = TypeService::find($request->input('type'));
            $service->name = $request->input('name');


            $service->price = $request->input('price');
            $service->bonus = $request->has('bonus') ? $request->input('bonus') : 0;
            $service->cash_pay = $request->input('cashpay');
            $service->online_pay = $request->input('onlinepay');
            $service->bonus_pay = $request->input('bonuspay');

            $service->interval_id = $this->getIntervalId($request, 'duration');
            $service->range = $this->getIntervalRange($request, 'interval');

            $type->services()->save($service);
            $service->attachAddresses($request->input('addresses'));

            if ($request->has('timetable'))
                $service->attachTimetable($request->input('timetable'));

            if ($request->prepay)
                $service->updatePrepayment(['card_number' => $request->input('prepay_card'), 'message' => $request->input('prepay_message')]);

            if (!empty($request->input('quantity')) && !empty($request->input('message')))
                $service->group()->create(['quantity' => $request->input('quantity'), 'message' => $request->input('message')]);

            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    public function getIntervalId($request, $type)
    {
        $intervalLength = $this->getIntervalRange($request, $type);
        $item = Interval::where('minutes', $intervalLength)->first();
        return $item ? $item->id : 1;

    }

    public function getIntervalRange($request, $type)
    {
        return $request->input($type . 'Minutes') + $request->input($type . 'Hours') * 60;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editService(Request $request): JsonResponse
    {
        $validator = $this->validateService($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }
//        if(!$this->extValidate($request->all())) {
//            return response()->json(['errors' => ["message" => 'Такой адрес уже добавлен']], 405);
//        }
        try {
            $service = Service::find($request->id);
            $service->type_service_id = $request->input('type');
            $service->name = $request->input('name');

            $service->interval_id = $this->getIntervalId($request, 'duration');
            $service->range = $this->getIntervalRange($request, 'interval');

            $service->price = $request->input('price');
            $service->bonus = $request->has('bonus') ? $request->input('bonus') : 0;
            $service->cash_pay = $request->input('cashpay');
            $service->online_pay = $request->input('onlinepay');
            $service->bonus_pay = $request->input('bonuspay');
            $service->save();
            $service->rewriteAddresses($request->input('addresses'));

            if ($request->has('timetable'))
                $service->updateTimetable($request->input('timetable'));

            if ($request->prepay)
                $service->updatePrepayment(['card_number' => $request->input('prepay_card'), 'message' => $request->input('prepay_message')]);

            if (!empty($request->input('quantity')) && !empty($request->input('message')))
                $service->updateGroup(['quantity' => $request->input('quantity'), 'message' => $request->input('message')]);
            else
                if (isset($service->group)) $service->group->delete();

            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage() . ' ' . $e->getLine()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeService(Request $request): JsonResponse
    {
        try {
            Service::find($request->id)->delete();
            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]]);
        }
    }

    /**
     * @param Request $request
     * @return Validator
     */
    private function validateService(Request $request)
    {
        $rules = [
            'price' => 'required|integer|min:1',
            'bonus' => 'nullable|integer|min:1',
            'type' => 'required|integer',
            'addresses' => 'required|array',
            'name' => 'required|string',
//            'interval'  => 'required|integer|min:0|max:24',
            'range' => 'integer|min:0',
            'message' => 'nullable|required_with:quantity|string',
            'quantity' => 'nullable|required_with:message|integer|min:2',
            'timetable' => 'nullable|array',
            'prepay' => 'required|boolean',
            'cashpay' => 'required|boolean',
            'onlinepay' => 'required|boolean',
            'bonuspay' => 'required|boolean',
        ];
        $data = $request->all();
        if ($data['prepay'] == true) {
            $rules['prepay_message'] = 'required|string';
            $rules['prepay_card'] = 'required|string';
        } else {
            $rules['prepay_message'] = 'nullable|string';
            $rules['prepay_card'] = 'nullable|string';
        }
        $validator = \Validator::make($data, $rules);

        //Проверяем на уникальность сочетания имени услуги и типа
        if ($request->is('*/add-service')) {
            $validator->after(function ($validator) use ($data) {
                if (Service::where('type_service_id', $data['type'])->where('name', $data['name'])->exists()) {
                    $validator->errors()->add('name', 'Такая услуга в выбранной категории уже есть');
                }
            });
        }
        return $validator;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function extValidate(array $data): bool
    {
        $count = ServiceAddress::query()->where('service_id', $data['service_id'])
            ->where('address_id', $data['ddress_id'])->count();
        if ($count > 0) return false;
    }

}
