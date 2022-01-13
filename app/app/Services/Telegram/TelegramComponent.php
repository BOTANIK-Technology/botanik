<?php

namespace App\Services\Telegram;


use \GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;


/**
 * Class TelegramAlertComponent
 * @package common\components
 *
 * @property string $botToken
 * @property string $botLink
 * @property string $url
 */
class TelegramComponent
{
    /**
     * action для отправки сообщений
     */
    const ACTION_SEND_MESSAGE = 'sendMessage';
    const ACTION_SEND_PHOTO = 'sendPhoto';
    const ACTION_SEND_INVOICE = 'sendInvoice';
    const DELETE_MESSAGE = 'deleteMessage';

    const ACTION_UPDATE_MESSAGE_TEXT = 'editMessageText';

    const ACTION_SET_WEBHOOK = 'setWebhook';

    const ACTION_GET_ME = 'getMe';

    const ACTION_GET_WEBHOOK_INFO = 'getWebhookInfo';

    const ACTION_ANSWER_CALLBACK_QUERY = 'answerCallbackQuery';

    const ACTION_SET_MY_COMMANDS = 'setMyCommands';
    /**
     * @var string токен телеграм бота
     */
    public $botToken;
    /**
     * @var string ссылка не телеграм бота
     */
    public $botLink;
    /**
     * @var string базовый url апи телеграма
     */
    private $url = 'https://api.telegram.org/bot';

    public array $menu = [
        [
            'Запись',
        ],
        [
            'Акции',
            'Отзывы',
            'О нас',
        ],
        [
            'Личный кабинет',
        ],
    ];

    private $client;

    public function __construct($token)
    {
        // set telegram token
        $this->botToken = $token;
        $this->client = new Client();

    }


    /**
     * Установка токена бота
     * @param $token string
     * @return $this
     */
    public function setBotToken(string $token): TelegramComponent
    {

        $this->botToken = $token;
        return $this;
    }

    public function checkToken()
    {

        if (!empty($this->botToken)) {
            return $this;
        }
        return false;
    }

    /**
     * Установка веб хука
     * @param $url
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function setWebHook($url)
    {
        $response = $this->sendRequest(self::ACTION_SET_WEBHOOK, ['url' => $url]);
        if ($response->ok) {
            return $response->result['data'] ?? $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;
    }

    /**
     * Получение информации о боте
     * @return mixed
     * @throws GuzzleException
     */
    public function getMe()
    {
        $response = $this->sendRequest(self::ACTION_GET_ME);

        if ($response->ok) {
            return $response->result['data'] ?? $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;
    }

    /**
     * Получение информации о веб хуке
     * @return mixed
     * @throws GuzzleException
     */
    public function getWebhookInfo()
    {

        $response = $this->sendRequest(self::ACTION_GET_WEBHOOK_INFO);
        if ($response->ok) {
            return $response->result['data'] ?? $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;


    }

    /**
     * Отправка пользователю уведомления в телеграм
     * @param $chatId
     * @param $text string
     * @param null $parseMode
     * @param bool $disablePreview
     * @param null $replyToMessageId
     * @param null $replyMarkup
     * @param bool $disableNotification
     * @return mixed
     * @throws GuzzleException
     */
    public function sendMessage(
        $chatId,
        string $text,
        $parseMode = null,
        bool $disablePreview = false,
        $replyToMessageId = null,
        $replyMarkup = null,
        bool $disableNotification = false
    )
    {
        $data = [
            'chat_id'                  => $chatId,
            'text'                     => $text,
            'parse_mode'               => $parseMode,
            'disable_web_page_preview' => $disablePreview,
            'reply_to_message_id'      => (int)$replyToMessageId,
            'reply_markup'             => json_encode($replyMarkup),
            'disable_notification'     => $disableNotification,
        ];
        $response = $this->sendRequest(self::ACTION_SEND_MESSAGE, $data);

        if ($response->ok) {
            return $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;

    }

    /**
     * Отправка пользователю уведомления в телеграм
     * @param $chatId
     * @param $photo
     * @param null $caption
     * @param null $replyToMessageId
     * @param null $replyMarkup
     * @param bool $disableNotification
     * @param null $parseMode
     * @return mixed
     * @throws GuzzleException
     */
    public function sendPhoto(
        $chatId,
        $photo,
        $caption = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        bool $disableNotification = false,
        $parseMode = null
    )
    {
        $data = [
            'chat_id'              => $chatId,
            'photo'                => $photo,
            'caption'              => $caption,
            'reply_to_message_id'  => $replyToMessageId,
            'reply_markup'         => json_encode($replyMarkup),
            'disable_notification' => (bool)$disableNotification,
            'parse_mode'           => $parseMode,
        ];
        $response = $this->sendRequest(self::ACTION_SEND_PHOTO, $data);
        if ($response->ok) {
            return $response->result;
        }
        Log::error(self::ACTION_SEND_PHOTO, $response->result);
        return false;
    }

    /**
     * Use this method to edit text messages sent by the bot or via the bot
     *
     * @param int|string $chatId
     * @param int $messageId
     * @param string $text
     * @param string $inlineMessageId
     * @param string|null $parseMode
     * @param bool $disablePreview
     * @throws GuzzleException
     */
    public function editMessageText(
        $chatId,
        $messageId,
        $text,
        $parseMode = null,
        $disablePreview = false,
        $replyMarkup = null,
        $inlineMessageId = null
    )
    {
        $response = $this->sendRequest(self::ACTION_UPDATE_MESSAGE_TEXT, [
            'chat_id'                  => $chatId,
            'message_id'               => $messageId,
            'text'                     => $text,
            'inline_message_id'        => $inlineMessageId,
            'parse_mode'               => $parseMode,
            'disable_web_page_preview' => $disablePreview,
            'reply_markup'             => json_encode($replyMarkup),
        ]);
        if ($response->ok) {
            return $response->result;
        }
        Log::error(self::ACTION_UPDATE_MESSAGE_TEXT, (array)$response->result);
        return false;
    }


    /**
     * Use this method to delete a message, including service messages, with the following limitations:
     *  - A message can only be deleted if it was sent less than 48 hours ago.
     *  - Bots can delete outgoing messages in groups and supergroups.
     *  - Bots granted can_post_messages permissions can delete outgoing messages in channels.
     *  - If the bot is an administrator of a group, it can delete any message there.
     *  - If the bot has can_delete_messages permission in a supergroup or a channel, it can delete any message there.
     *
     * @param int|string $chatId
     * @param int $messageId
     *
     * @throws GuzzleException
     */
    public function deleteMessage($chatId, int $messageId)
    {
        try {
            return $this->sendRequest(self::DELETE_MESSAGE, [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
            ]);
        }
        catch (Exception $e) {
            Log::error($e->getMessage(), [
                'method'  => self::DELETE_MESSAGE,
                'chat ID' => $chatId,
                'mess ID' => $messageId,
            ]);
        }
    }

    /**
     * Use this method to send invoices.
     *
     * @param int|string $chatId
     * @param string $title
     * @param string $description
     * @param string $payload
     * @param string $providerToken
     * @param string $startParameter
     * @param string $currency
     * @param array $prices
     * @param string|null $photoUrl
     * @param int|null $photoSize
     * @param int|null $photoWidth
     * @param int|null $photoHeight
     * @param bool $needName
     * @param bool $needPhoneNumber
     * @param bool $needEmail
     * @param bool $needShippingAddress
     * @param bool $isFlexible
     * @param int|null $replyToMessageId
     * @param bool $disableNotification
     * @param string|null $providerData
     * @param bool $sendPhoneNumberToProvider
     * @param bool $sendEmailToProvider
     *
     * @throws GuzzleException
     */
    public function sendInvoice(
        $chatId,
        $title,
        $description,
        $payload,
        $providerToken,
        $startParameter,
        $currency,
        $prices,
        $isFlexible = false,
        $photoUrl = null,
        $photoSize = null,
        $photoWidth = null,
        $photoHeight = null,
        $needName = false,
        $needPhoneNumber = false,
        $needEmail = false,
        $needShippingAddress = false,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false,
        $providerData = null,
        $sendPhoneNumberToProvider = false,
        $sendEmailToProvider = false
    )
    {
        $data = [
            'chat_id'                       => $chatId,
            'title'                         => $title,
            'description'                   => $description,
            'payload'                       => $payload,
            'provider_token'                => $providerToken,
            'start_parameter'               => $startParameter,
            'currency'                      => $currency,
            'prices'                        => json_encode($prices),
            'is_flexible'                   => $isFlexible,
            'photo_url'                     => $photoUrl,
            'photo_size'                    => $photoSize,
            'photo_width'                   => $photoWidth,
            'photo_height'                  => $photoHeight,
            'need_name'                     => $needName,
            'need_phone_number'             => $needPhoneNumber,
            'need_email'                    => $needEmail,
            'need_shipping_address'         => $needShippingAddress,
            'reply_to_message_id'           => $replyToMessageId,
            'disable_notification'          => (bool)$disableNotification,
            'provider_data'                 => $providerData,
            'send_phone_number_to_provider' => (bool)$sendPhoneNumberToProvider,
            'send_email_to_provider'        => (bool)$sendEmailToProvider,
        ];
        if ($replyMarkup) {
            $data['reply_markup'] = '';
        }
        return $this->sendRequest(self::ACTION_SEND_INVOICE, $data);
    }


    /**
     * Установка команд
     * @param array $commands массив https://core.telegram.org/bots/api/#botcommand
     * Пример:
     * ```php
     * [
     *      [
     *          'command' => 'command',
     *          'description' => 'description of command',
     *      ],
     * ]
     * ```
     * @return mixed
     * @throws GuzzleException
     */
    public function setMyCommands($commands)
    {
        $response = $this->sendRequest(self::ACTION_SET_MY_COMMANDS, ['commands' => json_encode($commands)]);

        if ($response->ok) {
            return $response->result;
        }
        Log::error(self::ACTION_SET_MY_COMMANDS, (array)$response->result);
        return false;
    }

    /**
     * Ответ на callback query в виде всплывающего окна
     * @param $callback_query_id integer
     * @param null|string $text
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function answerCallbackQuery($callback_query_id, $text = null, $params = [])
    {


        $response = $this->sendRequest(self::ACTION_ANSWER_CALLBACK_QUERY, array_merge([
            'callback_query_id' => $callback_query_id,
            'text'              => $text,
        ], $params));

        if ($response->ok) {
            return $response->result['data'] ?? $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;
    }

    /**
     * Отправка пользователю сообщения с клавиатурой
     * @param $chat_id integer
     * @param $text string
     * @param $buttons array Массив массивов InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
     * Пример:
     * ```php
     * [
     *      [
     *          ['text' => 'button_text', 'callback_data' => 'callback_value'],
     *      ],
     * ]
     * ```
     * @return mixed
     * @throws GuzzleException
     */
    public function sendInlineKeyboard($chat_id, $text, $buttons)
    {

        $response = $this->sendMessage($chat_id, $text, [
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons,
            ]),
        ]);
        if ($response->ok) {
            return $response->result;
        }
        Log::error(self::ACTION_SEND_MESSAGE, $response->result);
        return false;
    }

    /**
     * Изменение сообщения с клавиатурой
     * @param $chat_id integer
     * @param $message_id integer
     * @param $text string
     * @param $buttons array Массив массивов InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
     * Пример:
     * ```php
     * [
     *      [
     *          ['text' => 'button_text', 'callback_data' => 'callback_value'],
     *      ],
     * ]
     * ```
     * @return mixed
     * @throws GuzzleException
     */
    public function updateInlineKeyboard($chat_id, $message_id, $text, $buttons = [])
    {

        $this->editMessageText($chat_id, $message_id, $text, [
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons,
            ]),
        ]);
    }

    /**
     * Отправка запроса
     * @throws GuzzleException
     */
    private function sendRequest($method, $data = []): Response
    {
        $response = new Response();
        try {
            $resp = $this->client->request('post', $this->url . $this->botToken . '/' . $method,
                [
                    'form_params' => $data,
                ]);
            $res = json_decode($resp->getBody()->getContents());
            if ($resp->getStatusCode() == 200) {
                $response->result = (array)$res->result;
            }
            else {
                Log::error($method . ' Error: ' . $res->getBody()->getContents(), $data);
                $response->ok = false;
                $response->result = (array)$res;
            }
        }
        catch (\Exception $e) {
            Log::error($method . ' ErrorExeption: ' . $e->getMessage(), $data);
            $response->ok = false;
            $response->result = $data;
        }
        return $response;
    }

}


