<?php

namespace App\Services\Telegram;


use \GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


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

    public function __construct(Request $request)
    {
        // set telegram token
        $this->botToken = $request->input('token');
        $this->client = new Client();

    }

    public function buildReplyKeyboard($keys): array
    {
        return [
            'reply_markup' => json_encode([
                'keyboard' => $keys,
            ]),
        ];
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
     * @param $chat_id integer
     * @param $text string
     * @param $params array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMessage($chat_id, $text, $menu): string
    {
        $data = array_merge([
            'chat_id'    => $chat_id,
            'text'       => $text,
            'parse_mode' => 'html',
        ], (array)$menu );

        $response = $this->client->request('post', $this->url . $this->botToken . '/' . self::ACTION_SEND_MESSAGE,
            [
                'form_params' => $data
            ]);

        Log::debug($response->getBody()->getContents());
        return $response->getBody()->getContents();

    }

    /**
     * @param $chat_id integer
     * @param $message_id integer
     * @param $text string
     * @param array $params
     * @return bool|mixed
     */
    public function updateMessage($chat_id, $message_id, $text)
    {

        $data = [
            'chat_id'    => $chat_id,
            'text'       => $text,
            'message_id' => $message_id,
            'parse_mode' => 'html',
        ];

        $response = $this->client->request('post', $this->url . $this->botToken . '/' . self::ACTION_UPDATE_MESSAGE_TEXT,
            [
                'form_params' => $data
            ]);

        Log::debug($response->getBody()->getContents());
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
     * Создание запроса
     */
    private function getRequest(): Client
    {

        return new Client();
    }

}


