<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use App\Models\Notice;
use App\Models\Service;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserTimetable;
use App\Models\Role;
use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class TestMailController extends Controller
{

    public function send(Request $request): string
    {
        $email = 'zerg3519@gmail.com';
        $name = 'Worker 3';
        $business = 'test_16_05';
        Mail::send('emails.user-create', ['login'=> 'zerg3519@gmail.com', 'password' => 'Zerg1979', 'slug' => 'test_16_05'], function ($message) use ($email, $name, $business) {
            $message->to($email, 'To '.$name)->subject(__('Доступ к BOTANIK - '.$business));
        });

        return "OK";
    }

}
