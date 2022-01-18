<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\TelegramUser;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\View\View;
use Validator;

class ClientController extends Controller
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
    public $view = 'client';


    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {

        isset($request->load) ? $this->params['load'] = $request->load : $this->params['load'] = 15;
        isset($request->sort) ? $this->params['sort'] = $request->sort : $this->params['sort'] = 'visit_asc';

        switch ($this->params['sort']) {
            case 'visit_asc':
                $this->params['clients'] = TelegramUser::orderBy('updated_at', 'asc')->take($this->params['load'])->get();
                break;
            case 'visit_desc':
                $this->params['clients'] = TelegramUser::orderBy('updated_at', 'desc')->take($this->params['load'])->get();
                break;
            case 'frequency_asc':
                $this->params['clients'] = TelegramUser::orderBy('frequency', 'asc')->take($this->params['load'])->get();
                break;
            case 'frequency_desc':
                $this->params['clients'] = TelegramUser::orderBy('frequency', 'desc')->take($this->params['load'])->get();
                break;
            case 'price_asc':
                $this->params['clients'] = TelegramUser::orderBy('spent_money', 'asc')->take($this->params['load'])->get();
                break;
            case 'price_desc':
                $this->params['clients'] = TelegramUser::orderBy('spent_money', 'desc')->take($this->params['load'])->get();
                break;
        }

        if ( isset($request->search) ) {
            $this->params['search'] = $request->search;
            $words = explode(' ',trim($request->search));
            switch (count($words)) {
                case 1:
                    $this->params['clients'] = $this->search($words[0]);
                    break;
                case 2:
                    $this->params['clients'] = $this->params['clients']->where('last_name', $words[0])->where('first_name', $words[1]);
                    break;
                case 3:
                    $this->params['clients'] = $this->params['clients']->where('last_name', $words[0])->where('first_name', $words[1])->where('middle_name', $words[2]);
                    break;
                default:
                    $this->params['clients'] = collect([]);
                    break;
            }
        }

        if ( $this->params['clients']->isEmpty() ) $this->params['clients'] = 0;
        $this->params['countClients'] = is_array($this->params['clients']) ? $this->params['clients']->count() : 0;
        $this->params['titles'] = TelegramUser::getTitles();

        if (isset($request->modal)) $this->params['modal'] = $request->modal;
    }

    /**
     * @param string $search
     * @return \Illuminate\Support\Collection
     */
    private function search (string $search) : Collection
    {
        if (!strlen($search))
            return $this->params['clients'];
        foreach (['username', 'phone', 'email', 'last_name', 'first_name'] as $title) {
            $result = $this->params['clients']->where($title, $search);
            if (!$result->isEmpty())
                return $result;
        }
        return collect([]);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function getView(Request $request)
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
     * @return Factory|View
     */
    public function window(Request $request)
    {
        $this->params['current'] = TelegramUser::find($request->id);

        if ($request->modal == 'statistic') {
            $this->params['time'] = $request->time ?? 'all';
            $this->params['stat'] = $this->params['current']->getStatistic($this->params['time']);
        }
        elseif ($request->modal == 'history') {
            $this->params['visits'] = $this->params['current']->getVisitTable();
            $this->params['labels'] = $this->params['current']->getVisitLabels();
        }
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {

         $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username'    => 'nullable|string|max:32',
            'email'       => 'nullable|email',
            'phone'       => 'string|max:20',
            'age'         => 'nullable|integer|min:0',
            'bonus'       => 'nullable|integer|min:0',
            'sex'         => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {

            $array = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? null,
                'middle_name' => $request->middle_name ?? null,
                'username' => $request->username ?? null,
                'email' => $request->email ?? null,
                'phone' => $request->phone,
                'age' => $request->age ?? null,
                'bonus' => $request->bonus ?? 0,
            ];

            if (!is_null($request->sex)) {
                switch (trim(mb_strtolower($request->sex))) {
                    case 'мужчина':
                    case 'мужской':
                    case 'м':
                        $array['sex'] = 1;
                        break;
                    case 'женщина':
                    case 'женский':
                    case 'ж':
                        $array['sex'] = 0;
                        break;
                }
            }

            TelegramUser::find($request->id)->update($array);
            return response()->json(['ok' => true], 200);

        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function block (Request $request): JsonResponse
    {
        try {
            $client = TelegramUser::findOrFail($request->id);
            $client->update(['status' => !$client->status]);
            return response()->json(['ok' => true]);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 501);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete (Request $request): JsonResponse
    {
        try {
            TelegramUser::find($request->id)->delete();
            return response()->json(['ok' => true], 200);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 501);
        }
    }


}
