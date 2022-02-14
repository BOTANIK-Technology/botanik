<?php

use Illuminate\Support\Facades\Artisan;
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
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return 'Botanic.inc';
//    abort(404);
});

/**
 * A-level routes
 */

Route::group(
    [
        'as' => 'root.',
        'prefix' => 'a-level',
        'middleware' => 'root.auth'
    ],
    function () {

        /**
         * Redirect from index to login route
         */
        Route::get('/', function () {
            return redirect()->route('login', ['a-level']);
        });

        /**
         * Default auth routes without registration
         */
        Auth::routes(['register' => false]);

        /**
         * Routes only for auth users
         */
        Route::group(
            [
                'middleware' => 'is.auth:root',
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

        Route::group(
            [
                'prefix' => '/api',
                'middleware' => 'api'
            ],
            function() {
                Route::post('/services_addresses', [App\Http\Controllers\ScheduleController::class, 'getAddresses'])->name('api.services_addresses');
                Route::post('/services_masters', [App\Http\Controllers\ScheduleController::class, 'getMasters'])->name('api.services_masters');
                Route::post('/services_list', [App\Http\Controllers\ScheduleController::class, 'getServices'])->name('api.services_list');
                Route::post('/calendar', [App\Http\Controllers\ScheduleController::class, 'getCalendar'])->name('api.calendar');
                Route::post('/times', [App\Http\Controllers\ScheduleController::class, 'getTimes'])->name('api.times');
            }
        );


        /**
         * Redirect from index to login route
         */
        Route::get('/', function () {
          return redirect()->route('login', [Route::getCurrentRoute()->parameter('business')]);
        });

        /**
         * Default auth routes without registration
         */
        Auth::routes(['register' => false]);

        /**
         * Password Reset
         */
        Route::post('/custom-reset', [App\Http\Controllers\ResetController::class, 'reset'])->name('custom.reset');
        Route::get('/reset/confirm', [App\Http\Controllers\ResetController::class, 'confirm'])->name('custom.reset.confirm');

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
                Route::post('/notice-event', [App\Http\Controllers\NoticeController::class, 'getNoticeEvent'])->name('noticeEvent');
                Route::get('/notice/delete/{id}', [App\Http\Controllers\NoticeController::class, 'delete'])->name('deleteNotice');

                /**
                 * Schedule routes
                 */
                Route::get('/schedule', [App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule');
                Route::get('/schedule/{modal}/{id?}', [App\Http\Controllers\ScheduleController::class, 'window'])->name('window.schedule');
                Route::post('/schedule/delete/{id}', [App\Http\Controllers\ScheduleController::class, 'deleteSchedule'])->name('schedule.delete');
                Route::post('/schedule/update/{id}', [App\Http\Controllers\ScheduleController::class, 'editSchedule'])->name('schedule.update');
                Route::post('/schedule/create', [App\Http\Controllers\ScheduleController::class, 'createRecord'])->name('schedule.create');
                Route::get('/testmail',[App\Http\Controllers\TestMailController::class, 'send'])->name('emails.user-create');
                Route::get('/testowner',[App\Http\Controllers\TestMailController::class, 'owner'])->name('emails.owner');
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
                    //Route::post('/catalog/delete/{id}', [App\Http\Controllers\CatalogController::class, 'delete'])->name('catalog.delete');
                    Route::post('/catalog/delete/{id}', [App\Http\Controllers\CatalogController::class, 'deleteConfirm'])->name('catalog.delete');
                });

                /**
                 * Routes only for admins and owners
                 */
                Route::group(['middleware' => 'role:owner,admin'], function () {

                    /**
                     * Services routes
                     */
                    Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('service');
                    Route::post('/services/window/timetable/check-records', [App\Http\Controllers\ScheduleController::class, 'checkRecords']);

                    Route::get('/services/window/{modal}/{id?}', [App\Http\Controllers\ServiceController::class, 'window'])->name('window.service');
                    Route::post('/services/window/create/add-type', [App\Http\Controllers\ServiceController::class, 'addType']);
                    Route::post('/services/window/create/add-address', [App\Http\Controllers\ServiceController::class, 'addAddress']);
                    Route::post('/services/window/create/add-service', [App\Http\Controllers\ServiceController::class, 'create']);
                    Route::post('/services/window/delete/{id}/confirm', [App\Http\Controllers\ServiceController::class, 'deleteService']);

                    Route::post('/services/window/edit/{id}/confirm', [App\Http\Controllers\ServiceController::class, 'editService']);
                    Route::post('/services/window/create/confirm', [App\Http\Controllers\ServiceController::class, 'editService']);

                    Route::post('/services/window/edit/{id}/remove-service', [App\Http\Controllers\ServiceController::class, 'removeService']);
                    Route::post('/services/window/edit/{id}/remove-address', [App\Http\Controllers\ServiceController::class, 'removeAddress']);


                    /**
                     * Types routes
                     */
                    Route::get('/types/window/edit/{id}', [App\Http\Controllers\TypesController::class, 'edit'])->name('types.edit');
                    Route::get('/types/window/delete/{id}', [App\Http\Controllers\TypesController::class, 'delete'])->name('types.delete');
                    Route::post('/types/window/delete/{id}/confirm', [App\Http\Controllers\TypesController::class, 'confirmDelete']);
                    Route::post('/types/window/edit/{id}/save', [App\Http\Controllers\TypesController::class, 'save']);
                    /**
                     * Addresses routes
                     */
                    Route::get('/addresses/window/edit/{id}', [App\Http\Controllers\AddressesController::class, 'edit'])->name('addresses.edit');
                    Route::get('/addresses/window/delete/{id}', [App\Http\Controllers\AddressesController::class, 'delete'])->name('addresses.delete');
                    Route::post('/addresses/window/delete/{id}/confirm', [App\Http\Controllers\AddressesController::class, 'confirmDelete']);
                    Route::post('/addresses/window/edit/{id}/save', [App\Http\Controllers\AddressesController::class, 'save']);

                    /**
                     * Users routes
                     */
                    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('user');

                    Route::get('/users/window/{modal}/{id?}', [App\Http\Controllers\UserController::class, 'window'])->name('window.user');
                    Route::get('/users/window/{modal}/{id?}/{moreService?}', [App\Http\Controllers\UserController::class, 'addService'])->name('addService');

                    Route::post('/users/window/delete/{id}/confirm', [App\Http\Controllers\UserController::class, 'deleteUser']);

//                    Route::post('/users/window/create/{id}/edit-user', [App\Http\Controllers\UserController::class, 'editUser']);
                    Route::post('/users/window/edit/{id}/edit-user', [App\Http\Controllers\UserController::class, 'editUser'])->name('editUser');
                    Route::get('/users/manage/confirm', [App\Http\Controllers\UserController::class, 'manageConfirm'])->name('manage.confirm');
                    Route::get('/users/manage/reject', [App\Http\Controllers\UserController::class, 'manageReject'])->name('manage.reject');

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

                    /**
                     * Timetable route
                     */
                    Route::post('/timetable', [App\Http\Controllers\TimetablesController::class, 'create'])->name('timetable.create');

                    Route::group(['middleware' => 'package:pro'], function () {

                        /**
                         * Feedback route
                         */
                        Route::get('/feedback/{modal?}/{id?}', [App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback');

                        /**
                         * Mail routes
                         */
                        Route::get('/mail', [App\Http\Controllers\MailController::class, 'index'])->name('mail');
                        Route::get('/mail/window/create', [App\Http\Controllers\MailController::class, 'create'])->name('mail.window.create');
                        Route::get('/mail/window/view/{id}', [App\Http\Controllers\MailController::class, 'view'])->name('mail.window.view');
                        Route::post('/mail/create', [App\Http\Controllers\MailController::class, 'createConfirm'])->name('mail.create');

                        /**
                         * Share routes
                         */
                        Route::get('/share', [App\Http\Controllers\ShareController::class, 'index'])->name('share');
                        Route::get('/share/window/{modal}/{id?}', [App\Http\Controllers\ShareController::class, 'window'])->name('window.share');
                        Route::post('/share/delete/{id}', [App\Http\Controllers\ShareController::class, 'deleteConfirm'])->name('share.delete');
                        Route::post('/share/update/{id}', [App\Http\Controllers\ShareController::class, 'editConfirm'])->name('share.update');
                        Route::post('/share/create', [App\Http\Controllers\ShareController::class, 'createConfirm'])->name('share.create');

                    });

                    /**
                     * Info routes
                     */
                    Route::get('/info', [App\Http\Controllers\InfoController::class, 'index'])->name('info');
                    Route::get('/info/window/{modal}/{id?}', [App\Http\Controllers\InfoController::class, 'window'])->name('window.info');
                    Route::post('/info/delete/{id}', [App\Http\Controllers\InfoController::class, 'deleteConfirm'])->name('info.delete');
                    Route::post('/info/update/{id}', [App\Http\Controllers\InfoController::class, 'editConfirm'])->name('info.update');
                    Route::post('/info/create', [App\Http\Controllers\InfoController::class, 'createConfirm'])->middleware('package:pro,base')->name('info.create');

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
                     * Api routes
                     */
                    Route::get('/partner-api', [App\Http\Controllers\PartnerApiController::class, 'index'])->name('api');
                    Route::put('/partner-api/{slug}/update', [App\Http\Controllers\PartnerApiController::class, 'update'])->name('api.update');
                    Route::get('/partner-api/{slug}/synchronize', [App\Http\Controllers\PartnerApiController::class, 'synchronize'])->name('api.synchronize');
                });
            }

        );
    }
);

