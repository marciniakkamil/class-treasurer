<?php

declare(strict_types=1);

use App\Models\Collection;
use App\Models\User;

it('allows admin to perform any action via before hook', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['role' => 'collector']);
    $collection = Collection::factory()->for($owner)->create();

    // Admin can viewAny
    $this->actingAs($admin);
    expect($admin->can('viewAny', Collection::class))->toBeTrue();

    // Admin can view/update/delete any collection
    expect($admin->can('view', $collection))->toBeTrue()
        ->and($admin->can('update', $collection))->toBeTrue()
        ->and($admin->can('delete', $collection))->toBeTrue();
});

it('allows collectors to viewAny and create collections', function () {
    $collector = User::factory()->create(['role' => 'collector']);

    $this->actingAs($collector);

    expect($collector->can('viewAny', Collection::class))->toBeTrue()
        ->and($collector->can('create', Collection::class))->toBeTrue();
});

it('allows collector to view/update/delete only their own collections', function () {
    $collector = User::factory()->create(['role' => 'collector']);
    $otherCollector = User::factory()->create(['role' => 'collector']);

    $ownCollection = Collection::factory()->for($collector)->create();
    $othersCollection = Collection::factory()->for($otherCollector)->create();

    $this->actingAs($collector);

    // Own collection
    expect($collector->can('view', $ownCollection))->toBeTrue()
        ->and($collector->can('update', $ownCollection))->toBeTrue()
        ->and($collector->can('delete', $ownCollection))->toBeTrue();

    // Someone else's collection
    expect($collector->can('view', $othersCollection))->toBeFalse()
        ->and($collector->can('update', $othersCollection))->toBeFalse()
        ->and($collector->can('delete', $othersCollection))->toBeFalse();
});

it('denies non-privileged users to viewAny, create or manage collections', function () {
    $member = User::factory()->create(['role' => 'member']);
    $collector = User::factory()->create(['role' => 'collector']);
    $collection = Collection::factory()->for($collector)->create();

    $this->actingAs($member);

    expect($member->can('viewAny', Collection::class))->toBeFalse()
        ->and($member->can('create', Collection::class))->toBeFalse()
        ->and($member->can('view', $collection))->toBeFalse()
        ->and($member->can('update', $collection))->toBeFalse()
        ->and($member->can('delete', $collection))->toBeFalse();
});
