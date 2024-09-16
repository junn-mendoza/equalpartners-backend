<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $categories = [
            ['name' => 'Custom', 'color' => '#000', 'icon' => ''],
            ['name' => 'Bedroom', 'color' => '#ffac00', 'icon' => 'bedroom.png'],
            ['name' => 'Laundry', 'color' => '#f46e0f', 'icon' => 'laundry.png'],
            ['name' => 'Living Room', 'color' => '#e92b17', 'icon' => 'sofa.png'],
            ['name' => 'Kitchen', 'color' => '#ba3251', 'icon' => 'other.png'],
            ['name' => 'Pets', 'color' => '#6d3088', 'icon' => 'pets.png'],
            ['name' => 'Kids', 'color' => '#302564', 'icon' => 'kids.png'],
            ['name' => 'Meals', 'color' => '#1b5a85', 'icon' => 'meals.png'],
            ['name' => 'Bathroom', 'color' => '#18bcf3', 'icon' => 'bath.png'],
            ['name' => 'Garden', 'color' => '#57c96d', 'icon' => 'garden.png'],

        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
