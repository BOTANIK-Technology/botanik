<?php

namespace App\Http\Controllers;

use App\Models\Timetables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;


class TimetablesController extends Controller
{
    protected $routePath = 'timetables';
    protected $viewPath = 'timetables';


    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return Timetables::query()->paginate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return Timetables::updateOrCreate([
            'user_id' =>$request->input('user_id'),
            'service_id' =>$request->input('service_id'),
            'address_id' =>$request->input('address_id'),
        ]);

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id)
    {
        return Timetables::query()->findOrFail($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param Request $request
     * @return bool|int
     */
//    public function update(int $id, Request $request)
//    {
//        $item = Timetables::query()->findOrFail($id);
//        return $item->update($request->validated());
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id)
    {
        Timetables::destroy($id);
        return true;
    }
}
