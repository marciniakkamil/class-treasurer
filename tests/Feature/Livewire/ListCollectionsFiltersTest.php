<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\Collections\ListCollections;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

it('filters by name in ListCollections', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Two collections with different names for the same user
    Collection::factory()->for($collector)->create(['name' => 'Alpha Trip', 'is_active' => true, 'school_year' => '2024/2025']);
    Collection::factory()->for($collector)->create(['name' => 'Beta Books', 'is_active' => true, 'school_year' => '2024/2025']);

    $this->actingAs($collector);

    Livewire::test(ListCollections::class)
        ->set('filters.name', 'Alpha')
        ->assertSee('Alpha Trip')
        ->assertDontSee('Beta Books');
});

it('filters by school_year in ListCollections', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    Collection::factory()->for($collector)->create(['name' => 'Old Year', 'school_year' => '2023/2024', 'is_active' => true]);
    Collection::factory()->for($collector)->create(['name' => 'Current Year', 'school_year' => '2024/2025', 'is_active' => true]);

    $this->actingAs($collector);

    Livewire::test(ListCollections::class)
        ->set('filters.school_year', '2023/2024')
        ->assertSee('Old Year')
        ->assertDontSee('Current Year');
});

it('filters by is_active in ListCollections and can clear filters', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    Collection::factory()->for($collector)->create(['name' => 'Inactive One', 'is_active' => false, 'school_year' => '2024/2025']);
    Collection::factory()->for($collector)->create(['name' => 'Active One', 'is_active' => true, 'school_year' => '2024/2025']);

    $this->actingAs($collector);

    Livewire::test(ListCollections::class)
        ->set('filters.is_active', '1')
        ->assertSee('Active One')
        ->assertDontSee('Inactive One')
        ->call('clearFilters')
        ->assertSee('Active One')
        ->assertSee('Inactive One');
});
