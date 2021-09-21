<?php

namespace App\Services\Telegram\Callback;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Service extends CallbackQuery
{
    /**
     * Service constructor.
     * @param Request $request
     * @throws GuzzleException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'TypesServices_';
        $type_id = parent::setTypeID();
        return $this->editMessage(__('Выберете услугу для записи'), $this->getServices($type_id));
    }

    /**
     * @param $type_id
     * @return array
     */
    public function getServices($type_id)
    {
        $type = \App\Models\TypeService::find($type_id);
        $array = [];
        foreach ($type->services as $service) {
            $array[] = [['text' => $service->name, 'callback_data' => 'Address_'.$service->id]];
        }
        return parent::buildInlineKeyboard($array);
    }
}
