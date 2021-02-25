<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use Illuminate\Http\Request;

class CatalogController extends Controller
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
    public $view = 'catalog';

    /**
     * Set default params for view
     *
     * @param Request $request
     */
    public function setParams(Request $request)
    {
        $this->params['countItems'] = Catalog::count();
        $this->params['load'] = $request->load ?? 5;
        $this->params['table'] = Catalog::take($this->params['load'])->get() ?? 0;
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
        $this->params['modal'] = $request->modal;
        if ($request->has('id')) $this->params['id'] = $request->id;

        switch ($request->modal) {
            case 'view':
            case 'delete':
            case 'edit':
                $this->params['product'] = Catalog::find($request->id);
        }

        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = $this->validateInfo($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $create = [
                'title'   => $request->input('title'),
                'text'    => $request->input('text'),
                'img'     => $request->has('img') ? $request->input('img') : null,
                'price'   => $request->input('price'),
                'article' => $request->input('article'),
                'count'   => $request->input('count')
            ];
            Catalog::create($create);
            return response()->json(['ok' => true], 200);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
    }

    public function edit(Request $request)
    {
        $validator = $this->validateInfo($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }

        try {
            $update = [
                'title'   => $request->input('title'),
                'text'    => $request->input('text'),
                'img'     => $request->has('img') ? $request->input('img') : null,
                'price'   => $request->input('price'),
                'article' => $request->input('article'),
                'count'   => $request->input('count')
            ];
            Catalog::find($request->input('id'))->update($update);
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
            'price'     => 'required|int|min:0',
            'article'   => 'required|string|min:1|max:50',
            'count'     => 'required|int|min:0',
        ]);
    }

}
