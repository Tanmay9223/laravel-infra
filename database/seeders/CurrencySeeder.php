<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['currency_name' => 'United States Dollar', 'currency_symbol' => '$', 'currency' => 'USD', 'amount' => 1.00, 'default' => 1, 'status' => 1],
            ['currency_name' => 'Euro', 'currency_symbol' => '€', 'currency' => 'EUR', 'amount' => 0.85, 'default' => 0, 'status' => 1],
            ['currency_name' => 'Indian Rupee', 'currency_symbol' => '₹', 'currency' => 'INR', 'amount' => 85.23, 'default' => 0, 'status' => 1]
        ];

        foreach ($currencies as $key => $value) {
            Currency::create($value);
        }
    }
}
