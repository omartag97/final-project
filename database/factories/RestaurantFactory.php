<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Grade;
use Faker\Core\Number;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'store_name' => fake()->name(),
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'type' => ucwords($this->faker->words(1, true)),
            'mobile' => ucwords($this->faker->phoneNumber()),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make(123123123),
            'region' => ucwords($this->faker->words(4, true)),
            'description' => ucwords($this->faker->words(4, true)),
            'image' => 'https://talabat-iti.s3.amazonaws.com/users/635eb4dac5781.jpeg',
            'delivery_fee' => 10,
            'online_tracking' => 0,
            'created_at' => now(),
        ];
    }
}
