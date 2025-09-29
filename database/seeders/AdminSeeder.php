<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Admin::create([
            'uuid' => Str::uuid(),
            'role_id' => 1,
            'name' => 'Super Admin',
            'email' => 'super_admin@mlm.com',
            'password' => '$2y$10$of4xZ/4jcZACeHStb/lMZ./9vhPbuls1NJURRKEvuvVrgOzXTz24m', 
            'status' => 1,
        ]);

        Admin::factory()->count(25)->create();
    }
}
