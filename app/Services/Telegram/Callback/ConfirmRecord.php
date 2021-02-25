<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class ConfirmRecord extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $time = parent::setTime();
        $this->back = 'Time_'.parent::getDate();
        parent::editMessage($this->getText($time), $this->getButtons());
    }

    private function getText($time) {
        if (is_null(parent::getMasterID()))
            return
                'Оформление записи:'."\n".
                '1. Услуга: '.\App\Models\Service::find( $this->getServiceID() )->name."\n".
                '2. Адрес: '.\App\Models\Address::find( $this->getAddressID() )->address."\n".
                '3. Дата: '.Date::parse( parent::getDate() )->format('l j F Y')."\n".
                '4. Время: '.$time."\n".
                '5. Стоимость: '.\App\Models\Service::find( parent::getServiceID() )->price.' грн'."\n\n".
                'Оплата:';
        return
            'Оформление записи:'."\n".
            '1. Услуга: '.\App\Models\Service::find( $this->getServiceID() )->name."\n".
            '2. Адрес: '.\App\Models\Address::find( $this->getAddressID() )->address."\n".
            '3. Специалист: '.\App\Models\User::find( $this->getMasterID() )->name."\n".
            '4. Дата: '.Date::parse( parent::getDate() )->format('l j F Y')."\n".
            '5. Время: '.$time."\n".
            '6. Стоимость: '.\App\Models\Service::find( parent::getServiceID() )->price.' грн'."\n\n".
            'Оплата:';
    }

    private function getButtons () {

        if ( parent::hasPackage('pro', 'base') && !empty($this->pay_token) && !is_null($this->pay_token) )
            $buttons[] = [['text' => 'Онлайн','callback_data' => 'OnlinePay_']];

        $buttons[] = [['text' => 'На месте','callback_data' => 'CashPay_']];

        if ( parent::hasPackage('pro') )
            $buttons[] = [['text' => 'Бонусами','callback_data' => 'BonusPay_']];

        return parent::buildInlineKeyboard($buttons);

    }


}