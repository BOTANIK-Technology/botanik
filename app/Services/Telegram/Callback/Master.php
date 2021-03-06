<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class Master extends CallbackQuery
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $service_id = parent::getServiceID();
        $address_id = parent::setAddressID();
        $this->back = 'Address_'.$service_id;
        return $this->editMessage(__('Выберете специалиста'), $this->getMaster($service_id, $address_id));
    }

    /**
     * @param $service_id
     * @param $address_id
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    private function getMaster($service_id, $address_id) {

        $masters = [];
        $service = \App\Models\Service::find($service_id);

        foreach ($service->users as $user)
            foreach ($user->addresses as $user_address)
                if ($user_address->id == $address_id)
                    $masters[] = [['text' => $user->name, 'callback_data' => 'DatesMaster_'.$user->id]];

        return parent::buildInlineKeyboard($masters);

    }
}
