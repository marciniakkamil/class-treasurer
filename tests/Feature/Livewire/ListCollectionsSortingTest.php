<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\Collections\ListCollections;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

it('sorts by name asc and desc and respects perPage', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Ensure deterministic names for ordering
    Collection::factory()->for($collector)->create(['name' => 'Alpha', 'is_active' => true, 'school_year' => '2024/2025']);
    Collection::factory()->for($collector)->create(['name' => 'Zulu', 'is_active' => true, 'school_year' => '2024/2025']);

    $this->actingAs($collector);

    // Asc by name => Alpha before Zulu
    Livewire::test(ListCollections::class)
        ->set('sort', 'name')
        ->assertSeeInOrder(['Alpha', 'Zulu']);

    // Desc by name => Zulu before Alpha
    Livewire::test(ListCollections::class)
        ->set('sort', '-name')
        ->assertSeeInOrder(['Zulu', 'Alpha']);

    // Per page = 1 should only show the first record for the current sort
    Livewire::test(ListCollections::class)
        ->set('sort', 'name')
        ->set('perPage', 1)
        ->assertSee('Alpha')
        ->assertDontSee('Zulu');
});
