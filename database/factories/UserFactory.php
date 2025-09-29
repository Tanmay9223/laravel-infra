<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'username' => strtolower($this->faker->firstName() . $this->faker->lastName() . rand(100, 999)),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'mobile' => $this->faker->numerify('##########'),
            'dial_code' => '91',
            'country_code' => $this->faker->countryCode(),
            'password_changed_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'password_history' => json_encode([$this->faker->sha256(), $this->faker->sha256()]),
            'is_google2fa_enable' => 0,
            'ip_address' => $this->faker->ipv4(),
            'stage_status' => 1,
            'status' => 1
         ];
    }
}
