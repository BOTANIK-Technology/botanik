<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use App\Models\Notice;
use App\Models\Service;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserTimetable;
use App\Models\Role;
use App\Models\Address;
use Exception;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class UserController extends Controller
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
    public $view = 'user.page';

    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $this->params['slug'] = $request->route()->parameter('business');
        isset($request->sort) ? $this->params['sort'] = $request->sort : $this->params['sort'] = 'master';
        isset($request->load) ? $this->params['load'] = $request->load : $this->params['load'] = 5;
        if ($this->params['sort'] == 'moder' && \Auth::user()->hasRole('owner')) {
            $this->params['table'] = User::where('status', 0)->take($this->params['load'])->get();
            $this->params['countUsers'] = $this->params['table']->count();
        }
        else {
            $role = Role::where('slug', $this->params['sort'])->first();
            $this->params['countUsers'] = $role->users->count();
            $this->params['table'] = $role->users->take($this->params['load']);
        }
        if ($this->params['table']->isEmpty()) $this->params['table'] = 0;
        if (isset($request->modal)) $this->params['modal'] = $request->modal;
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

        switch ($modal) {
            case 'create':
                $this->params['services'] = Service::withoutTimetable() ?? 0;
                $this->params['addresses'] = Address::all() ?? 0;
                $this->params['roles'] = [Role::where('slug', 'admin')->first(), Role::where('slug', 'master')->first()];
                $this->params['moreService'] = $request->moreService ?? 1;
                break;
            case 'delete':
            case 'view':
                $this->params['user'] = User::find($request->id);
                break;
            case 'edit':
                $this->params['id'] = $request->id;
                $this->params['user'] = User::find($this->params['id']);
                $this->setTimetableCookies($this->params['user'], $request->business);

                $this->params['services'] = Service::withoutTimetable();
                $this->params['addresses'] = Address::all();
                $this->params['moreService'] = $request->moreService ?? count($this->params['user']->addresses);
                break;
            case 'timetable':
                $time = new UserTimetable();
                $this->params['times'] = $time->getHours();
                $this->params['days'] = $time->getDays();
                $this->params['id'] = $request->id ?? null;
                if (!is_null($this->params['id']))
                    $this->setTimetableCookies(User::find($this->params['id']), $request->business, true);
                $this->params['moreService'] = intval($request->moreService);
                $this->params['currentService'] = intval($request->currentService);
                break;
            case 'note':
                break;
            default:
                abort(404);
        }

        return $this->index($request);

    }

    /**
     * @param $user
     * @param $slug
     * @param bool $checked
     */
    private function setTimetableCookies($user, $slug, $checked = false)
    {
        $this->params['timetables'] = [];
        if (empty($user->timetables))
            return;

        $days = UserTimetable::getDaysEn();
        foreach ($user->timetables as $k => $timetable) {

//            if (isset($_COOKIE['timetable-' . $k]))
//                continue;

            if (isset($this->params['timetables']['timetable-' . $k]) )
                continue;

            $cookie = [];
            foreach ($days as $day)
                if (!is_null($timetable->$day))
                    $cookie[$day] = json_decode($timetable->$day);

            if (!$checked) {
                $this->params['timetables']['timetable-' . $k] = $cookie;
                setcookie('timetable-' . $k, json_encode($cookie), ['path' => '/' . $slug . '/users/', 'samesite' => 'Lax']);
            }
            else {
                $this->params['timetables']['checked-' . $k] = UserTimetable::getChecked($cookie);
                setcookie('checked-' . $k, json_encode(UserTimetable::getChecked($cookie)), ['samesite' => 'Lax', 'path' => '/' . $slug . '/users/']);
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        $validator = $this->validateUser($request);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 405);

        try {
            $role = Role::where('slug', $request->role)->first();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['admin' => __('Выбранная роль не доступна.'), 'message' => $e->getMessage()]], 405);
        }

        $services = [];
        if ($role->slug == 'master') {
            $services = User::relationServicesAddresses($request->services, $request->addresses);
            if (!is_array($services))
                return response()->json(['errors' => ['admin' => $services]], 405);
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

            if ($role->slug == 'master')
                $user->attachCustom('services', $services);

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
            return response()->json(['errors' => ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editUser(Request $request): JsonResponse
    {
        $validator = $this->validateUser($request, false);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 405);

        try {
            $role = Role::where('slug', $request->role)->first();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['admin' => __('Выбранная роль не доступна.')]], 405);
        }

        $services = [];
        if ($role->slug == 'master') {
            $services = User::relationServicesAddresses($request->services, $request->addresses);
            if (!is_array($services))
                return response()->json(['errors' => ['admin' => $services]], 405);
        }


        try {

            $root = \Auth::user();

            $array = [
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'updated_by' => $root->id,
            ];

            if (!empty($request->password))
                $array['password'] = bcrypt($request->password);

            $user = User::find($request->id);
            $user->update($array);

            $user->roles()->detach($user->getIds('roles'));
            $user->roles()->attach($role);

            if ($role->slug == 'master')
                $user->attachCustom('services', $services, true);

            $user->timetables()->delete();
            $user->attachTimetables($request->timetables, $request->addresses, $services);

            $user->attachCustom('addresses', $request->addresses, true);

            return response()->json(['ok' => true], 200);

        }

        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    public function modalEditUser(Request $request)
    {
        $this->params['id'] = $request->id;
        $this->params['user'] = User::find($this->params['id']);
        $this->setTimetableCookies($this->params['user'], $request->business);
        $this->params['services'] = Service::withoutTimetable();
        $this->params['addresses'] = Address::all();
        $this->params['moreService'] = $request->moreService ?? count($this->params['user']->addresses);

        $this->setParams($request);
        $html = view($this->view, $this->params)->render();
        return response()->json(['html' => $html]);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function addService(Request $request)
    {

        $this->params['services'] = Service::withoutTimetable();
        $this->params['addresses'] = Address::all();
        $this->params['moreService'] = intval($request->moreService);

        if ($request->modal == 'edit') {
            $user = User::find($request->id);
            $this->params['user'] = $user;
            $this->params['id'] = $request->id;
            return $this->getView($request);
        }
        else
            $this->params['roles'] = [Role::where('slug', 'admin')->first(), Role::where('slug', 'master')->first()];

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteUser(Request $request): JsonResponse
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
    public function manageConfirm(Request $request)
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
    public function manageReject(Request $request)
    {
        $this->deleteUser($request);
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function getView(Request $request)
    {
        if (! isset($this->params['timetables'])) {
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
    private function validateUser(Request $request, $create = true): Validator
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
        if (!$validator->fails()) {
            $validator->after(function ($validator) use ($request) {

                if (empty($request->addresses))
                    $validator->errors()->add('addresses', __('Укажите адрес'));

                foreach ($request->addresses as $address)
                    if (empty($address)) $validator->errors()->add('address', __('Указаны не все адерса'));

                if ($request->role == 'master') {

                    if (empty($request->services))
                        $validator->errors()->add('services', __('Укажите услугу'));

                    foreach ($request->services as $address)
                        if (empty($address)) $validator->errors()->add('service', __('Указаны не все услуги'));

                }
                else {
                    $duplicates = collect($request->addresses)->toBase()->duplicates();
                    if ($duplicates->count())
                        $validator->errors()->add('addresses', __('Адреса не могут быть продублированы.'));
                }
            });
        }


        return $validator;
    }
}
