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
        return view('root.analytic');
    }
}
