<?php

namespace App\Services\Telegram;

use App\Jobs\SendNotice;
use App\Jobs\TelegramFeedBack;
use App\Jobs\TelegramNotice;
use App\Models\Payment;
use App\Models\Record;
use App\Models\Service;
use App\Models\TelegramSession;
use Carbon\Carbon;
use ConnectService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;


class TelegramAPI
{
//    public BotApi $bot;
    public TelegramComponent $bot;
    public $user = false;
    public $package;
    public string $token;
    public ?string $pay_token;
    public int $chat_id;
    public int $message_id;
    public string $business_db;
    public array $menu = [
        [
            'Запись'
        ],
        [
            'Акции',
            'Отзывы',
            'О нас'
        ],
        [
            'Личный кабинет'
        ]
    ];
    public $result;

    /**
     * TelegramAPI constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // set telegram token
        $this->token = $request->input('token');

        // set object of telegram bot api
//        $this->bot = new BotApi($this->token);

        $this->bot = new TelegramComponent($this->token);

        // set pay token
        $this->pay_token = $request->input('pay_token') ?? null;
        // set business database name
        $this->business_db = $request->input('business_db');
        // set package of business
        $this->package = $request->input('package');
        // set client collection
        if ($request->has('client')) {
            $this->user = $request->input('client');
        }

   //     Log::debug($request->input());

        // set catalog
        if (!is_null($this->pay_token) && $request->has('catalog') && $request->input('catalog') == true) {
            $this->menu = [
                [
                    'Запись'
                ],
                [
                    'Каталог'
                ],
                [
                    'Акции',
                    'Отзывы',
                    'О нас'
                ],
                [
                    'Личный кабинет'
                ]
            ];
        }
    }


    /**
     * @param $text
     * @param null $keyboard
     * @param null $replyTo
     * @param string $parseMode
     * @param bool $disablePreview
     * @return false|string
     * @throws GuzzleException
     */


    public function sendMessage($text, $keyboard = null, $replyTo = null, string $parseMode = 'HTML', bool $disablePreview = false)
    {
        Log::alert('ChatId: '. $this->chat_id);
        if (! $keyboard){
            $keyboard = $this->buildReplyKeyboard($this->menu);
        }
        return $this->bot->sendMessage($this->chat_id, $text, $parseMode, $disablePreview, $replyTo, $keyboard);
    }


    /**
     * @param $photo
     * @param $text
     * @param null $keyboard
     * @param null $replyTo
     * @param string $parseMode
     * @param bool $disableNotification
     * @return false|string
     * @throws GuzzleException
     */
    public function sendPhoto($photo, $text, $keyboard = null, $replyTo = null, $parseMode = 'HTML', $disableNotification = false)
    {
        return $this->bot->sendPhoto($this->chat_id, $photo, $text, $replyTo, $keyboard, $disableNotification, $parseMode);
    }

    /**
     * @param $text
     * @param null $keyboard
     * @param string $parseMode
     * @param bool $disablePreview
     * @return mixed
     * @throws GuzzleException
     */
    public function editMessage($text, $keyboard = null, $parseMode = 'HTML', $disablePreview = false)
    {
        if (! $keyboard){
            $keyboard = $this->buildInlineKeyboard([]);
        }
        return $this->bot->editMessageText($this->chat_id, $this->message_id, $text, $parseMode, $disablePreview, $keyboard);
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    public function deleteMessage()
    {
        return $this->bot->deleteMessage($this->chat_id, $this->message_id);
    }

    public function buildReplyKeyboard($keys)
    {
        return ['keyboard' => $keys];
    }

    public function buildInlineKeyboard($keys)
    {
        return ['inline_keyboard' => $keys];
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        if (empty($this->user))
            return false;
        else
            return true;
    }

    /**

     */
    public function buildStars()
    {
        $star = hex2bin('E2AD90');
        return $this->buildInlineKeyboard(
            [[
                ['text' => $star, 'callback_data' => 'Stars_1'],
                ['text' => $star, 'callback_data' => 'Stars_2'],
                ['text' => $star, 'callback_data' => 'Stars_3'],
                ['text' => $star, 'callback_data' => 'Stars_4'],
                ['text' => $star, 'callback_data' => 'Stars_5']
            ]]
        );
    }

    /**
     * @param $text
     * @return false|string
     * @throws GuzzleException
     */
    public function getMenu($text)
    {
        return $this->sendMessage(
            $text,
            $this->buildReplyKeyboard($this->menu)
        );
    }

    /**
     * @return mixed
     */
    public function getData ()
    {
        return json_decode($this->user->telegramSession ? $this->user->telegramSession->data : '');
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function setData ($data)
    {
        if ($this->user->telegramSession) {
            $this->user->telegramSession->data = json_encode($data);
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->data = json_encode($data);
            $this->user->telegramSession()->save($session);
        }
        return $data;
    }

    /**
     * Set NULL value to 'data' column in 'telegram_sessions' table.
     */
    protected function resetData ()
    {
        $this->user->telegramSession->data = null;
        $this->user->telegramSession->save();
    }

    /**
     * @return int
     */
    public function getStars(): int
    {
        return $this->user->telegramSession->stars;
    }

    /**
     * @return int
     */
    public function getTypeID (): int
    {
        return $this->user->telegramSession->type;
    }

    /**
     * @return mixed
     */
    public function getServiceID ()
    {
        return $this->user->telegramSession->service;
    }

    /**
     * @return int
     */
    public function getAddressID (): int
    {
        return $this->user->telegramSession->address;
    }

    /**
     */
    public function getMasterID ()
    {
        return $this->user->telegramSession->master;
    }

    /**
     * @return mixed
     */
    public function getDate ()
    {
        return $this->user->telegramSession->date;
    }

    /**
     * @return mixed
     */
    public function getTime ()
    {
        return $this->user->telegramSession->time;
    }

    /**
     * @return int
     */
    public function getRecordID (): int
    {
        return $this->user->telegramSession->record;
    }

    /**
     * @param mixed ...$packages
     * @return bool
     */
    public function hasPackage(...$packages): bool
    {
        foreach ($packages as $package)
            if ($this->package == $package)
                return true;

        return false;
    }

    /**
     * @param bool $status
     * @param bool $online_pay
     * @param int $bonus
     * @return Record|Model|bool
     */
    protected function createRecord ($status = true, $online_pay = false, $bonus = 0)
    {
        try {
            $service = Service::find( $this->getServiceID() );
        } catch (\Exception $e) {
            return false;
        }
        $price = intval( $service->price );
        if ($bonus > 0) $price = $this->currentPrice($price, $bonus);

        $record = Record::create([
            'telegram_user_id' => $this->user->id,
            'service_id' => $this->getServiceID(),
            'address_id' => $this->getAddressID(),
            'user_id' => $this->getMasterID(),
            'time' => $this->getTime(),
            'date' => $this->getDate(),
            'status' => $status
        ]);

        Payment::create([
            'online_pay' => $online_pay,
            'money' => $price,
            'bonuses' => $bonus,
            'status' => $status,
            'refund' => Carbon::now()->addHours(3),
            'record_id' => $record->id
        ]);

        if ($record && $status) {
            $this->user->last_service = $service->id;
            if (isset($this->user->records) && !$this->user->records->isEmpty()) {
                $duplicates = $this->user->records->toBase()->duplicates('service_id');
                $max = ['count' => 0, 'id' => 0];
                foreach ($duplicates as $service_id) {
                    $current = $this->user->records->where('service_id', $service_id)->count();
                    if ($current > $max['count']) $max = ['count' => $current, 'id' => $service_id];
                }
                $this->user->favorite_service = $max['id'];
            } else {
                $this->user->favorite_service = $service->id;
            }
            if($bonus == 0 && $service->bonus && $online_pay == true){
                $this->user->bonus += $service->bonus;
                $this->user->save();
            }
            $this->createRecordNotice($service->name, $record->id);
            $this->groupMessage($service);
        }

        return $record;
    }

    private function groupMessage (Service $service)
    {
        if (!isset($service->group))
            return;

        $records = Record::where('service_id', $service->id)->where('date', $this->getDate())->where('time', $this->getTime());

        if ($records->count() < $service->group->quantity)
            return;

        foreach ($records as $record) {
            try {
                $this->bot->sendMessage(
                    $record->telegramUser->chat_id,
                    $service->group->message
                );
            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param $service_name
     * @param $record_id
     */
    private function createRecordNotice ($service_name, $record_id)
    {
        if (!ConnectService::prepareJob())
            return;

        /**
         * Admin & Master notice
         */
        $notice_mess = __('Новая запись на услугу').' <b>'.$service_name.'</b> от '.$this->user->first_name.' на '.$this->getDate(). ' в '.$this->getTime();
        SendNotice::dispatch(
            $this->business_db,
            [
                [
                    'address_id' => $this->getAddressID(),
                    'message' => $notice_mess
                ],
                [
                    'user_id' => $this->getMasterID(),
                    'message' => $notice_mess
                ]
            ],
            )->delay(now()->addMinutes(2));

        /*
         * Client notice
         */
        TelegramNotice::dispatch(
            $this->business_db,
            $this->chat_id,
            $record_id,
            __('Напоминание. Сегодня Вы записаны на услугу').' "'.$service_name.'". Начало в '.$this->getTime(),
            $this->getDate(),
            $this->getTime(),
            $this->token
        )->delay(Carbon::parse($this->getDate().$this->getTime())->subHour());

        /*
         * Client feedback
         */
        TelegramFeedBack::dispatch(
            $this->business_db,
            $this->chat_id,
            $record_id,
            $this->token
        )->delay(Carbon::parse($this->getDate().$this->getTime())->addDay());

        ConnectService::dbConnect($this->business_db);
    }

    /**
     * @param $price
     * @param $bonus
     * @return int
     */
    protected function currentPrice($price, $bonus) : int
    {
        if ($bonus >= $price) {
            $this->user->bonus -= $price;
            $this->user->save();
            return 0;
        } else {
            $this->user->bonus = 0;
            $this->user->save();
            return $price - $bonus;
        }
    }

}
