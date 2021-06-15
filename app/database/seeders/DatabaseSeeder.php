<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(IntervalSeeder::class);
        $this->call(InformationSeeder::class);
        $this->call(ApiSeeder::class);
    }
}
