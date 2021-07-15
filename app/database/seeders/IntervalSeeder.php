<?php

namespace Database\Seeders;

use App\Models\Interval;
use Illuminate\Database\Seeder;

class IntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $minutes_30 = new Interval();
        $minutes_30->name = '30 минут';
        $minutes_30->value = '30 minutes';
        $minutes_30->minutes = 30;
        $minutes_30->save();

        $minutes_45 = new Interval();
        $minutes_45->name = '45 минут';
        $minutes_45->value = '45 minutes';
        $minutes_45->minutes = 45;
        $minutes_45->save();

        $hour = new Interval();
        $hour->name = '1 час';
        $hour->value = '1 hour';
        $hour->minutes = 60;
        $hour->save();

        $hours_2 = new Interval();
        $hours_2->name = '2 часа';
        $hours_2->value = '2 hours';
        $hours_2->minutes = 120;
        $hours_2->save();

        $hours_3 = new Interval();
        $hours_3->name = '3 часа';
        $hours_3->value = '3 hours';
        $hours_3->minutes = 180;
        $hours_3->save();

        $hours_4 = new Interval();
        $hours_4->name = '4 часа';
        $hours_4->value = '4 hours';
        $hours_4->minutes = 240;
        $hours_4->save();

        $hours_5 = new Interval();
        $hours_5->name = '5 часов';
        $hours_5->value = '5 hours';
        $hours_5->minutes = 300;
        $hours_5->save();

        $hours_6 = new Interval();
        $hours_6->name = '6 часов';
        $hours_6->value = '6 hours';
        $hours_6->minutes = 360;
        $hours_6->save();

        $hours_8 = new Interval();
        $hours_8->name = '8 часов';
        $hours_8->value = '8 hours';
        $hours_8->minutes = 480;
        $hours_8->save();

        $hours_12 = new Interval();
        $hours_12->name = '12 часов';
        $hours_12->value = '12 hours';
        $hours_12->minutes = 720;
        $hours_12->save();

        $hours_24 = new Interval();
        $hours_24->name = '24 часа';
        $hours_24->value = '24 hours';
        $hours_24->minutes = 1440;
        $hours_24->save();
    }
}
