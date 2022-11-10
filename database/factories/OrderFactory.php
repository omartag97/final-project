<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => random_int(1,10),
            'restaurant_id' => random_int(1,10),
            'addition_request' =>  ucwords($this->faker->words(1, true)),
            'delivery_fee'       => 10,
            'payment_type' =>  ucwords($this->faker->words(1, true)),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
