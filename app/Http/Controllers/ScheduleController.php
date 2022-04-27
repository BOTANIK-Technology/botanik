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
use App\Models\ServiceTimetable;
use App\Models\TelegramUser;
use App\Models\Timetables;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use function PHPUnit\Framework\isNan;

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
        $recordsQueue = Record::whereDate('date', Carbon::parse($this->params['date']));
        if ($request->current_type) {
            $recordsQueue->whereHas('service', function ($q) use ($request) {
                return $q->where('type_service_id', $request->current_type);
            });
        }

        if ($user->hasRole('admin', 'owner')) {


            $this->params['times'] = UserTimetable::getHours();
            $this->params['types'] = TypeService::all();

            $type = TypeService::find($request->current_type);

            $this->params['current_type'] = $request->input('current_type', 0);

            $this->params['services'] = $type->services ?? [];
        }
        else {

            $s = [];
            $services = DB::table('users_slots')->where('user_id', auth()->user()->id)->pluck('service_id')->toArray();
            foreach ($services as $service) {
                $s[] = $service;
            }

            $types = Service::where('id', $s)->pluck('type_service_id')->toArray();
            $schedule = UserTimetable::userSchedule($user, Carbon::parse($this->params['date']));
            $recordsQueue->where('user_id', $user->id);
            $this->params['types'] = $types;
            $this->params['current_type'] = $request->input('current_type') ?? $types[0];
            $this->params['schedule'] = $schedule['times'] ?? false;
            $this->params['address'] = $schedule['address'] ?? false;
        }


        $this->params['records'] = $recordsQueue->orderBy('time', 'ASC')->get();

    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function getView(Request $request)
    {
        $this->setParams($request);
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
                break;
            case 'edit':
            case 'view':
            case 'delete':

                $this->params['record'] =
                    Record::whereDate('date', Carbon::parse($request->input('date')))
                        ->where('service_id', $request->id)
                        ->where('time', $request->time)
                        ->first();
                $this->params['month'] = strtolower(Carbon::parse($this->params['record']->date)->format('F'));
                break;

        }
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createRecord(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'client_id'  => 'required|integer',
            'service_id' => 'required|integer',
            'address_id' => 'required|integer',
            'user_id'    => 'nullable|integer',
//            'pay_type'   => 'required|string',
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
        $date = Carbon::parse($request->date)->format('Y-m-d');
        try {
            $record = new Record();
            $record->fill([
                'telegram_user_id' => $request->client_id,
                'service_id'       => $request->service_id,
                'address_id'       => $request->address_id,
//                'pay_type'         => $request->pay_type,
                'user_id'          => $request->has('user_id') ? $request->user_id : null,
                'time'             => $request->time,
                'date'             => $date
            ]);
            if(Record::isTimeFree($request) ) {
                return response()->json(['errors' => ['text' => 'Запись на ' . $date . ' ' . $request->time . ' уже кем-то создана']], 400);
            }
            $record->save();

            $service = $record->service;

            $pay = new Payment();
            $pay->status = 1;
            $pay->online_pay = 0;
            $pay->refund = Carbon::now()->addHours(3);
            $pay->money = $service->price;
            $record->payment()->save($pay);

            $client = $record->telegramUser;

            TelegramNotice::dispatchSync(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Внимание! Вас записали на услугу') . ' "' . $record->service->name . '" ' . $record->date . ' в ' . $record->time,
                $request->date,
                $request->time,
                $request->token
            );

            return TelegramAPI::createRecordNotice($service->name, $record, $client, $request);

        }
        catch (Exception $e) {
            Log::error('**** create record error: ' . $e->getMessage());
            return response()->json(['errors' => ['server' => $e->getMessage()]], 200);
        }


    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSchedule(Request $request): JsonResponse
    {
        $record = Record::findOrFail($request->id);
        $client = $record->telegramUser;
        $service_name = $record->service->name;
        try {
            $record->delete();
            $notice_mess = __('Удалена запись на услугу ') . ' <b>' . $service_name . '</b> от ' . $client->getFio() . ' на ' . $record->date . ' в ' . $record->time;
            TelegramNotice::dispatchSync(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Внимание! Запись на услугу') . ' "' . $service_name . ' ' . $record->date . ' в ' . $record->time . ' удалена',
                $request->date,
                $request->time,
                $request->token
            );
            SendNotice::dispatchSync(
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
            );

            return response()->json(['ok' => 'Запись удалена']);

        }
        catch (Exception $e) {
            Log::error('**** delete record error: ' . $e->getMessage());
            return response()->json(['errors' => ['server' => $e->getMessage()]], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editSchedule(Request $request): JsonResponse
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');

        try {
            $record = Record::find($request->id);
            $record->time = $request->time;
            $record->transfer = $record->date . ' ' . $record->time;
            $record->date = $date;
            if(Record::isTimeFree($record) ) {
                return response()->json(['errors' => ['text' => 'Запись на ' . $date . ' ' . $request->time . ' уже кем-то создана']], 400);
            }
            $record->save();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        $client = $record->telegramUser;
        $service_name = $record->service->name;

        if (!ConnectService::prepareJob()) {
            return response()->json(['errors' => ['server' => __('Запись изменена. Уведомления не будут отправлены.')]], 500);
        }

        try {
            TelegramNotice::dispatchSync(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Внимание! Запись на услугу') . ' "' . $service_name . '" перенесена на ' . $request->date . ' в ' . $request->time,
                $request->date,
                $request->time,
                $request->token
            );

            $notice_mess = __('Перенесена запись на услугу ') . ' <b>' . $service_name . '</b> от ' . $client->getFio() . ' на ' . $request->date . ' в ' . $request->time;
            SendNotice::dispatchSync(
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
            );

            return response()->json(['ok' => 'Запись изменена']);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 200);
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
        $isEmpty = false;
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
            $isEmpty = ServiceTimetable::where('service_id', $service_id)->count() == 0;
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

        return response()->json(["result" => "OK", "addresses" => $addresses, 'is_empty' => $isEmpty]);
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
        if ($master_id) {
            $monthData = DatesHelper::masterDates($master_id, $service_id, $address_id, $month);
        }
        else {
            $monthData = DatesHelper::serviceDates($service_id, $address_id, $month);


        }
        return ['monthData' => $monthData, 'paymentTypes' => $service->paymentList];
    }

    public function getTimes(Request $request)
    {
        $date = $request->day;
        $service_id = $request->service_id;
        $master_id = $request->master_id;
        $address_id = $request->address_id;
        $ignoredTime = $request->mode == 'edit' ? $request->ignored_time : null;

        if ($master_id) {
            $user = User::findOrFail($master_id);
            return DatesHelper::getFreeMasterTimes($user, $address_id, $service_id, $date, $ignoredTime);
        }
        /** @var Service $service */
        $service = Service::findOrFail($service_id);

        return $service->getFreeTimes($date, $ignoredTime);
    }


    public function checkRecords(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $timeTable = $request->timetable;
        $service_id = $request->service_id;
        $master_id = $request->master_id;

        $dates = new Carbon();
        $dates->setYear($year);
        $dates->setMonth(Carbon::createFromFormat('F', strtoupper($month))->month);

        $firstDate = $dates->firstOfMonth();
        if ($firstDate->lessThan(Carbon::now())) {
            $firstDate = Carbon::now();
        }
        $lastDate = $dates->lastOfMonth();


        $records = Record::where('service_id', $service_id)
            ->where('user_id', $master_id)
            ->whereDate('date', '>=', $firstDate)
            ->whereDate('date', '<=', $lastDate)
            ->get();

        $times = $timeTable[$year][$month];
        $errors = [];
        foreach ($records as $record) {
            if (!$times || !$times[$record->date] || !in_array($record->time, $times[$record->date])) {
                $errors[] = $record->date . ' ' . $record->time . ' - ' . $record->telegramUser->getFio();
            }
        }

        if ($errors) {
            return response()->json(["result" => "Error", "errors" => $errors]);
        }
        return response()->json(["result" => "OK"]);
    }

}



