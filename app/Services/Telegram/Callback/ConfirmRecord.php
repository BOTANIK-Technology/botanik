<?php

namespace App\Services\Telegram\Callback;

use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class ConfirmRecord extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $time = parent::setTime();
        $this->back = 'Time_'.parent::getDate();
        parent::editMessage($this->getText($time), $this->getButtons());
    }

    private function getText($time): string
    {
        if (is_null(parent::getMasterID()))
            return
                'Оформление записи:'."\n".
                '1. Услуга: '.\App\Models\Service::find( $this->getServiceID() )->name."\n".
                '2. Адрес: '.\App\Models\Address::find( $this->getAddressID() )->address."\n".
                '3. Дата: '.Date::parse( parent::getDate() )->format('l j F Y')."\n".
                '4. Время: '.$time."\n".
                '5. Стоимость: '.\App\Models\Service::find( parent::getServiceID() )->price.' грн'."\n\n".
                '💳 Оплата:';
        return
            'Оформление записи:'."\n".
            '1. Услуга: '.\App\Models\Service::find( $this->getServiceID() )->name."\n".
            '2. Адрес: '.\App\Models\Address::find( $this->getAddressID() )->address."\n".
            '3. Специалист: '. User::find( $this->getMasterID() )->name."\n".
            '4. Дата: '.Date::parse( parent::getDate() )->format('l j F Y')."\n".
            '5. Время: '.$time."\n".
            '6. Стоимость: '.\App\Models\Service::find( parent::getServiceID() )->price.' грн'."\n\n".
            '💳 Оплата:';
    }

    /**
     */
    private function getButtons()
    {
        $service = \App\Models\Service::find(parent::getServiceID());

        if ( parent::hasPackage('pro', 'base') && !empty($this->pay_token) && !is_null($this->pay_token) && $service->online_pay)
            $buttons[] = [['text' => 'Онлайн','callback_data' => 'OnlinePay_']];

        if ( $service->cash_pay )
            $buttons[] = [['text' => 'На месте','callback_data' => 'CashPay_']];

        if ( parent::hasPackage('pro') && $service->bonus_pay)
            $buttons[] = [['text' => 'Бонусами','callback_data' => 'BonusPay_']];

        if ( !is_null($service->prepayment) )
            $buttons[] = [['text' => 'Предоплата','callback_data' => 'PrePay_']];

        if (empty($buttons))
            return null;

        return parent::buildInlineKeyboard($buttons);

    }


}
