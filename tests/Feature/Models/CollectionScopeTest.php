<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('scope visibleTo returns all for admin', function () {
    // Given 1 admin and 2 collectors, each with collections
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collectorA = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collectorB = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $aCollections = Collection::factory()->count(2)->for($collectorA)->create();
    $bCollections = Collection::factory()->count(3)->for($collectorB)->create();

    $visible = Collection::query()->visibleTo($admin)->pluck('id');

    expect($visible->sort()->values()->all())
        ->toEqual($aCollections->pluck('id')->merge($bCollections->pluck('id'))->sort()->values()->all());
});

it('scope visibleTo returns only own for collector', function () {
    $collectorA = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collectorB = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $aCollections = Collection::factory()->count(2)->for($collectorA)->create();
    Collection::factory()->count(3)->for($collectorB)->create();

    $visible = Collection::query()->visibleTo($collectorA)->pluck('id');

    expect($visible->sort()->values()->all())
        ->toEqual($aCollections->pluck('id')->sort()->values()->all());
});
