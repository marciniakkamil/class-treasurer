<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('redirects guest to login on collections.delete', function () {
    $collection = Collection::factory()->create();

    $response = $this->delete(route('collections.delete', $collection));

    $response->assertRedirect(route('login'));
});

it('forbids non-owner collector on collections.delete', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create();

    $this->actingAs($other);

    $response = $this->delete(route('collections.delete', $collection));

    $response->assertForbidden();
    $this->assertDatabaseHas('collections', ['id' => $collection->id, 'deleted_at' => null]);
});

it('allows owner collector to delete their collection', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create();

    $this->actingAs($owner);

    $response = $this->delete(route('collections.delete', $collection));

    $response->assertRedirect(route('collections.index'));
    $this->assertSoftDeleted('collections', ['id' => $collection->id]);
});

it('allows admin to delete any collection', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collection = Collection::factory()->create();

    $this->actingAs($admin);

    $response = $this->delete(route('collections.delete', $collection));

    $response->assertRedirect(route('collections.index'));
    $this->assertSoftDeleted('collections', ['id' => $collection->id]);
});
