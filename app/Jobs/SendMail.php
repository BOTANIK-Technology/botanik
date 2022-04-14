<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $slug;
    protected $email;
    protected $password;
    protected $name;
    protected $business_name;

    /**
     * Create a new job instance.
     *
     * @param string $slug
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $business_name
     *
     * @return void
     */
    public function __construct(string $slug, string $email, string $password, string $name, string $business_name)
    {
        $this->slug = $slug;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->business_name = $business_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $body = view('emails.user-create', ['login'=> $this->email, 'password' => $this->password, 'slug' => $this->slug]);
        $transport = new Swift_SmtpTransport('localhost', 25);
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message('Доступ к BOTANIK - ' . $this->business_name))
            ->setFrom([env('MAIL_FROM_ADDRESS') => 'Some One'])
            ->setTo($this->email)
            ->setBody($body);

        $mailer->send($message);
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed (Exception $exception)
    {
        \Log::warning('Cannot send message to '.$this->email.'. Business: '.$this->business_name.'. ErrorMessage: '.$exception->getMessage());
    }
}
