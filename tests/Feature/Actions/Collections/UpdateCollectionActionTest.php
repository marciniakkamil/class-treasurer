<?php

declare(strict_types=1);

use App\Actions\Collections\UpdateCollectionAction;
use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('updates a collection with provided fields', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    /** @var Collection $collection */
    $collection = Collection::factory()->for($owner)->create([
        'name' => 'Stara nazwa',
        'school_year' => '2023/2024',
        'description' => 'Stary opis',
        'is_active' => true,
        'status' => 'active',
    ]);

    $data = [
        'name' => 'Nowa nazwa',
        'school_year' => '2025/2026',
        'description' => 'Nowy opis',
        'is_active' => false,
    ];

    $action = new UpdateCollectionAction;
    $action->execute($collection, $data);

    $collection->refresh();

    expect($collection->name)->toBe('Nowa nazwa')
        ->and($collection->school_year)->toBe('2025/2026')
        ->and($collection->description)->toBe('Nowy opis')
        ->and((bool) $collection->is_active)->toBeFalse()
        ->and($collection->user_id)->toBe($owner->id);

    $this->assertDatabaseHas('collections', [
        'id' => $collection->id,
        'user_id' => $owner->id,
        'name' => 'Nowa nazwa',
        'school_year' => '2025/2026',
        'description' => 'Nowy opis',
        'is_active' => 0,
    ]);
});

it('nullifies optional fields on empty strings and casts boolean', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    /** @var Collection $collection */
    $collection = Collection::factory()->for($owner)->create([
        'name' => 'Pierwotna',
        'school_year' => '2024/2025',
        'description' => 'Jakiś opis',
        'is_active' => true,
        'status' => 'active',
    ]);

    $data = [
        'name' => 'Zmieniona',
        'school_year' => '', // powinno stać się null
        'description' => '', // powinno stać się null
        'is_active' => true, // powinno pozostać true
    ];

    $action = new UpdateCollectionAction;
    $action->execute($collection, $data);

    $collection->refresh();

    expect($collection->name)->toBe('Zmieniona')
        ->and($collection->school_year)->toBeNull()
        ->and($collection->description)->toBeNull()
        ->and((bool) $collection->is_active)->toBeTrue();
});
