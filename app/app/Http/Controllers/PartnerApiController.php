<?php

namespace App\Http\Controllers;

use App\Helpers\Beauty\BeautyPro;
use App\Helpers\Yclients\Yclients;
use App\Helpers\Yclients\YclientsException;
use App\Http\Requests\v1\PartnerApiRequest;
use App\Models\Api;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerApiController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index ()
    {
        return view(
            'api.page',
            [
                'apis' => Api::all()
            ]
        );
    }

    /**
     * @param PartnerApiRequest $request
     * @return JsonResponse
     */
    public function update (PartnerApiRequest $request): JsonResponse
    {
        $api = Api::where('slug', $request->slug)->firstOrFail();
        return response()->json([
            'result' => $api->updateConfig($request->params)
        ]);
    }

    /**
     * @param PartnerApiRequest $request
     * @return JsonResponse
     */
    public function call(PartnerApiRequest $request): JsonResponse
    {
        switch ($request->slug) {
            case 'beauty':
                return response()->json(BeautyPro::apiCall($request->method, $request->params ?? []));
            case 'yclients':
                return response()->json(Yclients::apiCall($request->method, $request->params ?? []));
        }

        return response()->json(['errors' => ['message' => ' "'.$request->slug.'" API integration not found.']], 404);
    }

    /**
     * @param Request $request
     * @return array|Application|Factory|View|mixed
     */
    public function synchronize(Request $request)
    {
        switch ($request->slug) {
            case 'beauty':
                if(BeautyPro::isActive()) {
                    $api = new BeautyPro();
                    $res = $api->synchronize();
                    return view('api.page', ['apis' => Api::all(), 'modal' => 'beauty', 'result' => $res]);
                }
            case 'yclients':
                if(Yclients::isActive()) {
                    $api = new Yclients();
                    $res = $api->synchronize();
                    return view('api.page', ['apis' => Api::all(), 'modal' => 'yclients', 'result' => $res]);
                }
        }

        return view('api.page', ['apis' => Api::all()]);
    }

}
