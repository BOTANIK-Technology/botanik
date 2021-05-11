<?php

namespace App\Http\Controllers\Root;

use App\Models\Root\Business;
use App\Models\Root\Owner;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Root\Package;
use mysql_xdevapi\Exception;
use Validator;

class BusinessController extends Controller
{

    public function index ()
    {
        $packages = Package::orderBy('id', 'DESC')->get() ?? false;
        return view('root.business', ['packages' => $packages]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create (Request $request): JsonResponse
    {
        $validator = $this->validator($request);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 405);

        $path = null;
        if ($request->has('logo'))
            $path = $request->file('logo')->store('logos', 'public');
        if ($request->has('image_path'))
            $path = $request->input('image_path');

        try {

            $owner = Owner::create([
                'fio'      => $request->input('last_name').' '.$request->input('first_name').' '.$request->input('middle_name'),
                'email'    => $request->input('email'),
                'password' => $request->input('password'),
            ]);

            $business = Business::create([
                'name'       => $request->input('business_name'),
                'bot_name'   => $request->input('bot_name'),
                'package_id' => $request->input('package'),
                'db_name'    => 'botanik_'.$request->input('slug'),
                'slug'       => $request->input('slug'),
                'img'        => $path,
                'token'      => $request->input('tg_token'),
                'pay_token'  => $request->has('pay_token') ? $request->input('pay_token') : null,
                'catalog'    => $request->input('catalog'),
                'owner_id'   => $owner->id,
            ]);

        } catch (Exception $e) {
            if (!empty($owner))
                $owner->delete();
            return response()->json(['errors' => ['message' => $e->getMessage()]], '501');
        }

        try {
            $business->deploy();
        } catch (GuzzleException $e) {
            return response()->json(['errors' => ['message' => $e->getMessage()]], '501');
        }

        return response()->json(['ok' => true]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    public function validator (Request $request): \Illuminate\Validation\Validator
    {
        $rules = [
            'last_name' => 'required|string|min:1|max:30',
            'first_name' => 'required|string|min:1|max:30',
            'middle_name' => 'required|string|min:1|max:30',
            'password' => 'required|string|min:6|max:25|confirmed',
            'email' => 'required|email|unique:owners,email',
            'business_name' => 'required|string|min:1|max:255',
            'bot_name' => 'required|string|min:3|max:32',
            'package' => 'required|integer',
            'slug' => 'required|string|min:3|max:32',
            'tg_token' => 'required|string|max:255',
            'pay_token' => 'nullable|string|max:255',
            'logo' => 'nullable|file|image',
            'image_path' => 'nullable|string',
            'catalog' => 'required|boolean'
        ];

        return Validator::make($request->all(), $rules);
    }
}
