<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StorageRequest;
use Illuminate\Http\JsonResponse;

class StorageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param StorageRequest $request
     * @return JsonResponse
     */
    public function store (StorageRequest $request): JsonResponse
    {
        $path = $request->file('image')->store($request->has('path') ? $request->path : 'all', 'public');
        if ($path === false)
            return response()->json(['errors' => ['message' => __('Не удалось сохранить image в хранилище.')]], 501);
        return response()->json(['path' => $path]);
    }
}
