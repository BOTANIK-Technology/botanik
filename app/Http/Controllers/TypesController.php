<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function PHPUnit\Framework\returnArgument;

class TypesController extends Controller
{

    /**
     * @param string $business
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function edit(string $business, int $id, Request $request): View
    {
        $params = [
            'id' => $id,
            'view' => 'types',
            'types' => TypeService::all(),
            'slug' => $business,
            'w_type' => TypeService::find($id),
            'load' => $request->load ?? 5,
            'modal' => $request->modal,
            'countService' => Service::where('type_service_id', $id)->count()
        ];

        return view('service.page', $params);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function save(Request $request): JsonResponse
    {
        $id = $request->input("id", null);
        $name = $request->input("name", null);
        if(is_null($id) || is_null($name)) {
            throw new Exception("Тип услуги не изменен");
        }

        TypeService::where('id', $id)->update([
            "type" => $name
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * @param string $business
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function delete(string $business, int $id, Request $request): View
    {
        $params = [
            'view' => 'types',
            'types' => TypeService::all(),
            'slug' => $business,
            'type' => TypeService::find($id),
            'load' => $request->load ?? 5,
            'modal' => $request->modal,
            'countService' => Service::where('type_service_id', $id)->count()
        ];

        return view('service.page', $params);
    }

    /**
     * @param $business
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmDelete($business, $id, Request $request): JsonResponse
    {
        try {
            TypeService::find($id)->delete();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
        return response()->json(['ok' => true]);
    }

}
