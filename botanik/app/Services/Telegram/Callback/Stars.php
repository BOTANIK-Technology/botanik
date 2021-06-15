<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class Stars extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if (isset($this->getData()->Review)) {
            return $this->review();
        }
        return false;
    }

    private function review ()
    {
        $review = \App\Models\Review::create(
            [
                'telegram_user_id' => $this->user->id,
                'stars' => parent::setStarts(),
                'text' => parent::getData()->Review
            ]
        );
        if ($review) {
            parent::resetData();
            parent::editMessage('Спасибо за Ваш отзыв!');
        }
    }
}