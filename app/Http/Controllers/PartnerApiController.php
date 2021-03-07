<?php

namespace App\Http\Controllers;

use App\Helpers\Beauty\BeautyPro;
use App\Http\Requests\v1\PartnerApiRequest;
use App\Models\Api;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Throwable;

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
        $api->config = json_encode(
            ['partner_token' => $request->has('partner_token') ? $request->partner_token : '']
        );
        try {
            $api->saveOrFail();
        } catch (Throwable $e) {
            return response()->json(
                [
                    'errors' =>
                        ['message' => $e->getMessage()]
                ],
                501
            );
        }
        return response()->json(['result' => true]);
    }

    /**
     * @param PartnerApiRequest $request
     * @return array
     */
    public function call(PartnerApiRequest $request): array
    {
        $response = ['errors' => ['message' => 'API integration not found.']];
        switch ($request->slug) {
            case 'beauty':
                $response = BeautyPro::apiCall($request->method, $request->params ?? []);
                break;
            case 'yclients':
        }

        return response()->json($response);
    }


}
