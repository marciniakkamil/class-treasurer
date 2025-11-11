<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('redirects guest to login on collections.create', function () {
    $response = $this->get(route('collections.create'));

    $response->assertRedirect(route('login'));
});

it('allows collector to see create form', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $this->actingAs($collector);

    $response = $this->get(route('collections.create'));

    $response->assertSuccessful()->assertSee('Nowa zbiórka');
});

it('allows admin to see create form', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    $this->actingAs($admin);

    $response = $this->get(route('collections.create'));

    $response->assertSuccessful()->assertSee('Nowa zbiórka');
});

it('creates collection and redirects to show', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $this->actingAs($collector);

    // Submit the Livewire form by hitting the component endpoint using regular POST is tricky; instead, imitate by calling component directly.
    \Livewire\Livewire::test(\App\Livewire\Collections\CreateCollection::class)
        ->set('name', 'Wycieczka szkolna')
        ->set('school_year', '2024/2025')
        ->set('description', 'Zbieramy na wycieczkę')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect();

    $this->assertDatabaseHas('collections', [
        'name' => 'Wycieczka szkolna',
        'school_year' => '2024/2025',
        'user_id' => $collector->id,
        'is_active' => 1,
        'status' => 'active',
    ]);
});

it('validates required name', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $this->actingAs($collector);

    \Livewire\Livewire::test(\App\Livewire\Collections\CreateCollection::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);

    expect(Collection::query()->count())->toBe(0);
});
