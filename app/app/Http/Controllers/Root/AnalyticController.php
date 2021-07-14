<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class AnalyticController extends Controller
{
    public function index()
    {
        $email = 'apekindd@gmail.com';
        $password = 'apekindd@gmail.com';
        $slug = 'apekindd@gmail.com';
        $name = 'apekindd';

        $business_name = 'apekindd@gmail.com';
        try {
//            Mail::send('emails.user-create', ['login' => $email, 'password' => $password, 'slug' => $slug], function ($message) use ($name, $email, $business_name) {
//                $message->to($email, $name)->subject(__('Доступ к BOTANIK - '.$business_name));
//            });

            $transport = new Swift_SmtpTransport('localhost', 25);
            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message('Доступ к BOTANIK' . $business_name))
                ->setFrom([env('MAIL_FROM_ADDRESS') => 'Some One'])
                ->setTo($email)
                ->setBody("123");

            $mailer->send($message);


        } catch (Exception $e) {
           echo "<pre>"; print_r($e->getMessage()); echo "</pre>"; die();
        }
        echo "<pre>"; print_r(111); echo "</pre>"; die();
        return view('root.analytic');
    }
}
