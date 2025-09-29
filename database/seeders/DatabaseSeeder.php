<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            MetaDataSeeder::class,
            CurrencySeeder::class,
            TemplateSeeder::class,
        ]);
    }
}
