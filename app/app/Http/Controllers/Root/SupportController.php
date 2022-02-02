<?php

namespace App\Http\Controllers\Root;

use App\Models\Root\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    /**
     * Params for view.
     * @var array
     */
    public array $params = [];

    /**
     * View name.
     * @var string
     */
    public $view = 'support';

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function  index(Request $request)
    {
        return $this->getView($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getView(Request $request)
    {
        $this->params['business_id'] = $request->business_id;
        $this->params['user_id'] = \Auth::user()->id;
        return view($this->view, $this->params);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rootIndex()
    {
        return view('root.supports');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required|min:5|max:255',
            'image'       => 'nullable|file|mimes:jpg,png',
            'text'        => 'required|min:5',
            'business_id' => 'required|integer',
            'user_id'     => 'required|integer'
        ]);

        \ConnectService::setDefaultConnect();

        $support = Support::create([
            'title'       => $request->input('title'),
            'img'         => $request->has('image') ? $request->file('image')->store('supports', 'public') : null,
            'text'        => $request->input('text'),
            'business_id' => $request->business_id,
            'user_id'     => $request->user_id,
        ]);

        $support ?
            $this->params['response'] = __('Ваша заявка обрабатывается.  Наши специалисты свяжутся с Вами.') :
            $this->params['response'] =  __('500 Internal Server Error');

        \ConnectService::dbConnect($request->business_db);

        return $this->getView($request);

    }
}
