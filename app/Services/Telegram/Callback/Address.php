<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Address extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $service_id = parent::setServiceID();
        $this->back = 'Service_' . parent::getTypeID();
        return $this->editMessage('🌐 ' . __('Выберете адрес'), $this->getAddress($service_id));
    }

    /**
     * @param $service_id
     * @return array
     */
    private function getAddress($service_id)
    {

        $service = \App\Models\Service::where('id', $service_id)->first();
        $addresses = [];
        foreach ($service->addresses as $address) {
            if (!$service->timetables->count()) {
                $addresses[] = [['text' => $address->address, 'callback_data' => 'Master_' . $address->id]];
            }
            else {
                $addresses[] = [['text' => $address->address, 'callback_data' => 'DatesService_' . $address->id]];
            }
        }
        return parent::buildInlineKeyboard($addresses);
    }

}
