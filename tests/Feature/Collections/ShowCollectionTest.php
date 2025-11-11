<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('redirects guest to login on collections.show', function () {
    $collection = Collection::factory()->create();

    $response = $this->get(route('collections.show', $collection));

    $response->assertRedirect(route('login'));
});

it('allows owner (collector) to view collections.show', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create(['name' => 'Owner Collection']);

    $this->actingAs($owner);

    $response = $this->get(route('collections.show', $collection));

    $response->assertSuccessful()->assertSee('Owner Collection');
});

it('forbids non-owner collector on collections.show', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create();

    $this->actingAs($other);

    $response = $this->get(route('collections.show', $collection));

    $response->assertForbidden();
});

it('allows admin to view any collection on collections.show', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collection = Collection::factory()->create(['name' => 'Any Collection']);

    $this->actingAs($admin);

    $response = $this->get(route('collections.show', $collection));

    $response->assertSuccessful()->assertSee('Any Collection');
});
