<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'ROOT';
        $user->email = 'root-ukrlogika@gess.com';
        $user->password = bcrypt('v2kLZ1CEL7aYceXAXh');
        $user->save();
    }
}
