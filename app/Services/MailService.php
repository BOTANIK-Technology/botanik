<?php
namespace App\Services;

use App\Models\TelegramUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MailService
{

    public static function sendReviewCreate($business, $user_id, $stars, $text)
    {
        $owner = DB::table('users_roles')->where('role_id', 1)->first();
        $owner = User::find($owner->user_id);
        $email = $owner->email;
        $user = TelegramUser::find($user_id);
        $subject = 'Отзыв на сайте BOTANIK - ' . $business;
        $body = view('emails.review-create', ['user_name'=> $user->first_name . $user->phone, 'rate' => $stars, 'message' => $text]);
        self::sendMail($owner->email, $subject, $body);
    }

    public static function sendMail($email, $subject, $body, $contentType = 'text/html')
    {

        $transport = new Swift_SmtpTransport('localhost', 25);
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message($subject))
            ->setFrom(env('MAIL_FROM_ADDRESS'))
            ->setTo($email)
            ->setBody($body, $contentType);

        $mailer->send($message);
    }


}
