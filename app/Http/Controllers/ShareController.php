<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Illuminate\Http\Request;

class ShareController extends Controller
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
    public $view = 'share';


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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getView (Request $request)
    {
        $this->setParams($request);
        return view($this->view, $this->params);
    }

    /**
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteConfirm (Request $request)
    {
        try {
            Share::find($request->id)->delete();
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
        $validator = $this->validateShare($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            Share::where('id', $request->input('id'))->update(
                [
                    'title' => $request->input('title'),
                    'text' => $request->input('text'),
                    'img' => $request->has('img') ? $request->input('img') : null ,
                    'button' => $request->has('button') ? $request->input('button') : null,
                    'user_id' => $request->input('user_id'),
                ]
            );
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
    private function validateShare(Request $request)
    {
        return \Validator::make($request->all(), [
            'title'   => 'required|string|min:1|max:255',
            'text'    => 'required|string|min:1',
            'img'     => 'nullable|active_url',
            'button'  => 'nullable|active_url',
            'user_id' => 'required|integer|min:0',
        ]);
    }

}
