<?php

namespace App\Services\Telegram;

use Illuminate\Http\Request;
use \TelegramBot\Api\BotApi;

class TelegramAPI
{
    public $bot;
    public $user = false;
    public $package;
    public $token;
    public $pay_token;
    public $chat_id;
    public $message_id;
    public $business_db;
    public $menu = [
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
        $this->bot = new BotApi($this->token);
        // set pay token
        $this->pay_token = $request->input('pay_token');
        // set business database name
        $this->business_db = $request->input('business_db');
        // set package of business
        $this->package = $request->input('package');
        // set client collection
        if ($request->has('client')) $this->user = $request->input('client');
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
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function sendMessage($text, $keyboard = null, $replyTo = null, $parseMode = 'HTML', $disablePreview = false)
    {
        return $this->bot->sendMessage($this->chat_id, $text, $parseMode, $disablePreview, $replyTo, $keyboard);
    }

    /**
     * @param $photo
     * @param $text
     * @param null $keyboard
     * @param null $replyTo
     * @param string $parseMode
     * @param bool $disableNotification
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
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
     * @return \TelegramBot\Api\Types\Message
     */
    public function editMessage($text, $keyboard = null, $parseMode = 'HTML', $disablePreview = false)
    {
        return $this->bot->editMessageText($this->chat_id, $this->message_id, $text, $parseMode, $disablePreview, $keyboard);
    }

    /**
     * @return bool
     */
    public function deleteMessage()
    {
        return $this->bot->deleteMessage($this->chat_id, $this->message_id);
    }

    /**
     * @param array $buttons
     * @param bool $oneTime
     * @return \TelegramBot\Api\Types\ReplyKeyboardMarkup
     */
    public function buildReplyKeyboard($buttons = [], $oneTime = true)
    {
        return $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, $oneTime);
    }

    /**
     * @param array $buttons
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    public function buildInlineKeyboard($buttons = [])
    {
        return $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup($buttons);
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        if (empty($this->user))
            return false;
        else
            return true;
    }

    /**
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
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
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
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
        return json_decode($this->user->telegramSession->data) ?? false;
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
            $session = new \App\Models\TelegramSession();
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
    public function getStars()
    {
        return $this->user->telegramSession->stars;
    }

    /**
     * @return int
     */
    public function getTypeID ()
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
    public function getAddressID ()
    {
        return $this->user->telegramSession->address;
    }

    /**
     * @return int
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
    public function getRecordID ()
    {
        return $this->user->telegramSession->record;
    }

    /**
     * @param mixed ...$packages
     * @return bool
     */
    public function hasPackage(...$packages)
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
     * @return \App\Models\Record|\Illuminate\Database\Eloquent\Model|bool
     */
    protected function createRecord ($status = true, $online_pay = false, $bonus = 0)
    {
        try {
            $service = \App\Models\Service::find( $this->getServiceID() );
        } catch (\Exception $e) {
            return false;
        }
        $price = intval( $service->price );
        if ($bonus > 0) $price = $this->currentPrice($price, $bonus);

        $record = \App\Models\Record::create([
            'telegram_user_id' => $this->user->id,
            'service_id' => $this->getServiceID(),
            'address_id' => $this->getAddressID(),
            'user_id' => $this->getMasterID(),
            'time' => $this->getTime(),
            'date' => $this->getDate(),
            'status' => $status
        ]);

        \App\Models\Payment::create([
            'online_pay' => $online_pay,
            'money' => $price,
            'bonuses' => $bonus,
            'status' => $status,
            'refund' => \Carbon\Carbon::now()->addHours(3),
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
            if($bonus == 0 && $service->bonus)
                $this->user->bonus += $service->bonus;
            $this->user->save();
            $this->createRecordNotice($service->name, $record->id);
            $this->groupMessage($service);
        }

        return $record;
    }

    private function groupMessage (\App\Models\Service $service)
    {
        if (!isset($service->group))
            return;

        $records = \App\Models\Record::where('service_id', $service->id)->where('date', $this->getDate())->where('time', $this->getTime());

        if ($records->count() < $service->group->quantity)
            return;

        foreach ($records as $record) {
            $this->bot->sendMessage(
                    $record->telegramUser->chat_id,
                    $service->group->message
            );
        }
    }

    /**
     * @param $service_name
     * @param $record_id
     */
    private function createRecordNotice ($service_name, $record_id)
    {
        if (!\ConnectService::prepareJob())
            return;

        /**
         * Admin & Master notice
         */
        $notice_mess = __('Новая запись на услугу').' <b>'.$service_name.'</b> от '.$this->user->first_name.' на '.$this->getDate(). ' в '.$this->getTime();
        \App\Jobs\SendNotice::dispatch(
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
        \App\Jobs\TelegramNotice::dispatch(
            $this->business_db,
            $this->chat_id,
            $record_id,
            __('Напоминание. Сегодня Вы записаны на услугу').' "'.$service_name.'". Начало в '.$this->getTime(),
            $this->getDate(),
            $this->getTime(),
            $this->token
        )->delay(\Carbon\Carbon::parse($this->getDate().$this->getTime())->subHour());

        /*
         * Client feedback
         */
        \App\Jobs\TelegramFeedBack::dispatch(
            $this->business_db,
            $this->chat_id,
            $record_id,
            $this->token
        )->delay(\Carbon\Carbon::parse($this->getDate().$this->getTime())->addDay());

        \ConnectService::dbConnect($this->business_db);
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
