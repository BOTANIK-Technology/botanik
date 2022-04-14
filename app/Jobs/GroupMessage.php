<?php

namespace App\Jobs;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Record;
use ConnectService;

class GroupMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $service_id;
    protected $date;
    protected $time;
    protected $token;
    protected $db;

    /**
     * Create a new job instance.
     *
     * @param string $db
     * @param int $service_id
     * @param string $date
     * @param string $time
     * @param string $token
     *
     * @return void
     */
    public function __construct($db, $service_id, $date, $time, $token)
    {
        $this->db = $db;
        $this->service_id = $service_id;
        $this->date = $date;
        $this->time = $time;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     *
     * @return void
     */
    public function handle()
    {
        if (!ConnectService::dbConnect($this->db))
            return;

        $service = Service::find($this->service_id);
        $records = Record::where('service_id', $this->service_id)->where('date', $this->date)->where('time', $this->time);
        $bot = new \TelegramBot\Api\BotApi($this->token);
        foreach ($records as $record) {
            $bot->sendMessage(
                $record->telegramUser->chat_id,
                $record->service->group->message
            );
        }
    }
}
