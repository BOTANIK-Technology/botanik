<?php

use App\Http\Controllers\API\v1\StorageController;
use App\Http\Controllers\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Telegram API
 */
Route::group(
    ['prefix' => '/telegram/{slug}', 'middleware' => 'tg.check.req'],
    function () {
        Route::post('/', [TelegramController::class, 'main']);
        Route::post('/admin', [TelegramController::class, 'admin']);
    }
);

Route::group(
    [
        'prefix' => '/v1'
    ],
    function () {
        Route::post('/storage', [StorageController::class, 'store'])->name('api.storage');
    }
);
