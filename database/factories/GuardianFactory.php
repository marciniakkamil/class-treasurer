<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'name' => $this->faker->name(),
            'child_name' => $this->faker->optional()->name(),
            'contact' => $this->faker->optional()->phoneNumber(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
