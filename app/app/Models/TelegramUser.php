<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TelegramUser
 *
 * @property-read Collection|FeedBack[] $feedBacks
 * @property-read int|null $feed_backs_count
 * @property-read Collection|Record[] $records
 * @property-read int|null $records_count
 * @property-read Collection|Review[] $reviews
 * @property-read int|null $reviews_count
 * @property-read TelegramSession|null $telegramSession
 * @property-read Collection|VisitHistory[] $visitHistories
 * @property-read int|null $visit_histories_count
 * @method static Builder|TelegramUser newModelQuery()
 * @method static Builder|TelegramUser newQuery()
 * @method static Builder|TelegramUser query()
 * @mixin Eloquent
 */
class TelegramUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'phone',
        'email',
        'age',
        'sex',
        'status',
        'last_service',
        'favorite_service',
        'bonus',
        'frequency',
        'spent_bonus',
        'spent_money'
    ];

    public const titles = ['ID клиента', 'Фамилия', 'Имя', 'Очество', 'Ник Телеграма', 'Телефон', 'Почта', 'Возраст', 'Пол', 'Баланс баллов'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function telegramSession()
    {
        return $this->hasOne(TelegramSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visitHistories()
    {
        return $this->hasMany(VisitHistory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedBacks()
    {
        return $this->hasMany(FeedBack::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return array
     */
    public static function getTitles() : array
    {
        return self::titles;
    }

    /**
     * @return array
     */
    public function getVisitLabels () : array
    {
        return [__('Услуга'), __('Адрес'), __('Специалист'), __('Дата'), __('Время посещения'), __('Потраченные деньги'), __('Потраченные бонусы')];
    }

    /**
     * @return array
     */
    public function getVisitTable () : array
    {
        $array = [];

        if (isset($this->records)) {

            foreach ($this->records as $visit) {
                $array[$visit->id]['service'] =  $visit->service->name;
                $array[$visit->id]['address'] =  $visit->address->address;
                $array[$visit->id]['master']  =  empty($visit->user) ? '-' : $visit->user->name;
                $array[$visit->id]['date']    =  $visit->date;
                $array[$visit->id]['time']    =  $visit->time;
                $array[$visit->id]['price']   =  $visit->payment->money;
                $array[$visit->id]['bonus']   =  $visit->payment->bonuses ?? 0;
            }

        }

        return $array;
    }

    public function getStatistic (string $sort) : array
    {
        $array = [
            'visit' => 0,
            'freq'  => 0,
            'money' => 0,
            'bonus' => 0
        ];

       switch ($sort) {
           case 'month':
               $records = $this->records->where('date', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'));
               break;
           case 'year':
               $records = $this->records->where('date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'));
               break;
           case 'half':
               $records = $this->records->where('date', '>=', Carbon::now()->subDays(182)->format('Y-m-d'));
               break;
           default :
               $records = $this->records;
       }

       if ($records && !$records->isEmpty()) {
           $freq = [];
           foreach ($records as $record) {
               if ($record->status) {
                   $array['money'] += $record->payment->money;
                   $array['bonus'] += $record->payment->bonuses;
                   $array['visit'] += 1;

                   if (!isset($freq[$record->date]))
                       $freq[$record->date] = 1;
                   else
                       $freq[$record->date] += 1;
               }
           }
           if (!empty($freq)) {
               foreach ($freq as $f)
                   $array['freq'] += $f;
           }
       }

       return $array;
    }

    /**
     * @return string
     */
    public function getFio () : string
    {
        $fio = '';
        is_null($this->last_name) ?: $fio .= $this->last_name.' ';
        $fio .= $this->first_name;
        is_null($this->middle_name) ?: $fio .= ' '.$this->middle_name;
        return $fio;
    }
}
