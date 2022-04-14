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
        $hours = 0;
        while($hours <= 24) {
            $minutes = 0;
            while ($minutes <= 55) {
                $minutes_30 = new Interval();
                $minutes_30->name = $hours . ' часов: ' . $minutes . ' минут';
                $minutes_30->value = $hours . ' hours: ' . $minutes . ' minutes';
                $minutes_30->minutes = $minutes + $hours * 60;
                $minutes_30->hoursField = $hours;
                $minutes_30->minutesField = $minutes;
                $minutes_30->save();
                $minutes += 5;
            }
            $hours++;
        }

    }
}
