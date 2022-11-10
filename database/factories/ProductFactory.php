<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'restaurant_id' => random_int(1,10),
            'name' => fake()->name(),
            'price' => 15,
            'description' =>  ucwords($this->faker->words(1, true)),
            'image' => 'https://talabat-iti.s3.amazonaws.com/users/635eb4dac5781.jpeg',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
