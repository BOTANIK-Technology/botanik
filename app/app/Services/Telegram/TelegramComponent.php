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
    public function setBotToken($token)
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
     */
    public function setWebHook($url)
    {

        $client = $this->getRequest();
        $response = $client->setUrl(self::ACTION_SET_WEBHOOK)
            ->setData([
                'url' => $url,
            ])
            ->send();

        if ($response->isOk) {

            return $response->data;
        }

        return false;
    }

    /**
     * Получение информации о боте
     * @return bool|mixed
     */
    public function getMe()
    {

        $client = $this->getRequest();
        $response = $client->setUrl(self::ACTION_GET_ME)
            ->send();

        if ($response->isOk) {
            //    Yii::info(print_r($response->data, true));
            return $response->data;
        }
        //else Yii::info(print_r($response->content, true));

        return false;
    }

    /**
     * Получение информации о веб хуке
     * @return bool|mixed
     */
    public function getWebhookInfo()
    {

        $client = $this->getRequest();

        $response = $client->setUrl(self::ACTION_GET_WEBHOOK_INFO)
            ->send();


        return $response->data;


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
     * @return string
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
     * @return string
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
        $response =  $this->sendRequest(self::ACTION_UPDATE_MESSAGE_TEXT, [
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
        Log::error(self::ACTION_UPDATE_MESSAGE_TEXT, $response->result);
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
     * @return bool
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
        catch (Exception $e){
            Log::error($e->getMessage(),[
                'method' => self::DELETE_MESSAGE,
                'chat ID' => $chatId,
                'mess ID' => $messageId] );
        }
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
     * @return bool|mixed
     */
    public function setMyCommands($commands)
    {
        $response = $this->getRequest()->setUrl(self::ACTION_SET_MY_COMMANDS)
            ->setData([
                'commands' => json_encode($commands),
            ])
            ->send();

        if ($response->isOk) {
//            Yii::info(print_r($response->data, true));
            return $response->data;
        }
//        else Yii::info(print_r($response->content, true));

        return false;
    }

    /**
     * Ответ на callback query в виде всплывающего окна
     * @param $callback_query_id integer
     * @param null|string $text
     * @param array $params
     * @return bool|mixed
     */
    public function answerCallbackQuery($callback_query_id, $text = null, $params = [])
    {


        $response = $this->getRequest()->setUrl(self::ACTION_ANSWER_CALLBACK_QUERY)
            ->setData(array_merge([
                'callback_query_id' => $callback_query_id,
                'text'              => $text,
            ], $params))
            ->send();

        if ($response->isOk) {
//            Yii::info(print_r($response->data, true));
            return $response->data;
        }
//        else Yii::info(print_r($response->content, true));

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
     */
    public function sendInlineKeyboard($chat_id, $text, $buttons)
    {

        $this->sendMessage($chat_id, $text, [
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons,
            ]),
        ]);
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
     */
    public function updateInlineKeyboard($chat_id, $message_id, $text, $buttons = [])
    {

        $this->updateMessage($chat_id, $message_id, $text, [
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons,
            ]),
        ]);
    }

    /**
     * Отправка запроса
     * @throws GuzzleException
     */
    private function sendRequest($method, $data)
    {
        $res = $this->client->request('post', $this->url . $this->botToken . '/' . $method,
            [
                'form_params' => $data,
            ]);

        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody()->getContents());
        }
        throw new \HttpException('HTTP ERROR', $res->getStatusCode());
    }

}


