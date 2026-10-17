<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            ServiceCategorySeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DemoContentSeeder::class);
        }
    }
}