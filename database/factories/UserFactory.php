<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['M', 'F']);
        $firstName = $this->faker->firstName($gender);
        $lastName = $this->faker->lastName($gender);

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'gender' => $gender,
            'walletID' => '',
            'address' => $this->faker->address(),
            'addressState' => $this->faker->city(),
            'addressCity' => $this->faker->country(),
            #'bvn' => $this->faker->numerify('###########'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => \Illuminate\Support\Str::replace(['(', ')', "\s", '-', '.', " "], '', $this->faker->phoneNumber()),
            'dateOfBirth' => Carbon::now()->addYears(-19),
            'email_verified_at' => now(),
            'account_alias' => "{$firstName} {$lastName}",
            'account_currencycode' => 'NGN',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
