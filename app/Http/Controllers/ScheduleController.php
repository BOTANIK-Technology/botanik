<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotice;
use App\Jobs\TelegramFeedBack;
use App\Jobs\TelegramNotice;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramUser;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use ConnectService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\UserTimetable;
use App\Models\TypeService;
use Exception;
use Illuminate\View\View;
use Validator;

class ScheduleController extends Controller
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
    public $view = 'schedule.page';

    /**
     * Set default params for view
     *
     * @param Request $request
     * @throws Exception
     */
    public function setParams(Request $request)
    {
        $user = Auth::user();
        $this->params['slug'] = $request->route()->parameter('business');
        $this->params['mouths'] = UserTimetable::getMonths();
        $this->params['current_month'] = $request->has('current_month') ? $request->input('current_month') : mb_strtolower(Carbon::now()->format('F'));
        $this->params['next_month'] = mb_strtolower( Carbon::parse($this->params['current_month'])->addMonth()->format('F') );
        $this->params['prev_month'] = mb_strtolower( Carbon::parse($this->params['current_month'])->subMonth()->format('F') );
        $this->params['current_day'] = $request->has('current_day') ? $request->input('current_day') : Carbon::now()->format('d');
        $this->params['days'] = UserTimetable::getDaysOfMonth($this->params['current_month']);
        $this->params['date'] = $request->has('date') ? $request->input('date') : Carbon::now()->format('Y-m-d');

        if (isset($request->modal)) $this->params['modal'] = $request->modal;

        if ($user->hasRole('admin', 'owner')) {
            $records = (new Record)->whereDate('date', Carbon::parse($this->params['date']))->get();
            $this->params['times'] = UserTimetable::getHours();
            $this->params['types'] = TypeService::all();
            $this->params['current_type'] = $request->has('current_type') ? TypeService::findOrFail($request->current_type) : $this->params['types']->first();
            $this->params['services'] = $this->params['current_type']->services;
            $this->params['current_type'] = $this->params['current_type']->id;
        } else {
            $schedule = UserTimetable::userSchedule($user, Carbon::parse($this->params['date']));
            $records = (new Record)->where('user_id', $user->id)->whereDate( 'date', Carbon::parse($this->params['date']) )->get();
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
    public function window (Request $request)
    {
        switch ($request->modal) {
            case 'create':
                $this->params['create_clients'] = TelegramUser::all();
                $this->params['create_services'] = Service::all();
                $this->params['create_users'] = User::all();
                $this->params['create_addresses'] = Address::all();
                break;
            case 'view':
            case 'edit':
            case 'delete':
                $this->params['id'] = $request->id;
                $this->params['record'] = Record::find($request->id);
                break;
        }
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse|false
     */
    public function createRecord (Request $request)
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
        } else {
            $service = Service::findOrFail($request->service_id);
            $res = $service->canRecord($request->address_id);
        }

        if ($res !== true)
            return response()->json(['errors' => ['text' => $res]], 405);


        try {

            $record = Record::create([
                'telegram_user_id' => $request->client_id,
                'service_id' => $request->service_id,
                'address_id' => $request->address_id,
                'user_id' => $request->has('user_id') ? $request->user_id: null,
                'time' => $request->time,
                'date' => Carbon::parse($request->date)->format('Y-m-d')
            ]);

            $service = Service::find($request->service_id);

            $pay = new Payment();
            $pay->status = 1;
            $pay->online_pay = 0;
            $pay->refund = Carbon::now()->addHours(3);
            $pay->money = $service->price;
            $record->payment()->save($pay);

        } catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        $client = TelegramUser::find($request->client_id);

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
                ],
            )->delay(now()->addMinutes(2));

            TelegramNotice::dispatch(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Напоминание. Сегодня Вы записаны на услугу').' "'.$service->name.'". Начало в '.$request->time,
                $request->date,
                $request->time,
                $request->token
            )->delay(Carbon::parse($request->date.' '.$request->time)->subHour());


            TelegramFeedBack::dispatch(
                $request->business_db,
                $client->chat_id,
                $record->id,
                $request->token
            )->delay(Carbon::parse($request->date.' '.$request->time)->addDay());

            return response()->json(['ok' => 'Запись создана']);

        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSchedule (Request $request): JsonResponse
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
    public function editSchedule (Request $request): JsonResponse
    {
        try {
            $record = Record::find($request->id);
            $record->time = $request->time;
            $record->transfer = Carbon::parse($record->date)->format('Y-m-d');
            $record->date = Carbon::parse($request->changeDate)->format('Y-m-d');
            $record->save();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }

        $client = TelegramUser::find($record->telegram_user_id);
        $type_service = TypeService::find($record->service->type_service_id);

        if (!ConnectService::prepareJob())
            return response()->json(['errors' => ['server' => __('Запись изменена. Уведомления не будут отправлены.')]], 500);

        try {
            TelegramNotice::dispatch(
                $request->business_db,
                $client->chat_id,
                $record->id,
                __('Внимание! Запись на услугу').' "'.$type_service->type.'" перенесена на '.$request->date.' в '.$request->time,
                $request->date,
                $request->time,
                $request->token
            )->delay(now()->addMinutes(2));

            $notice_mess = __('Перенесена запись на услугу ').' <b>'.$type_service->type.'</b> от '.$client->getFio().' на '.$request->date. ' в '.$request->time;
            SendNotice::dispatch(
                $request->business_db,
                [
                    [
                        'address_id' => $record->address_id,
                        'message' => $notice_mess
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
}


//        return dd(Date::now()->format('l j F Y H:i:s'));
//        return dd(Date::now()->format('F'));
