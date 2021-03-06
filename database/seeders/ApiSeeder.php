<?php

namespace Database\Seeders;

use App\Models\Api;
use Illuminate\Database\Seeder;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $yclients = new Api();
        $yclients->slug = 'yclients';
        $yclients->save();

        $beauty = new Api();
        $beauty->slug = 'beauty';
        $beauty->save();

    }
}
