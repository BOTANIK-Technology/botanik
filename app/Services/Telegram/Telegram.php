<?php

namespace App\Services\Telegram;

use Illuminate\Http\Request;
use App\Models\TelegramUser;

class Telegram
{

    private $nsCommands = 'App\\Services\\Telegram\\Commands\\';
    private $nsCallback = 'App\\Services\\Telegram\\Callback\\';

    /**
     * @param Request $request
     * @return bool
     */
    public function doCommand(Request $request) {

        $dir = dirname(__FILE__).'/Commands/';
        $files = scandir($dir);

        $command = substr($request->input('message.text'), 1);
        $command = ucfirst($command);

        foreach ($files as $file) {

            if ($file == $command.'.php') {
                $class = $this->nsCommands.$command;
                new $class($request);
                return true;
            }
        }

        return false;

    }

    /**
     * @param Request $request
     * @return void
     */
    public function confirmPay(Request $request) {
        $payload = $request->input('pre_checkout_query.invoice_payload');
        $class = substr($payload, 0, strpos($payload, '_'));
        new $class($request, $payload);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function textCommand(Request $request) {
        switch ($request->input('message.text')) {
            case 'Запись':
                $class = $this->nsCommands.'TypesOfServices';
                new $class($request);
            break;
            case 'Каталог':
                $class = $this->nsCommands.'Catalog';
                new $class($request);
            break;
            case 'Акции':
                $class = $this->nsCommands.'Shares';
                new $class($request);
            break;
            case 'Отзывы':
                $class = $this->nsCommands.'Review';
                new $class($request);
            break;
            case 'О нас':
                $class = $this->nsCommands.'AboutUs';
                new $class($request);
            break;
            case 'Личный кабинет':
                $class = $this->nsCommands.'Personal';
                new $class($request);
            break;
            default:
                $class = $this->nsCommands.'Text';
                new $class($request);
            break;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function doButton(Request $request) {
        if ($request->input('callback_query.data') == '-')
            return false;
        $data = $request->input('callback_query.data');
        $class = $this->nsCallback.substr($data, 0, strpos($data, '_'));
        $a = new $class($request);
        return response()->json($a->result, 200);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function getNumber(Request $request) {
        if ($request->has('client'))
            return $this->redirectToStart($request);

        $client = TelegramUser::create(
            [
                'chat_id'    => $request->input('message.chat.id'),
                'first_name' => $request->input('message.chat.first_name'),
                'last_name'  => $request->input('message.chat.last_name'),
                'username'   => $request->input('message.chat.username'),
                'phone'      => $request->input('message.contact.phone_number'),
            ]
        );
        if ($client) {
            $request->merge(['client' => $client]);
            return $this->redirectToStart($request);
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function redirectToStart(Request $request)
    {
        $class = $this->nsCommands.'Start';
        new $class($request);
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function rpcAdmin (Request $request)
    {
        $method = $request->input('call');

        if ( !method_exists(RPC::class, $method) )
            return false;

        return RPC::$method($request);
    }
}