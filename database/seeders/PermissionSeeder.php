<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manageB = new Permission();
        $manageB->name = 'Управлять администраторами';
        $manageB->slug = 'manage-b-level-users';
        $manageB->save();

        $manageD = new Permission();
        $manageD->name = 'Управлять специалистами';
        $manageD->slug = 'manage-d-level-users';
        $manageD->save();

        $showAllSch = new Permission();
        $showAllSch->name = 'Расписание всех специалистов';
        $showAllSch->slug = 'all-schedules';
        $showAllSch->save();

        $showPersSch = new Permission();
        $showPersSch->name = 'Личное расписание';
        $showPersSch->slug = 'personal-schedule';
        $showPersSch->save();
    }
}
