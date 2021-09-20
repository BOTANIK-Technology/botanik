<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use App\Services\Telegram\Commands\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Facades\TelegramService;

class TelegramController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function main(Request $request)
    {
        $id = null;
        if ($request->has('message.chat.id')) {
            $id = $request->input('message.chat.id');
        }
        else if ($request->has('callback_query.message.chat.id')) {
            $id = $request->input('callback_query.message.chat.id');
        }
        else if ($request->has('pre_checkout_query.from.id')) {
            $id = $request->input('pre_checkout_query.from.id');
        }

        if ($client = TelegramUser::where('chat_id', $id)->first()) {
            if ($client->status) {
                $request->merge(['client' => $client]);
            }
            else {
               Log::info($id . ': Клиент неактивный');
                abort(404);
            }
        }
        else {
            Log::debug($id . ': Клиент не создан');
        }


        if ($request->has('callback_query')) {

            return $this->button($request);

        }
        else if ($request->has('message.entities')) {

            return $this->command($request);

        }
        else if ($request->has('pre_checkout_query')) {

            return $this->pay($request);

        }
        else if ($request->has('message.contact.phone_number')) {

            return $this->number($request);

        }
        else {

            if ($request->has('message.text')) {
                return $this->text($request);
            }

            return abort(404);

        }
    }

    public function pay($request)
    {
        return TelegramService::confirmPay($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function admin(Request $request)
    {
        if (!$request->has('gess_key') || ($request->input('gess_key') !== env('APP_KEY')))
            return abort('404');
        return TelegramService::rpcAdmin($request);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function text(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::textCommand($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function command(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::doCommand($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function button(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::doButton($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function number(Request $data)
    {
        return TelegramService::getNumber($data);
    }
}
