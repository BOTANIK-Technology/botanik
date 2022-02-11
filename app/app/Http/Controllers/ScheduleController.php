<?php

namespace app\Http\Controllers;

use App\Helpers\DatesHelper;
use App\Facades\ConnectService;
use App\Jobs\SendNotice;
use App\Jobs\TelegramNotice;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\Telegram\TelegramAPI;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\UserTimetable;
use App\Models\TypeService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ScheduleController extends Controller
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
    public $view = 'schedule.page';

    /**
     * Set default params for view
     *
     * @param Request $request
     * @throws Exception
     */
    public function setParams(Request $request)
    {
        parent::setParams($request);
        $user = Auth::user();
        $this->params['slug'] = $request->route()->parameter('business');
        $this->params['months'] = UserTimetable::getMonths();

        $this->params['next_month'] = mb_strtolower(Carbon::parse($this->params['current_month'])->addMonth()->format('F'));
        $this->params['prev_month'] = mb_strtolower(Carbon::parse($this->params['current_month'])->subMonth()->format('F'));
        $this->params['current_day'] = $request->has('current_day') ? $request->input('current_day') : Carbon::now()->format('d');
        $this->params['days'] = UserTimetable::getDaysOfMonth($this->params['current_month']);
        $this->params['date'] = $request->has('date') ? $request->input('date') : Carbon::now()->format('Y-m-d');


        if (isset($request->modal)) {
            $this->params['modal'] = $request->modal;
        }

        if ($user->hasRole('admin', 'owner')) {
            $records = Record::whereDate('date', Carbon::parse($this->params['date']))
                ->whereHas('service', function ($q) use ($request) {
                    return $q->where('type_service_id', $request->current_type);
                })
                ->get();

            $this->params['times'] = UserTimetable::getHours();
            $this->params['types'] = TypeService::all();
            $this->params['current_type'] = $request->has('current_type') ? TypeService::findOrFail($request->current_type) : $this->params['types']->first();

            $this->params['services'] = !is_null($this->params['current_type']) ? $this->params['current_type']->services : [];
            $this->params['current_type'] = $this->params['current_type']->id ?? 0;
        }
        else {

            $s = [];
            $services = DB::table('users_services')->where('user_id', auth()->user()->id)->pluck('service_id')->toArray();
            foreach ($services as $service) {
                $s[] = $service;
            }

            $types = Service::where('id', $s)->pluck('type_service_id')->toArray();
            $schedule = UserTimetable::userSchedule($user, Carbon::parse($this->params['date']));
            $records = Record::where('user_id', $user->id)->whereDate('date', Carbon::parse($this->params['date']))->get();
            $this->params['types'] = $types;
            $this->params['current_type'] = $request->input('current_type') ?? $types[0];
            $this->params['schedule'] = $schedule['times'] ?? false;
            $this->params['address'] = $schedule['address'] ?? false;
        }


        $this->params['records'] = $records;

    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function getView(Request $request)
    {
        $this->setParams($request);
//        dd($this->params);
        return view($this->view, $this->params);
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function window(Request $request)
    {

        switch ($request->modal) {
            case 'create':
                $this->params['create_clients'] = TelegramUser::all();
                $this->params['create_services'] = Service::all();
                $this->params['create_users'] = User::all();
                $this->params['create_addresses'] = Address::all();
                $this->params['months'] = Address::all();
                break;
            case 'edit':
            case 'view':
            case 'delete':
                $this->params['window_records'] =
                    Record::whereDate('date', Carbon::parse($request->input('date')))
                        ->where('service_id', $request->id)
                        ->where('time', $request->time)
                        ->get();
                break;
        }
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse|false
     */
    public function createRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id'  => 'required|integer',
            'service_id' => 'required|integer',
            'address_id' => 'required|integer',
            'user_id'    => 'nullable|integer',
            'date'       => 'required|date',
            'time'       => 'required|string|min:4|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        if ($request->has('user_id')) {
            $user = User::findOrFail($request->user_id);
            $res = $user->canRecord($request->service_id, $request->address_id);
        }
        else {
            $service = Service::findOrFail($request->service_id);
            $res = $service->canRecord($request->address_id);
        }

        if ($res !== true) {
            return response()->json(['errors' => ['text' => $res]], 405);
        }


        try {

            $record = Record::create([
                'telegram_user_id' => $request->client_id,
                'service_id'       => $request->service_id,
                'address_id'       => $request->address_id,
                'user_id'          => $request->has('user_id') ? $request->user_id : null,
                'time'             => explode(':', $request->time)[0] . ":00",
                'date'             => Carbon::parse($request->date)->format('Y-m-d')
            ]);

            $service = Service::find($request->service_id);

            $pay = new Payment();
            $pay->status = 1;
            $pay->online_pay = 0;
            $pay->refund = Carbon::now()->addHours(3);
            $pay->money = $service->price;
            $record->payment()->save($pay);

        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        $client = TelegramUser::find($request->client_id);

        return TelegramAPI::createRecordNotice($service->name, $record, $client, $request);
        /*
                if (!ConnectService::prepareJob())
                    return false;

                try {

                    $notice_mess = __('Новая запись на услугу').' <b>'.$service->name.'</b> от '.$client->getFio().' на '.$request->date. ' в '.$request->time;
                    SendNotice::dispatch(
                        $request->business_db,
                        [
                            [
                                'address_id' => $request->address_id,
                                'message' => $notice_mess
                            ],
                            [
                                'user_id' => $request->user_id,
                                'message' => $notice_mess
                            ]
                        ]
                    )->delay(now()->addMinutes(2));

                    $time = Carbon::parse($request->getDate() . " " . $request->getTime());

                    $remind1Time = $time->subHours(config('memoBefore'));
                    $remind2Time = $time->subDay();

                    // Проверка на ночное время. Если уведомление выпадает на ночь - переносим на утро.
                    if ($remind2Time->hour >= config('params.nightBeginHour') ){
                        $remind2Time->setHour(config('params.nightBeginHour'))->setMinutes(0);
                    }
                    else if ($remind2Time->hour < config('params.nightEndHour')){
                        $remind2Time->subDay()->setHours(config('params.nightBeginHour'));
                    }
                    if ($remind2Time > Carbon::now()){
                        TelegramNotice::dispatch(
                            $request->business_db,
                            $client->chat_id,
                            $record->id,
                            __('Напоминание. Вы записаны на услугу').' "'.$service->name.'". Начало '. Carbon::parse($request->date)->format('d.m.Y') . ' в ' . $request->time,
                            $request->date,
                            $request->time,
                            $request->token
                        )->delay($time->subDay());
                    }

                    TelegramNotice::dispatch(
                        $request->business_db,
                        $client->chat_id,
                        $record->id,
                        __('Напоминание. Вы записаны на услугу').' "'.$service->name.'". Начало '. Carbon::parse($request->date)->format('d.m.Y') . ' в ' . $request->time,
                        $request->date,
                        $request->time,
                        $request->token
                    )->delay($time->subDay());



                    TelegramFeedBack::dispatch(
                        $request->business_db,
                        $client->chat_id,
                        $record->id,
                        $request->token
                    )->delay($time->addDay());

                    return response()->json(['ok' => 'Запись создана']);

                }
                catch (Exception $e) {
                    return response()->json(['errors' => ['server' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]], 500);
                }
        */
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSchedule(Request $request): JsonResponse
    {
        try {
            Record::find($request->id)->delete();
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
    public function editSchedule(Request $request): JsonResponse
    {
        try {
            $record = Record::find($request->id);
            $record->time = $request->time;
            $record->transfer = Carbon::parse($record->date)->format('Y-m-d');
            $record->date = Carbon::parse($request->date)->format('Y-m-d');
            $record->save();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        $client = TelegramUser::find($record->telegram_user_id);
        $service_name = TypeService::find($record->service->name);

        if (!ConnectService::prepareJob()) {
            return response()->json(['errors' => ['server' => __('Запись изменена. Уведомления не будут отправлены.')]], 500);
        }

        try {
            TelegramNotice::dispatch(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Внимание! Запись на услугу') . ' "' . $service_name . '" перенесена на ' . $request->date . ' в ' . $request->time,
                $request->date,
                $request->time,
                $request->token
            )->delay(now()->addMinutes(2));

            $notice_mess = __('Перенесена запись на услугу ') . ' <b>' . $service_name . '</b> от ' . $client->getFio() . ' на ' . $request->date . ' в ' . $request->time;
            SendNotice::dispatch(
                $request->business_db,
                [
                    [
                        'address_id' => $record->address_id,
                        'message'    => $notice_mess
                    ],
                    [
                        'user_id' => $record->user_id,
                        'message' => $notice_mess
                    ]
                ],
            )->delay(now()->addMinutes(2));

            return response()->json(['ok' => 'Запись изменена']);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAddresses(Request $request): JsonResponse
    {
        $addresses = [];
        $service_id = $request->service_id;
        if ($service_id) {
            $ids = Service::query()
                ->where('id', $service_id)
                ->join('services_addresses', 'services.id', '=', 'services_addresses.service_id')
                ->get('services_addresses.address_id')
                ->toArray();
            foreach ($ids as $item) {
                $addresses[] = Address::query()
                    ->where('id', $item['address_id'])
                    ->get(['id', 'address'])
                    ->toArray();
            }
        }
        else {
            $services = Service::all();
            foreach ($services as $service) {
                $ids = Service::query()
                    ->where('id', $service->id)
                    ->join('services_addresses', 'services.id', '=', 'services_addresses.service_id')
                    ->get('services_addresses.address_id')
                    ->toArray();
                foreach ($ids as $item) {
                    $addresses[$service->id][] = Address::query()
                        ->where('id', $item['address_id'])
                        ->get(['id', 'address'])
                        ->toArray();
                }
            }
        }
        return response()->json(["result" => "OK", "addresses" => $addresses]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getServices(Request $request): JsonResponse
    {
        $service_type_id = $request->service_type_id;
        $services = [];
        if ($service_type_id) {
            $services = Service::query()
                ->where('type_service_id', $service_type_id)
                ->select('id', 'name')
                ->get()
                ->toArray();

        }
        else {
            $types = TypeService::all();
            foreach ($types as $type) {
                $services[$type->id] = Service::query()
                    ->where('type_service_id', $type->id)
                    ->select('id', 'name')
                    ->get()
                    ->toArray();
            }
        }
        return response()->json(["result" => "OK", "services" => $services]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMasters(Request $request): JsonResponse
    {
        $masters = [];
        $service_id = $request->service_id;
        if ($service_id > 0) {
            $ids = Service::query()
                ->where('services.id', $service_id)
                ->join('users_slots', 'services.id', '=', 'users_slots.service_id')
                ->get('users_slots.user_id')
                ->toArray();
            foreach ($ids as $item) {
                $masters[] = User::query()
                    ->where('id', $item['user_id'])
                    ->get(['id', 'name'])
                    ->toArray();
            }
        }
        return response()->json(["result" => "OK", "masters" => $masters]);
    }

    public function getCalendar(Request $request)
    {
        $month = explode('_', $request->month);
        if ($month) {
            $month = $month[0];
        }
        $master_id = $request->master_id;
        $service_id = $request->service_id;
        $address_id = $request->address_id;

        $service = Service::findOrFail($service_id);
        $monthData = DatesHelper::masterDates($master_id, $service_id, $address_id, $month);
        return ['monthData' => $monthData, 'paymentTypes' => $service->paymentList];
    }

    public function getTimes(Request $request)
    {
        $date = substr($request->day, 5);
        $service_id = $request->service_id;
        $master_id = $request->master_id;
        $address_id = $request->address_id;


        $user = User::findOrFail($master_id);

        return DatesHelper::getFreeMasterTimes($user,  $address_id,  $service_id,  $date);

    }

}


//        return dd(Date::now()->format('l j F Y H:i:s'));
//        return dd(Date::now()->format('F'));
