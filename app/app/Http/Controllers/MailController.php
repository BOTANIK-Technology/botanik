<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Models\TypeService;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public $sort;
    public $view;
    public $modal;
    public $load;
    public $id;

    public function __construct(Request $request)
    {
        $this->view = 'mail';
        isset($request->modal) ? $this->modal = $request->modal : $this->modal = false;
        $this->modal ? $this->id = $request->id : $this->id = false;
        $request->has('sort') ? $this->sort = $request->sort : $this->sort = 'asc';
        $this->load = $request->load ?? 5;

    }

    public function index()
    {
        return view($this->view,
            [
                'sort'  => $this->sort,
                'table' => Mail::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'load'  => $this->load,
                'countMail' => Mail::count()
            ]
        );
    }

    public function create()
    {
        $mail = new Mail();
        return view($this->view,
            [
                'sort'  => $this->sort,
                'modal' => $this->modal,
                //'types' => TypeService::all(),
                'types' => Service::all(),
                'table' => Mail::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'load'  => $this->load,
                'age'   => $mail->age,
                'freq'  => $mail->frequency,
                'countMail' => Mail::count()
            ]
        );
    }

    public function createConfirm(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'title'            => 'required|string|min:1|max:255',
            'text'             => 'required|string|min:1',
            'age_start'        => 'nullable|integer',
            'age_end'          => 'nullable|integer',
            'sex'              => 'nullable|boolean',
            'frequency'        => 'nullable|integer|min:0',
            'img'              => 'nullable|string',
            'button'           => 'nullable|active_url',
            'last_service'     => 'nullable|integer|min:0',
            'favorite_service' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 405);
        }
        $mail = new Mail();
        $data = [
            'title'            => $request->input('title'),
            'text'             => $request->input('text'),
            'age_start'        => $request->has('age_start')        ? $request->input('age_start')        : null,
            'age_end'          => $request->has('age_end')          ? $request->input('age_end')          : null,
            'sex'              => $request->has('sex')              ? $request->input('sex')              : null,
            'frequency'        => $request->has('frequency')        ? $request->input('frequency')        : null,
            'img'              => $request->has('img')              ? $request->input('img')              : null,
            'button'           => $request->has('button')           ? $request->input('button')           : null,
            'last_service'     => $request->has('last_service')     ? $request->input('last_service')     : null,
            'favorite_service' => $request->has('favorite_service') ? $request->input('favorite_service') : null,
        ];

        try {
            Mail::doMails($request->token, $data);
            $mail->create($data);
            return response()->json(['ok' => true]);
        }
        catch (\Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage().", ".$e->getFile().", ".$e->getLine()], 'data'=>$data], 500);
        }
    }

    public function view()
    {
        return view($this->view,
            [
                'sort'  => $this->sort,
                'table' => Mail::orderBy('created_at', $this->sort)->take($this->load)->get(),
                'load'  => $this->load,
                'modal' => $this->modal,
                'mail'  => Mail::find($this->id),
                'countMail' => Mail::count()
            ]
        );
    }
}
