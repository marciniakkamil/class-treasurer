<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\Collections\EditCollection;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

it('redirects guest to login on collections.edit', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create();

    $response = $this->get(route('collections.edit', $collection));

    $response->assertRedirect(route('login'));
});

it('forbids non-owner collector from seeing edit form', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create();

    $this->actingAs($other);

    $this->get(route('collections.edit', $collection))->assertForbidden();
});

it('allows owner to edit collection and persists changes', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create([
        'name' => 'Stara nazwa',
        'school_year' => '2023/2024',
        'description' => 'Opis',
        'is_active' => true,
    ]);

    $this->actingAs($owner);

    Livewire::test(EditCollection::class, ['collection' => $collection->id])
        ->assertOk()
        ->set('name', 'Nowa nazwa')
        ->set('school_year', '2024/2025')
        ->set('description', 'Nowy opis')
        ->set('is_active', false)
        ->call('update')
        ->assertRedirect();

    $this->assertDatabaseHas('collections', [
        'id' => $collection->id,
        'name' => 'Nowa nazwa',
        'school_year' => '2024/2025',
        'description' => 'Nowy opis',
        'is_active' => 0,
    ]);
});
