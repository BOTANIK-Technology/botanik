<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = new Role();
        $owner->name = 'Основатель';
        $owner->slug = 'owner';
        $owner->save();

        $admin = new Role();
        $admin->name = 'Администратор';
        $admin->slug = 'admin';
        $admin->save();

        $master = new Role();
        $master->name = 'Специалист';
        $master->slug = 'master';
        $master->save();
    }
}
