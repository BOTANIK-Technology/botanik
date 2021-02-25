<?php

namespace App\Http\Controllers\Root;

use App\Models\Root\Business;
use App\Models\Root\Owner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Root\Package;

class BusinessController extends Controller
{
    public function index ()
    {
        $packages = Package::orderBy('id', 'DESC')->get() ?? false;
        return view('root.business', ['packages' => $packages]);
    }

    public function create (Request $request)
    {
        $this->validate($request, [
            'last_name'     => 'required|string|min:1|max:30',
            'first_name'    => 'required|string|min:1|max:30',
            'middle_name'   => 'required|string|min:1|max:30',
            'password'      => 'required|string|min:6|max:25|confirmed',
            'email'         => 'required|email|unique:owners,email',
            'business_name' => 'required|string|min:1|max:255',
            'bot_name'      => 'required|string|min:3|max:32',
            'package'       => 'required|integer',
            'slug'          => 'required|string|min:3|max:32',
            'tg_token'      => 'required|string|max:255',
            'pay_token'     => 'nullable|string|max:255',
            'logo'          => 'nullable|file|image',
            'catalog'       => 'required|boolean'
        ]);

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
            'img'        => $request->has('logo') ? $request->file('logo')->store('logos', 'public') : null,
            'token'      => $request->input('tg_token'),
            'pay_token'  => $request->has('pay_token') ? $request->input('pay_token') : null,
            'catalog'    => $request->input('catalog'),
            'owner_id'   => $owner->id,
        ]);

        $business->deploy();

        return $this->index();
    }
}
