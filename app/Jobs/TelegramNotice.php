<?php

namespace App\Jobs;

use App\Facades\ConnectService;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;


class TelegramNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chat_id;
    protected $record_id;
    protected $message;
    protected $date;
    protected $time;
    protected $token;
    protected $db;

    /**
     * Create a new job instance.
     *
     * @param string $db
     * @param int $chat_id
     * @param int $record_id
     * @param string $message
     * @param string $date
     * @param string $time
     * @param string $token
     */
    public function __construct($db, $chat_id, $record_id, $message, $date, $time, $token)
    {
        $this->db = $db;
        $this->chat_id = $chat_id;
        $this->record_id = $record_id;
        $this->message = $message;
        $this->date = $date;
        $this->time = $time;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function handle()
    {

        if (!ConnectService::dbConnect($this->db)) {
            return;
        }


        $bot = new \TelegramBot\Api\BotApi($this->token);
        $bot->sendMessage($this->chat_id, $this->message);

    }
}
