<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Information;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class InfoController extends Controller
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
    public string $view = 'info';

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
    public function index (Request $request)
    {
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return Factory|View
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
     * @return JsonResponse
     */
    public function deleteConfirm (Request $request): JsonResponse
    {
        try {
            Information::find($request->id)->delete();
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
    public function createConfirm(Request $request): JsonResponse
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
            $request->has('addresses') ? $create['addresses'] = json_encode($request->input('addresses')) : $create['addresses'] = null;
            Information::create($create);
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
    public function editConfirm(Request $request): JsonResponse
    {
        $validator = $this->validateInfo($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        $update = [
            'title'   => $request->input('title'),
            'text'    => $request->input('text'),
            'button'  => $request->has('button') ? $request->input('button') : null
        ];

        if ($request->has('img') && !empty($request->input('img'))) $update['img'] = $request->input('img');
        $request->has('addresses') ? $update['addresses'] = json_encode($request->input('addresses')) : $update['addresses'] = null;

        try {
            Information::findOrFail($request->id)->update($update);
            return response()->json(['ok' => true]);
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    /**
     * @param Request $request
     * @return Validator
     */
    private function validateInfo(Request $request): Validator
    {
        return \Validator::make($request->all(), [
            'title'     => 'required|string|min:1|max:255',
            'text'      => 'required|string|min:1',
            'img'       => 'nullable|string',
            'button'    => 'nullable|active_url',
            'addresses' => 'nullable|array'
        ]);
    }

}
