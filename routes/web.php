<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * Index route
 */
Route::get('/', function () {
    abort(404);
});

/**
 * A-level routes
 */
Route::group(
    [
        'as' => 'root.',
        'prefix' => 'a-level'
    ],
    function () {

        /**
         * Redirect from index to login route
         */
        Route::get('/', function () {
            return redirect()->route('login', ['a-level']);
        });

        /**
         * Default auth laravel routes without registration
         */
        Auth::routes(['register' => false]);

        /**
         * Routes only for auth users
         */
        Route::group(
            [
                'middleware' => 'is.auth'
            ],
            function () {

                /*
                 * Create business routes
                 */
                Route::get('/business', [App\Http\Controllers\Root\BusinessController::class, 'index'])->name('business');
                Route::post('/business/create', [App\Http\Controllers\Root\BusinessController::class, 'create'])->name('business.create');

                /*
                 * Management routes
                 */
                Route::get('/management', [App\Http\Controllers\Root\ManagementController::class, 'index'])->name('management');
                Route::get('/management/window/{modal}/{id}', [App\Http\Controllers\Root\ManagementController::class, 'window'])->name('window.management');
                Route::post('/management/edit/{id}', [App\Http\Controllers\Root\ManagementController::class, 'edit'])->name('management.edit');
                Route::post('/management/pause/{id}', [App\Http\Controllers\Root\ManagementController::class, 'pause'])->name('management.pause');
                Route::post('/management/delete/{id}', [App\Http\Controllers\Root\ManagementController::class, 'delete'])->name('management.delete');
                Route::post('/management/webhook/{id}', [App\Http\Controllers\Root\ManagementController::class, 'webhook'])->name('management.webhook');

                /*
                 * Support routes support
                 */
                Route::get('/supports', [App\Http\Controllers\Root\SupportController::class, 'rootIndex'])->name('supports');

                /**
                 * Analytics routes analytic
                 */
                Route::get('/analytic', [App\Http\Controllers\Root\AnalyticController::class, 'index'])->name('analytic');

            }
        );
    }
);

/**
 * Business routes
 */
Route::group(
    [
        'prefix' => '{business?}',
        'middleware' => 'set.business'
    ],
    function () {

        /**
         * Redirect from index to login route
         */
        Route::get('/', function () {
          return redirect()->route('login', [Route::getCurrentRoute()->parameter('business')]);
        });

        /**
         * Default auth laravel routes without registration
         */
        Auth::routes(['register' => false]);

        /**
         * Routes only for auth users
         */
        Route::group(
            [
                'middleware' => 'is.auth'
            ],
            function () {

                /**
                 * Home page
                 * app redirect to this page after login
                 */
                Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

                /**
                 * Notice routes
                 */
                Route::get('/notice', [App\Http\Controllers\NoticeController::class, 'index'])->name('notice');
                Route::get('/notice/delete/{id}', [App\Http\Controllers\NoticeController::class, 'delete'])->name('deleteNotice');

                /**
                 * Schedule routes
                 */
                Route::get('/schedule', [App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule');
                Route::get('/schedule/{modal}/{id?}', [App\Http\Controllers\ScheduleController::class, 'window'])->name('window.schedule');
                Route::post('/schedule/{modal}/{id}/confirm', [App\Http\Controllers\ScheduleController::class, 'deleteSchedule']);
                Route::post('/schedule/{modal}/{id}/edit-schedule', [App\Http\Controllers\ScheduleController::class, 'editSchedule']);
                Route::post('/schedule/services', [App\Http\Controllers\ScheduleController::class, 'createService']);
                Route::post('/schedule/addresses', [App\Http\Controllers\ScheduleController::class, 'createAddress']);
                Route::post('/schedule/masters', [App\Http\Controllers\ScheduleController::class, 'createMaster']);
                Route::post('/schedule/dates', [App\Http\Controllers\ScheduleController::class, 'createDates']);
                Route::post('/schedule/times', [App\Http\Controllers\ScheduleController::class, 'createTimes']);
                Route::post('/schedule/create', [App\Http\Controllers\ScheduleController::class, 'createRecord'])->name('schedule.create');

                /**
                 * Reports routes
                 */
                Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report');
                Route::get('/report/download', [App\Http\Controllers\ReportController::class, 'download'])->name('report.download');
                Route::get('/report/catalog-download', [App\Http\Controllers\ReportController::class, 'downloadCatalog'])->name('report.catalog.download')->middleware('has:catalog');

                /**
                 * Catalog routes
                 */
                Route::group(['middleware' => 'has:catalog'], function () {
                    Route::get('/catalog', [App\Http\Controllers\CatalogController::class, 'index'])->name('catalog');
                    Route::get('/catalog/window/{modal}/{id?}', [App\Http\Controllers\CatalogController::class, 'window'])->name('window.catalog');
                    Route::post('/catalog/edit/{id}', [App\Http\Controllers\CatalogController::class, 'edit'])->name('catalog.edit');
                    Route::post('/catalog/create', [App\Http\Controllers\CatalogController::class, 'create'])->name('catalog.create');
                    Route::post('/catalog/delete/{id}', [App\Http\Controllers\CatalogController::class, 'delete'])->name('catalog.delete');
                });

                /**
                 * Routes only for admins and owners
                 */
                Route::group(['middleware' => 'role:owner,admin'], function () {

                    /**
                     * Services routes
                     */
                    Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('service');
                    Route::get('/services/window/{modal}/{id?}', [App\Http\Controllers\ServiceController::class, 'window'])->name('window.service');
                    Route::post('/services/window/create/add-type', [App\Http\Controllers\ServiceController::class, 'addType']);
                    Route::post('/services/window/create/add-address', [App\Http\Controllers\ServiceController::class, 'addAddress']);
                    Route::post('/services/window/create/add-service', [App\Http\Controllers\ServiceController::class, 'create']);
                    Route::post('/services/window/delete/{id}/confirm', [App\Http\Controllers\ServiceController::class, 'deleteService']);
                    Route::post('/services/window/edit/{id}/confirm', [App\Http\Controllers\ServiceController::class, 'editService']);
                    Route::post('/services/window/edit/{id}/remove-service', [App\Http\Controllers\ServiceController::class, 'removeService']);
                    Route::post('/services/window/edit/{id}/remove-address', [App\Http\Controllers\ServiceController::class, 'removeAddress']);

                    /**
                     * Users routes
                     */
                    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('user');
                    Route::get('/users/window/{modal}/{id?}', [App\Http\Controllers\UserController::class, 'window'])->name('window.user');
                    Route::get('/users/window/{modal}/{id?}/{moreService?}', [App\Http\Controllers\UserController::class, 'addService'])->name('addService');
                    Route::post('/users/window/create/add-user', [App\Http\Controllers\UserController::class, 'addUser']);
                    Route::post('/users/window/delete/{id}/confirm', [App\Http\Controllers\UserController::class, 'deleteUser']);
                    Route::post('/users/window/edit/{id}/edit-user', [App\Http\Controllers\UserController::class, 'editUser']);

                    /**
                     * Clients routes
                     */
                    Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('client');
                    Route::get('/clients/{modal}/{id}', [App\Http\Controllers\ClientController::class, 'window'])->name('window.client');
                    Route::post('/clients/edit/{id}', [App\Http\Controllers\ClientController::class, 'edit']);
                    Route::post('/clients/delete/{id}', [App\Http\Controllers\ClientController::class, 'delete']);
                    Route::post('/clients/block/{id}', [App\Http\Controllers\ClientController::class, 'block']);

                    /**
                     * Review route
                     */
                    Route::get('/reviews/{modal?}/{id?}', [App\Http\Controllers\FeedbackController::class, 'index'])->name('review');

                    Route::group(['middleware' => 'package:pro'], function () {

                        /**
                         * Feedback route
                         */
                        Route::get('/feedback/{modal?}/{id?}', [App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback');

                        /**
                         * Mail routes
                         */
                        Route::get('/mail', [App\Http\Controllers\MailController::class, 'index'])->name('mail');
                        Route::get('/mail/window/{modal}', [App\Http\Controllers\MailController::class, 'create'])->name('mail.create');
                        Route::get('/mail/window/{modal}/{id}', [App\Http\Controllers\MailController::class, 'view'])->name('mail.view');
                        Route::post('/mail/window/create/confirm', [App\Http\Controllers\MailController::class, 'createConfirm']);

                        /**
                         * Share routes
                         */
                        Route::get('/share', [App\Http\Controllers\ShareController::class, 'index'])->name('share');
                        Route::get('/share/window/{modal}/{id?}', [App\Http\Controllers\ShareController::class, 'window'])->name('window.share');
                        Route::post('/share/window/delete/{id}/confirm', [App\Http\Controllers\ShareController::class, 'deleteConfirm']);
                        Route::post('/share/window/edit/{id}/confirm', [App\Http\Controllers\ShareController::class, 'editConfirm']);
                        Route::post('/share/window/create/confirm', [App\Http\Controllers\ShareController::class, 'createConfirm']);

                    });

                    /**
                     * Info routes
                     */
                    Route::get('/info', [App\Http\Controllers\InfoController::class, 'index'])->name('info');
                    Route::get('/info/window/{modal}/{id?}', [App\Http\Controllers\InfoController::class, 'window'])->name('window.info');
                    Route::post('/info/window/delete/{id}/confirm', [App\Http\Controllers\InfoController::class, 'deleteConfirm']);
                    Route::post('/info/window/edit/{id}/confirm', [App\Http\Controllers\InfoController::class, 'editConfirm']);
                    Route::post('/info/window/create/confirm', [App\Http\Controllers\InfoController::class, 'createConfirm'])->middleware('package:pro,base');

                    /**
                     * Support routes
                     */
                    Route::get('/support', [App\Http\Controllers\Root\SupportController::class, 'index'])->name('support');
                    Route::post('/support/create', [App\Http\Controllers\Root\SupportController::class, 'create'])->name('support.create');

                });

                /**
                 * Routes only for owners from pro version
                 */
                Route::group(['middleware' => ['role:owner', 'package:pro']], function () {

                    /**
                     * Users routes
                     */
                    Route::get('/users/manage/confirm', [App\Http\Controllers\UserController::class, 'manageConfirm'])->name('manage.confirm');
                    Route::get('/users/manage/reject', [App\Http\Controllers\UserController::class, 'manageReject'])->name('manage.reject');

                });
            }
        );
    }
);

