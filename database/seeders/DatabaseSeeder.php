<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use App\Models\Drivers;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(10)->create();
        Restaurant::factory(10)->create();
        Product::factory(10)->create();
        Order::factory(10)->create();
    }
}


