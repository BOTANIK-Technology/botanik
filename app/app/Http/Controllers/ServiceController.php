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
use Illuminate\Validation\Validator;
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
        $this->params['types'] = TypeService::all();
        $this->params['services'] = Service::all();
        $this->params['addresses'] = Address::all();
        $this->params['view'] = $request->get('view') ?? 'services';
        $this->params['load'] = $request->get('load') ?? 5;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function getView (Request $request)
    {
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
    public function window (Request $request)
    {
        $this->params['modal'] = $request->modal;

        switch ($request->modal) {
            case 'delete':
            case 'view':
            case 'edit':
            case 'create':
            case 'timetable':
                break;
            default:
                abort(404);
        }

        switch ($request->modal) {
            case 'delete':
                $this->params['service_type'] = TypeService::find($request->id);
                break;
            case 'timetable':
                $this->params['times'] = ServiceTimetable::getHours();
                $this->params['days'] = ServiceTimetable::getDays();
                $this->params['service_id'] = $request->service_id ?? $this->params['service_id'] = null;
                $this->params['type_id'] = $request->type_id ?? $this->params['type_id'] = null;
                if (!is_null($this->params['service_id']))
                    $this->setTimetableCookies(Service::where('id', $request->service_id)->get(), $request->business, true);
                break;
            case 'create':
            case 'edit':
                $this->params['intervals'] = Interval::all();
                $this->params['types_select'] = TypeService::all();
                $this->params['addresses'] = Address::all();
                if ($this->params['addresses']->isEmpty()) $this->params['addresses'] = 0;
                break;
        }

        if ($request->modal == 'edit' || $request->modal == 'view') {
            $this->params['service_type'] = TypeService::find($request->id);
            $this->params['services'] = isset($this->params['service_type']->services) ?  $this->params['service_type']->services : false;
            if ($request->modal == 'edit')
                $this->setTimetableCookies($this->params['services'], $request->business);
        }

        return $this->getView($request);
    }


    private function setTimetableCookies($services, $slug, $checked = false)
    {
        if (!$services || $services->isEmpty())
            return;
        foreach ($services as $service) {

            if ( isset($_COOKIE['timetable-'.$service->id]) )
                continue;

            if (!$service->timetable)
                continue;

            $days = ServiceTimetable::getDaysEn();
            $timetable = [];
            foreach ($days as $day)
                if(!is_null($service->timetable->$day))
                    $timetable[$day] = json_decode($service->timetable->$day);

            switch ($checked) {
                case false:
                    setcookie('timetable-'.$service->id, json_encode($timetable), ['samesite' => 'Lax', 'path' => '/'.$slug.'/services/']);
                case true:
                    setcookie('checked-'.$service->id, json_encode(ServiceTimetable::getChecked($timetable)), ['samesite' => 'Lax', 'path' => '/'.$slug.'/services/']);
            }

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
        }
        catch (Exception $e) {
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
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteService(Request $request): JsonResponse
    {
        try {
            TypeService::find($request->id)->delete();
        }
        catch (Exception $e) {
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
            $type = TypeService::find( $request->input('type') );
            $service->name = $request->input('name');
            $service->interval_id = $request->input('interval');
            $service->range = $request->input('range');
            $service->price = $request->input('price');
            $service->bonus = $request->has('bonus') ? $request->input('bonus') : 0;
            $service->cash_pay = $request->input('cashpay');
            $service->online_pay = $request->input('onlinepay');
            $service->bonus_pay = $request->input('bonuspay');
            $type->services()->save($service);
            $service->attachAddresses($request->input('addresses'));

            if ($request->has('timetable'))
                $service->attachTimetable($request->input('timetable'));

            if ($request->prepay)
                $service->updatePrepayment(['card_number' => $request->input('prepay_card'), 'message' => $request->input('prepay_message')]);

            if (!empty($request->input('quantity')) &&  !empty($request->input('message')))
                $service->group()->create(['quantity' => $request->input('quantity'), 'message' => $request->input('message')]);

            return response()->json(['ok' => true]);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
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
            $service->interval_id = $request->input('interval');
            $service->range = $request->input('range');
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

            if (!empty($request->input('quantity')) &&  !empty($request->input('message')))
                $service->updateGroup(['quantity' => $request->input('quantity'), 'message' => $request->input('message')]);
            else
                if (isset($service->group)) $service->group->delete();

            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage().' '.$e->getLine()]], 500);
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
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]]);
        }
    }

    /**
     * @param Request $request
     * @return Validator
     */
    private function validateService(Request $request): Validator
    {
        return \Validator::make($request->all(), [
            'price'     => 'required|integer|min:1',
            'bonus'     => 'nullable|integer|min:1',
            'type'      => 'required|integer',
            'addresses' => 'required|array',
            'name'      => 'required|string',
            'interval'  => 'required|integer|min:0|max:24',
            'range'     => 'integer|min:0',
            'message'   => 'nullable|required_with:quantity|string',
            'quantity'  => 'nullable|required_with:message|integer|min:2',
            'timetable' => 'nullable|array',
            'prepay'    => 'required|boolean',
            'cashpay'   => 'required|boolean',
            'onlinepay' => 'required|boolean',
            'bonuspay'  => 'required|boolean',
            'prepay_message' => 'nullable|string',
            'prepay_card'    => 'nullable|string',

        ]);
    }

    /**
     * @param array $data
     * @return bool
     */
    private function extValidate(array $data): bool
    {
        $count = ServiceAddress::query()->where('service_id', $data['service_id'])
            ->where('address_id', $data['ddress_id'])->count();
        if($count > 0) return false;
    }

}
