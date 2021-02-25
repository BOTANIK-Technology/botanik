<?php

namespace Database\Seeders;

use App\Models\Information;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contacts = new Information();
        $contacts->title = 'Контакты';
        $contacts->text = 'Ежедневно с 09:00 до 20:30'."\n".'+380 99 999 99 99'."\n".'+380 99 999 99 99'."\n".'+380 99 999 99 99'."\n".'salon-name@gmail.com';
        $contacts->addresses = \GuzzleHttp\json_encode(['ул. Сумкая №1']);
        $contacts->save();

        $about = new Information();
        $about->title = 'О нас';
        $about->text = 'Мы... ';
        $about->save();

        $dev = new Information();
        $dev->title = 'О разработчике';
        $dev->text = 'LLC Ukrlogika';
        $dev->save();
    }
}
