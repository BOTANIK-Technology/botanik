<?php

use Illuminate\Database\Migrations\Migration;

class AddDefaultPackages extends Migration
{
    private const TABLE_NAME = 'packages';

    private const DEFAULT_FIELD = [
        [
            'name' => 'PRO',
            'slug' => 'pro',
        ],
        [
            'name' => 'BASE',
            'slug' => 'base',
        ],
        [
            'name' => 'LITE',
            'slug' => 'lite',
        ],
    ];

    public function up(): void
    {
        DB::table(self::TABLE_NAME)->insert(self::DEFAULT_FIELD);
    }

    public function down(): void
    {
        DB::table(self::TABLE_NAME)
            ->whereIn('slug', ['pro', 'base', 'lite'])
            ->delete();
    }
}
