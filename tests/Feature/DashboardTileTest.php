<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('shows count of all active collections for admin and links to collections index', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collectorA = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collectorB = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Active and inactive collections for both collectors
    Collection::factory()->count(2)->for($collectorA)->create(['is_active' => true]);
    Collection::factory()->count(1)->for($collectorA)->create(['is_active' => false]);

    Collection::factory()->count(3)->for($collectorB)->create(['is_active' => true]);
    Collection::factory()->count(2)->for($collectorB)->create(['is_active' => false]);

    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    // Admin sees all active: 2 + 3 = 5
    $response->assertOk()
        ->assertSee('Aktywne zbiórki')
        ->assertSee('5')
        ->assertSee(route('collections.index'));
});

it('shows count of own active collections for collector and links to collections index', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Collector's collections
    Collection::factory()->count(4)->for($collector)->create(['is_active' => true]);
    Collection::factory()->count(1)->for($collector)->create(['is_active' => false]);

    // Other user's active collections should NOT be counted for collector
    Collection::factory()->count(10)->for($other)->create(['is_active' => true]);

    $this->actingAs($collector);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Aktywne zbiórki')
        ->assertSee('4')
        ->assertSee(route('collections.index'));
});
