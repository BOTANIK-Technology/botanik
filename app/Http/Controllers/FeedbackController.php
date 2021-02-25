<?php

namespace App\Http\Controllers;

use App\Models\FeedBack;
use App\Models\Review;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{

    public $sort;
    public $view;
    public $modal;
    public $id;
    public $route;
    public $load;

    /**
     * FeedbackController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->view = 'feedback';
        $this->route = \Route::currentRouteName();
        isset($request->modal) ? $this->modal = $request->modal : $this->modal = false;
        $this->modal ? $this->id = $request->id : $this->id = false;
        $request->has('sort') ? $this->sort = $request->sort : $this->sort = 'asc';
        $request->has('load') ? $this->load = $request->load : $this->load = 5;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->route == 'review') {
            if ($this->modal)
                return $this->windowReview();

            return view($this->view,
                [
                    'sort' => $this->sort,
                    'table' => Review::orderBy('created_at', $this->sort)->take($this->load)->get(),
                    'countItems' => Review::count(),
                    'load' => $this->load,
                    'route' => $this->route
                ]
            );
        }
        if ($this->modal)
            return $this->windowFeedback();
        return view(
            $this->view,
            [
                'sort' => $this->sort,
                'table' => FeedBack::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'countItems' => FeedBack::count(),
                'load' => $this->load,
                'route' => $this->route
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function windowReview ()
    {
        return view($this->view,
            [
                'sort' => $this->sort,
                'modal' => $this->modal,
                'table' => Review::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'content' => Review::find($this->id),
                'countItems' => Review::count(),
                'load' => $this->load,
                'route' => $this->route
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function windowFeedback ()
    {
        return view($this->view,
            [
                'sort' => $this->sort,
                'modal' => $this->modal,
                'table' => FeedBack::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'content' => FeedBack::find($this->id),
                'countItems' => FeedBack::count(),
                'load' => $this->load,
                'route' => $this->route
            ]
        );
    }
}
