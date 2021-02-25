<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Information;

class InfoController extends Controller
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
    public $view = 'info';

    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $this->params['countInfo'] = Information::count();
        $this->params['load'] = $request->load ?? 5;
        $this->params['table'] = Information::take($this->params['load'])->get() ?? 0;
        if (isset($request->modal)) $this->params['modal'] = $request->modal;
        if (isset($request->id)) $this->params['id'] = $request->id;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getView(Request $request)
    {
        $this->setParams($request);
        return view($this->view, $this->params);
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function window(Request $request)
    {
        switch ($request->modal) {
            case 'view':
            case 'delete':
            case 'edit':
                $this->params['info'] = Information::find($request->id);
        }

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteConfirm (Request $request)
    {
        try {
            Information::find($request->id)->delete();
            return response()->json(['ok' => true], 200);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createConfirm(Request $request)
    {
        $validator = $this->validateInfo($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $create = [
                'title'   => $request->input('title'),
                'text'    => $request->input('text'),
                'img'     => $request->has('img')    ? $request->input('img')    : null,
                'button'  => $request->has('button') ? $request->input('button') : null
            ];
            $request->has('addresses') ? $create['addresses'] = \GuzzleHttp\json_encode($request->input('addresses')) : $create['addresses'] = null;
            Information::create($create);
            return response()->json(['ok' => true], 200);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editConfirm(Request $request)
    {
        $validator = $this->validateInfo($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $update = [
                'title'   => $request->input('title'),
                'text'    => $request->input('text'),
                'img'     => $request->has('img')    ? $request->input('img')    : null,
                'button'  => $request->has('button') ? $request->input('button') : null
            ];
            $request->has('addresses') ? $update['addresses'] = \GuzzleHttp\json_encode($request->input('addresses')) : $update['addresses'] = null;
            Information::find($request->input('id'))->update($update);
            return response()->json(['ok' => true], 200);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateInfo(Request $request)
    {
        return \Validator::make($request->all(), [
            'title'     => 'required|string|min:1|max:255',
            'text'      => 'required|string|min:1',
            'img'       => 'nullable|active_url',
            'button'    => 'nullable|active_url',
            'addresses' => 'nullable|array'
        ]);
    }

}