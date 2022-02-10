<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use App\Models\Notice;
use App\Models\Service;
use App\Models\Timetables;
use App\Models\TypeService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserTimetable;
use App\Models\Role;
use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class UserController extends Controller
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
    public $view = 'user.page';

    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        parent::setParams($request);
        $this->params['slug'] = $request->route()->parameter('business');
        isset($request->sort) ? $this->params['sort'] = $request->sort : $this->params['sort'] = 'master';
        isset($request->load) ? $this->params['load'] = $request->load : $this->params['load'] = 15;
        $this->params['types'] = TypeService::all();
        if ($this->params['sort'] == 'moder' && Auth::user()->hasRole('owner')) {
            $this->params['table'] = User::where('status', 0)->take($this->params['load'])->get();
            $this->params['countUsers'] = $this->params['table']->count();
        }
        else {
            $role = Role::where('slug', $this->params['sort'])->first();
            $this->params['countUsers'] = $role->users->count();
            $this->params['table'] = $role->users->take($this->params['load']);
        }

        if ($this->params['table']->isEmpty()) {
            $this->params['table'] = 0;
        }
        if (isset($request->modal)) {
            $this->params['modal'] = $request->modal;
        }
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
        $this->setParams($request);
        $modal = $request->modal;

        //TODO сделать нормальную проверку на неповторение услуги
//                $this->params['services'] = Service::withoutTimetable();

        $this->params['services'] = Service::all();
        $this->params['types_select'] = TypeService::all();
        $this->params['addresses'] = Address::all();

        switch ($modal) {
            case 'create':
                $this->params['roles'] = [
                    Role::where('slug', 'admin')->first(), Role::where('slug', 'master')->first()
                ];
                $this->params['moreService'] = $request->moreService ?? 1;
                break;

            case 'delete':
            case 'view':
                $this->params['user'] = User::find($request->id);
                break;

            case 'edit':
                $this->params['id'] = $request->id ?? 0;
                /** @var User $user */
                $user = User::find($this->params['id']);

                $this->params['user'] = $user;
                $this->params['moreService'] = $request->moreService ?? count($user->services);

                $this->setUserCookies($user);
                $this->setTimetableCookies($user);

                $this->params['user']['admin'] = $user->hasRole(Role::ROLE_ADMIN_SLUG) || $user->hasRole(Role::ROLE_OWNER_SLUG);

                break;

            case 'timetable':
                $this->params['id'] = $request->id ?? 0;
                /** @var User $user */
                $user = User::find($this->params['id']);
                $time = new UserTimetable();
                $this->params['times'] = $time->getHours();
                $this->params['days'] = $time->getDays();

                if($user) {
                    $this->setTimetableCookies($user);
                    $this->setUserCookies($user);
                }

                $this->params['user'] = $user;
                $this->params['moreService'] = $request->moreService ?? count($user->services);
                $this->params['currentService'] = intval($request->currentService);
                break;
            case 'note':
                break;
            default:
                abort(404);
        }


        return $this->index($request);

    }

    private function setUserCookies($user)
    {
        $this->params['userData'] = [];
        if ($user) {
            foreach ($user->slots as $slot) {
                $cookie = [
                    'service_type_id' => $slot->service->type_service_id,
                    'service_id'      => $slot->service_id,
                    'address_id'      => $slot->address_id,
                    'slot_id'      => $slot->id
                ];
                $this->params['userData'][] = $cookie;
            }
        }
    }

    /**
     * @param $user
     * @param $slug
     * @param bool $checked
     */
    private function setTimetableCookies($user)
    {
        $this->params['timetables'] = [];
        if (empty($user->slots)) {
            return;
        }

        $months = [];
        foreach ($user->slots as $key => $slot) {
            $timetable = [];
            foreach($slot->timetables as $item) {
                $timetable[$item->year][$item->month] = $item->schedule;
                $months[$key][] = $item->year . '-' . Timetables::getMonthList()[$item->month];
            }
            $this->params['timetables'][$key] = $timetable;
        }
        $this->params['usedMonths'] = $months;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        $validator = $this->validateUser($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $role = Role::where('slug', $request->role)->first();
        }
        catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'admin' => __('Выбранная роль не доступна.'), 'message' => $e->getMessage()
                ]
            ], 405);
        }

        $services = [];
        if ($role->slug == 'master') {
            $services = User::relationServicesAddresses($request->services, $request->addresses);
            if (!is_array($services)) {
                return response()->json(['errors' => ['admin' => $services]], 405);
            }
        }

        try {

            $root = \Auth::user();

            $array = [
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => bcrypt($request->password),
                'created_by' => $root->id,
                'updated_by' => $root->id,
            ];

            if ($root->hasRole('admin')) {
                $array['status'] = 0;
                Notice::sendNotice(
                    $request->input('business_db'),
                    [
                        [
                            'role_slug' => 'owner',
                            'message'   => __('Администратор') . ' ' . '<b>' . $root->name . '</b>' . ' ' . __('хочет добавить') . ' ' . $role->name . 'а <b>' . $array['name'] . '</b>',
                        ],
                    ]
                );
            }

            $user = User::create($array);
            $user->roles()->attach($role);

            if ($role->slug == 'master') {
                $user->attachCustom('services', $services);
            }

            $user->attachTimetables($request->timetables, $request->addresses, $services);
            $user->attachCustom('addresses', $request->addresses);

            if (!isset($array['status']) && \ConnectService::prepareJob()) {
                SendMail::dispatch(
                    $request->business,
                    $request->email,
                    $request->password,
                    $request->name,
                    $request->business_name
                )->delay(now()->addMinutes(2));
            }

            return response()->json(['ok' => true], 200);

        }
        catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editUser(Request $request): JsonResponse
    {
        $validator = $this->validateUser($request, false);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $role = Role::where('slug', $request->role)->first();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['admin' => __('Выбранная роль не доступна.')]], 405);
        }

        try {

            $root = \Auth::user();

            $array = [
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'updated_by' => $root->id,
            ];

            if (!empty($request->password)) {
                $array['password'] = bcrypt($request->password);
            }

            /** @var User $user */
            $user = User::updateOrCreate(
                ['id' => $request->id],
                $array
            );

            $user->roles()->detach($user->getIds('roles'));
            $user->roles()->attach($role);

            if ($role->slug == 'master') {
                $user->updateSlots( $request->services, $request->addresses, $request->timetables);
            }

//            $user->timetables()->delete();
//            $user->attachTimetables($request->timetables, $request->addresses, $services);
//
//            $user->attachCustom('addresses', $request->addresses, true);

            return response()->json(['ok' => true], 200);

        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

//    public function modalEditUser(Request $request)
//    {
//        $this->params['id'] = $request->id;
//        $this->params['user'] = User::find($this->params['id']);
//        $this->setTimetableCookies($this->params['user']);
//        $this->params['services'] = Service::withoutTimetable();
//        $this->params['addresses'] = Address::all();
//        $this->params['moreService'] = $request->moreService ?? count($this->params['user']->slots);
//
//        $this->setParams($request);
//        $html = view($this->view, $this->params)->render();
//        return response()->json(['html' => $html]);
//    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function addService(Request $request)
    {
        $this->params['services'] = Service::withoutTimetable();
        $this->params['addresses'] = Address::all();



        if ($request->modal == 'edit') {
            $user = User::find($request->id);
            $this->params['user'] = $user;
            $this->params['id'] = $user->id;
            $this->setTimetableCookies($user);
            $this->setUserCookies($user);
        }
        else {
            $this->params['roles'] = [Role::where('slug', 'admin')->first(), Role::where('slug', 'master')->first()];
        }
        $this->params['moreService'] = intval($request->moreService);

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public
    function deleteUser(Request $request): JsonResponse
    {

        try {

            $user = User::find($request->id);

            $root = \Auth::user();
            if ($root->hasRole('admin')) {
                $user->status = 0;
                $user->updated_by = $root->id;
                $user->save();

                Notice::sendNotice(
                    $request->input('business_db'),
                    [
                        [
                            'role_slug' => 'owner',
                            'message'   => __('Администратор') . ' ' . '<b>' . $root->name . '</b>' . ' ' . __('хочет удалить') . ' ' . $user->roles[0]->name . 'а <b>' . $user->name . '</b>',
                        ],
                    ]
                );
            }
            else {
                Notice::sendNotice(
                    $request->input('business_db'),
                    [
                        [
                            'user_id' => $user->updated_by,
                            'message' => __('Владелец сохранил') . ' ' . $user->roles[0]->name . 'а <b>' . $user->name . '</b>' . ' ' . __('в системе.'),
                        ],
                    ]
                );

                $user->delete();
            }

            return response()->json(['ok' => true], 200);

        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public
    function manageConfirm(Request $request)
    {
        $user = User::find($request->id);
        $user->status = 1;
        $user->save();

        Notice::sendNotice(
            $request->input('business_db'),
            [
                [
                    'user_id' => $user->updated_by,
                    'message' => __('Владелец сохранил ') . $user->roles[0]->name . 'а <b>' . $user->name . '</b>' . __('в системе.'),
                ],
            ]
        );

        if (\ConnectService::prepareJob()) {
            SendMail::dispatch(
                $request->business,
                $user->email,
                $user->password,
                $user->name,
                $request->business_name
            )->delay(now()->addMinutes(2));
        }

        return response()->redirectTo($request->business . '/users?sort=moder&load=5');
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public
    function manageReject(Request $request)
    {
        $this->deleteUser($request);
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public
    function getView(Request $request)
    {
        if (!isset($this->params['timetables'])) {
            $this->params['timetables'] = [];
        }
        $this->setParams($request);

        return view($this->view, $this->params);
    }

    /**
     * @param Request $request
     * @param bool $create
     * @return Validator
     */
    private
    function validateUser(Request $request, $create = true): Validator
    {
        $rules = [
            'name'       => 'required|string|min:1',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'required|email',
            'password'   => 'nullable|string|min:6|max:25',
            'role'       => 'required|string',
            'addresses'  => 'required|array',
            'services'   => 'nullable|array',
            'timetables' => 'required|array',
        ];

        if ($create) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6|max:25';
        }


        $validator = \Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request) {
            if (empty($request->addresses)) {
                $validator->errors()->add('addresses', __('Укажите адрес'));
            }
            else {
                foreach ($request->addresses as $address) {

                    if (empty($address)) {
                        $validator->errors()->add('address', __('Указаны не все адреса'));
                    }
                }
            }

            if ($request->role == 'master') {

                if (empty($request->services)) {
                    $validator->errors()->add('services', __('Укажите услугу'));
                }
                else {
                    foreach ($request->services as $address) {

                        {
                            if (empty($address)) {
                                $validator->errors()->add('service', __('Указаны не все услуги'));
                            }
                        }
                    }
                }

            }
            else {
                $duplicates = collect($request->addresses)->toBase()->duplicates();
                if ($duplicates->count()) {
                    $validator->errors()->add('addresses', __('Адреса не могут быть продублированы.'));
                }
            }


            if (empty($request->timetables)) {
                $validator->errors()->add('addresses', __('Укажите расписание'));
            }
            else {
                foreach ($request->addresses as $key => $item) {
                    if (empty($request->timetables[$key])) {
                        $validator->errors()->add('service', __('Указаны не все расписания'));
                    }
                }
            }

        });

        return $validator;
    }
}
