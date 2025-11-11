<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filters\CollectionFilters;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('limits visibility for admin and collector', function (): void {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collectorA = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collectorB = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Collections for both collectors
    Collection::factory()->count(2)->create(['user_id' => $collectorA->id, 'is_active' => true]);
    Collection::factory()->count(3)->create(['user_id' => $collectorB->id, 'is_active' => true]);

    // Admin sees all
    $adminCount = Collection::query()->visibleTo($admin)->count();
    expect($adminCount)->toBe(5);

    // Collector A sees only own
    $aCount = Collection::query()->visibleTo($collectorA)->count();
    expect($aCount)->toBe(2);

    // Collector B sees only own
    $bCount = Collection::query()->visibleTo($collectorB)->count();
    expect($bCount)->toBe(3);
});

it('applies name, school_year, and is_active filters', function (): void {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    Collection::factory()->create(['user_id' => $collector->id, 'name' => 'Wycieczka do Krakowa', 'school_year' => '2024/2025', 'is_active' => true]);
    Collection::factory()->create(['user_id' => $collector->id, 'name' => 'Składka klasowa', 'school_year' => '2024/2025', 'is_active' => false]);
    Collection::factory()->create(['user_id' => $collector->id, 'name' => 'Zbiórka na kwiaty', 'school_year' => '2023/2024', 'is_active' => true]);

    $filters = [
        'name' => 'Krak',
        'school_year' => '2024/2025',
        'is_active' => true,
    ];

    $results = Collection::query()
        ->visibleTo($collector)
        ->applyFilters(CollectionFilters::fromArray($filters))
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toContain('Krak');
});

it('supports pagination after filters', function (): void {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    actingAs($collector);

    Collection::factory()->count(30)->create([
        'user_id' => $collector->id,
        'is_active' => true,
        'school_year' => '2024/2025',
    ]);

    $paginator = Collection::query()
        ->visibleTo($collector)
        ->applyFilters(CollectionFilters::fromArray(['school_year' => '2024/2025', 'is_active' => true]))
        ->orderBy('id')
        ->paginate(15);

    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginator->perPage())->toBe(15)
        ->and($paginator->total())->toBe(30)
        ->and($paginator->currentPage())->toBe(1);
});
