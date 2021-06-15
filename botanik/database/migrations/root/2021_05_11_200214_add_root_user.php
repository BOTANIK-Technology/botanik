<?php

use Illuminate\Database\Migrations\Migration;

class AddRootUser extends Migration
{
    private const TABLE_NAME = 'users';

    private const EMAIL_FIELD = 'root-ukrlogika@gess.com';

    public function up(): void
    {
        DB::table(self::TABLE_NAME)->insert([
            'name' => 'ROOT',
            'email' => self::EMAIL_FIELD,
            'password' => bcrypt('v2kLZ1CEL7aYceXAXh'),
        ]);
    }

    public function down(): void
    {
        DB::table(self::TABLE_NAME)
            ->where('email', self::EMAIL_FIELD)
            ->delete();
    }
}
