<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mail
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail query()
 * @mixin \Eloquent
 */
class Mail extends Model
{

    protected $fillable = [
        'title',
        'text',
        'age_start',
        'age_end',
        'sex',
        'frequency',
        'img',
        'button',
        'last_service',
        'favorite_service'
    ];

    public $age = [
        17, 18, 19,
        20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
        40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
        50, 51, 52, 53, 54, 55, 56, 57, 58, 59
    ];

    public $frequency = [
        0, '1 - 2', '3 - 5', '6 - 10', 'Больше 10'
    ];

    use HasFactory;

    /**
     * @param string $token
     * @param array $params
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public static function doMails (string $token, array $params)
    {
        $all = true;
        foreach ($params as $k => $val) {
            if (
                $k != 'title'  &&
                $k != 'button' &&
                $k != 'title'  &&
                $k != 'img'    &&
                !is_null($val)
            ) {
                $all = false;
                break;
            }
        }

        if ($all) {

            $ids = TelegramUser::select('chat_id')->toArray();

        } else {

            $age_between = !empty($params['age_start']) && !empty($params['age_end']) ? true : false;
            $age_before = empty($params['age_start']) && !empty($params['age_end']) ? true : false;
            $age_from = !empty($params['age_start']) && empty($params['age_end']) ? true : false;

            $clients = new TelegramUser();

            $ids = $clients
                ->when($age_between, function ($query) use ($params) {
                    return $query->whereBetween('age', [$params['age_start'], $params['age_end']]);
                })
                ->when($age_before, function ($query) use ($params) {
                    return $query->where('age', '<', $params['age_end']);
                })
                ->when($age_from, function ($query) use ($params) {
                    return $query->where('age', '>', $params['age_start']);
                })
                ->when(!empty($params['sex']), function ($query) use ($params) {
                    return $query->where('sex', $params['sex']);
                })
                ->when(!empty($params['frequency']), function ($query) use ($params) {
                    switch ($params['frequency']) {
                        case 1:  return $query->whereBetween('frequency', [0, 3]);
                        case 2:  return $query->whereBetween('frequency', [2, 6]);
                        case 3:  return $query->whereBetween('frequency', [5, 11]);
                        case 4:  return $query->where('frequency', '>', 10);
                        default: return $query->where('frequency', 0);
                    }
                })
                ->when(!empty($params['last_service']), function ($query, $params) {
                    return $query->where('last_service', $params['last_service']);
                })
                ->when(!empty($params['favorite_service']), function ($query, $params) {
                    return $query->where('favorite_service', $params['favorite_service']);
                })
                ->pluck('chat_id')
                ->toArray();
        }

        if (empty($ids) && !$ids)
            return;

        if (!is_null($params['button']))
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([[['text' => 'Перейти', 'url' => $params['button']]]]);
        else
            $keyboard = null;

        $bot = new \TelegramBot\Api\BotApi($token);
        $mess = '<b>'.$params['title'].'</b>'."\n\n".$params['text'];

        if (is_null($params['img'])) {

            foreach ($ids as $id) {
                $bot->sendMessage(
                    $id,
                    $mess,
                    'HTML',
                    false,
                    null,
                    $keyboard
                );
            }

        } else {

            foreach ($ids as $id) {
                $bot->sendPhoto(
                    $id,
                    $params['img'],
                    $mess,
                    null,
                    $keyboard,
                    false,
                    'HTML'
                );
            }

        }

    }
}
