<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\EventTaskSeeder;
use Database\Seeders\EventTypeSeeder;
use Database\Seeders\TaskTemplateSeeder;
use Database\Seeders\EventTemplateSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
    public function run()
    {
        $this->call([
            UserSeeder::class,
            EventTypeSeeder::class,
            EventTemplateSeeder::class,
            EventSeeder::class,
            TaskTemplateSeeder::class,
            EventTaskSeeder::class,

        ]);

    }


}

