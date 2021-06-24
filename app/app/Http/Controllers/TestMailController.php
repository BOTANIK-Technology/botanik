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
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class TestMailController extends Controller
{

    public function send(Request $request): string
    {
        $email = ['zerg3519@gmail.com' => 'Some One'];
        $name = 'Worker 3';
        $business = 'test_16_05';
        $body = view('emails.user-create', ['login'=> 'zerg3519@gmail.com', 'password' => 'Zerg1979', 'slug' => 'test_16_05']);

        $transport = new Swift_SmtpTransport('localhost', 25);
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message('Доступ к BOTANIK - ' . $business))
            ->setFrom([env('MAIL_FROM_ADDRESS') => 'Some One'])
            ->setTo($email)
            ->setBody($body);

        $mailer->send($message);

        return "OK";
    }

}
