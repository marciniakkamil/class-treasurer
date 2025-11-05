<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 5000),
            'expense_date' => $this->faker->optional()->date(),
            'description' => $this->faker->optional()->sentence(),
            'approved' => $this->faker->boolean(50),
        ];
    }
}
