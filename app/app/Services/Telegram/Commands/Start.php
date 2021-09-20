<?php

namespace App\Services\Telegram\Commands;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Start extends Command
{

    public function __construct(Request $request) {
        Log::debug('Start: ' . var_export($request->toArray(), true ) );
        parent::__construct($request);
        if ( parent::isUser() )
            return $this->redirect();
        return $this->registration();
    }

    private function registration() {
        $this->sendMessage(
            __('Чтобы пройти регистрацию, поделитесь, пожалуйста, своим номером.'),
            $this->buildReplyKeyboard([[['request_contact' => true, 'text' => 'Поделиться']]])
        );
    }

    private function redirect() {
//        return parent::getMenu(__('Вы авторизованы'));
        return parent::sendMessage(__('Вы авторизованы'));
    }
}
