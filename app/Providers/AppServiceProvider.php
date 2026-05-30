<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));

        Validator::extend('valid_phone_length', function ($attribute, $value, $parameters, $validator) {
            // Get the country code from the parameters
            $countryCode = $parameters[0]; // Assuming country_code is passed as a parameter

            // Retrieve the phone_length for the given country code
            $country = \Illuminate\Support\Facades\Cache::remember("country_phone_length_{$countryCode}", now()->addHours(24), function () use ($countryCode) {
                return Country::where('short_code', $countryCode)->first();
            });

            // Check if the country exists and if the phone length matches
            if ($country && strlen($value) === (int) $country->phone_length) {
                return true;
            }

            return false;
        });

        Validator::replacer('valid_phone_length', function ($message, $attribute, $rule, $parameters) {
            // Get the country code from parameters
            $countryCode = $parameters[0];

            // Retrieve the phone length for the given country code
            $country = \Illuminate\Support\Facades\Cache::remember("country_phone_length_{$countryCode}", now()->addHours(24), function () use ($countryCode) {
                return Country::where('short_code', $countryCode)->first();
            });

            if ($country) {
                $expectedLength = $country->phone_length;
                return str_replace(':length', $expectedLength, 'The mobile number must be exactly :length digits for the selected country.');
            }

            return 'The mobile number is invalid.';
        });
    }
}
