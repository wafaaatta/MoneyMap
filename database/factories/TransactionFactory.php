<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => fake()->optional(),
            "type" => fake()->randomElement(["income", "expense"]),
            "amount" => fake()->numberBetween(100, 1000),
            "description" => fake()->sentence(),
            "transaction_date" => fake()->date(),
        ];
    }
}
