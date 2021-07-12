<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Service;
use App\Models\ServiceAddress;
use App\Models\TypeService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressesController extends Controller
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
            'view' => 'addresses',
            'addresses' => Address::all(),
            'slug' => $business,
            'w_address' => Address::find($id),
            'load' => $request->load ?? 5,
            'modal' => $request->modal,
            'countService' => ServiceAddress::where('address_id', $id)->count()
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
        $address = $request->input("name", null);
        if(is_null($id) || is_null($address)) {
            throw new Exception("Адрес не изменен");
        }

        Address::where('id', $id)->update([
            "address" => $address
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * @param $business
     * @param $id
     * @param Request $request
     * @return View
     */
    public function delete($business, $id, Request $request): View
    {
        $params = [
            'view' => 'addresses',
            'addresses' => Address::all(),
            'slug' => $business,
            'address' => Address::find($id),
            'load' => $request->load ?? 5,
            'modal' => $request->modal,
            'countService' => ServiceAddress::where('address_id', $id)->count()
        ];

        return view('service.page', $params);
    }

    /**
     * @param string $business
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmDelete(string $business, int $id, Request $request): JsonResponse
    {
        try {
            Address::find($id)->delete();
        }
        catch (Exception $e) {
            return response()->json(['errors' => ['server' => $e->getMessage()]], 500);
        }
        return response()->json(['ok' => true]);
    }



}
