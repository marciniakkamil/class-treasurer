<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'school_year' => (string) $this->faker->numberBetween(2020, 2035),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['pending', 'active', 'closed']),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
