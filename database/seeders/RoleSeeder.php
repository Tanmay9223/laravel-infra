<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        Role::create([
            'uuid' => Str::uuid(),
            'name' => 'Super Admin',
            'show' => $faker->boolean(80),
            'status' => 1,
        ]);

        Role::create([
            'uuid' => Str::uuid(),
            'name' => 'Staff',
            'show' => $faker->boolean(80),
            'status' => 1,
        ]);

        
    }
}
