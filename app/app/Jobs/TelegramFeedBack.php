<?php

namespace App\Jobs;

use App\Facades\ConnectService;
use App\Models\Record;
use App\Models\TelegramSession;
use App\Models\TelegramUser;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class TelegramFeedBack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $chat_id;
    protected $record_id;
    protected $token;
    protected $db;

    /**
     * Create a new job instance.
     *
     * @param string $db
     * @param int $chat_id
     * @param int $record_id
     * @param string $token
     *
     * @return void
     */
    public function __construct($db, $chat_id, $record_id, $token)
    {
        $this->db = $db;
        $this->chat_id = $chat_id;
        $this->record_id = $record_id;
        $this->token = $token;
    }


    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        if (!ConnectService::dbConnect($this->db))
        {
            return;
        }

        $record = Record::find($this->record_id);
        if ($record && $record->status == true) {
            $star = hex2bin('E2AD90');
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [[
                    ['text' => $star, 'callback_data' => 'FeedBackText_1'],
                    ['text' => $star, 'callback_data' => 'FeedBackText_2'],
                    ['text' => $star, 'callback_data' => 'FeedBackText_3'],
                    ['text' => $star, 'callback_data' => 'FeedBackConfirm_4'],
                    ['text' => $star, 'callback_data' => 'FeedBackConfirm_5']
                ]]
            );
            $user = TelegramUser::where('chat_id', $this->chat_id)->first();
            if (!isset($user->telegramSession)) {
                $session = new TelegramSession();
                $session->record = $this->record_id;
                $user->telegramSession()->save($session);
            }
            else {
                $user->telegramSession->record = $this->record_id;
                $user->telegramSession->save();
            }

            $bot = new \TelegramBot\Api\BotApi($this->token);
            $bot->sendMessage(
                $this->chat_id,
                __(Carbon::parse(now()) . ' / ' . Carbon::parse(now())->addMinutes(2) . ' Оцените услугу "'.$record->service->name.'"'),
                null,
                false,
                null,
                $keyboard
            );
        }
    }
}
