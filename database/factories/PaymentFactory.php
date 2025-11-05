<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Guardian;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'guardian_id' => Guardian::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 5000),
            'payment_date' => $this->faker->optional()->date(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
