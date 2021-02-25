<?php

use App\Http\Controllers\Root\AnalyticController;
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

/**
 * A-level API
 */
Route::group(
    ['prefix' => '/a-level', 'middleware' => 'root.auth'],
    function () {
        // Collect analytic
        Route::post('/analytic/collect', [AnalyticController::class, 'collect'])->name('analytic.collect');
    }
);
