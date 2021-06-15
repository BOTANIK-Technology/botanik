<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class ShareController extends Controller
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
    public string $view = 'share';


    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $request->has('sort') ? $this->params['sort'] = $request->sort :  $this->params['sort'] = 'asc';
        $this->params['table'] = Share::orderBy('created_at', $this->params['sort'])->get();
        $this->params['countShares'] = $this->params['table']->count();
        $this->params['load'] = $request->load ?? 5;
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
        $this->params['modal'] = $request->modal;
        switch ($request->modal) {
            case 'view':
            case 'delete':
            case 'edit':
                $this->params['share'] = Share::find($request->id);
        }
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConfirm (Request $request): JsonResponse
    {
        return response()->json(
            ['destroyed' => Share::destroy($request->id)]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createConfirm(Request $request): JsonResponse
    {
        $validator = $this->validateShare($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            Share::create(
                [
                    'title' => $request->input('title'),
                    'text' => $request->input('text'),
                    'img' => $request->has('img') ? $request->input('img') : null,
                    'button' => $request->has('button') ? $request->input('button') : null,
                    'user_id' => $request->input('user_id'),
                ]
            );

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
        $validator = $this->validateShare($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        $update = [
            'title' => $request->input('title'),
            'text' => $request->input('text'),
            'button' => $request->has('button') ? $request->input('button') : null,
            'user_id' => $request->input('user_id'),
        ];

        if ($request->has('img') && !empty($request->input('img'))) $update['img'] = $request->input('img');

        try {
            Share::findOrFail($request->id)->update($update);
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
    private function validateShare(Request $request): Validator
    {
        return \Validator::make($request->all(), [
            'title'   => 'required|string|min:1|max:255',
            'text'    => 'required|string|min:1',
            'img'     => 'nullable|string',
            'button'  => 'nullable|active_url',
            'user_id' => 'required|integer|min:0',
        ]);
    }

}
