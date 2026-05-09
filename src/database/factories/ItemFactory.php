<?php

namespace Database\Factories;

use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'condition_id' => Condition::inRandomOrder()->value('id'),
            'image' => 'test.png',
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 10000),
        ];
    }
}
