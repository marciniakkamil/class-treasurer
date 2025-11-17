<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filters\CollectionFilters;
use App\Models\Collection;
use App\Models\User;
use App\Services\CollectionReadService;

it('returns distinct, desc-sorted school years visible to the collector', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Two years for the same user (with duplicate to test distinct)
    Collection::factory()->for($collector)->create(['school_year' => '2023/2024']);
    Collection::factory()->for($collector)->create(['school_year' => '2024/2025']);
    Collection::factory()->for($collector)->create(['school_year' => '2023/2024']);

    // Another user's year should not be visible to this collector
    Collection::factory()->for($other)->create(['school_year' => '2026/2027']);

    $service = new CollectionReadService();

    $years = $service->scholYearsOptions($collector);

    expect($years)->toBeArray()
        ->and($years)->toEqual(['2024/2025', '2023/2024']);
});

it('paginates list with filters and includes aggregates fields', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Create two collections for this user
    $alpha = Collection::factory()->for($collector)->create([
        'name' => 'Alpha Trip',
        'school_year' => '2023/2024',
        'is_active' => true,
    ]);
    $beta = Collection::factory()->for($collector)->create([
        'name' => 'Beta Books',
        'school_year' => '2024/2025',
        'is_active' => false,
    ]);

    $service = new CollectionReadService();

    // Filter by name contains "Alpha" and active only
    $filters = CollectionFilters::fromArray([
        'name' => 'Alpha',
        'is_active' => '1',
    ]);

    $paginator = $service->paginateForList($collector, $filters, perPage: 1);

    // Assertions
    expect($paginator->perPage())->toBe(1)
        ->and($paginator->total())->toBe(1)
        ->and($paginator->count())->toBe(1);

    /** @var Collection $item */
    $item = $paginator->items()[0];

    expect($item->name)->toBe('Alpha Trip')
        // Aggregates added by withDashboardAggregates()
        ->and(isset($item->guardians_count))->toBeTrue()
        ->and(isset($item->payments_count))->toBeTrue()
        ->and(isset($item->expenses_count))->toBeTrue();

    // Sums may be NULL (no related rows) or numeric (0/float). Ensure attributes are accessible and of acceptable type.
    $paymentsSum = $item->getAttribute('payments_sum_amount');
    $expensesSum = $item->getAttribute('expenses_sum_amount');

    expect(is_null($paymentsSum) || is_numeric($paymentsSum))->toBeTrue()
        ->and(is_null($expensesSum) || is_numeric($expensesSum))->toBeTrue();
});
