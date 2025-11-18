<?php

declare(strict_types=1);

use App\Actions\Collections\CreateCollectionAction;
use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('creates a collection assigned to the owner with provided fields', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $data = [
        'name' => 'Wycieczka szkolna',
        'school_year' => '2024/2025',
        'description' => 'Opis zbiórki',
        'is_active' => true,
        'status' => 'active',
    ];

    $action = new CreateCollectionAction;
    $collection = $action->execute($owner, $data);

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection->user_id)->toBe($owner->id)
        ->and($collection->name)->toBe('Wycieczka szkolna')
        ->and($collection->school_year)->toBe('2024/2025')
        ->and($collection->description)->toBe('Opis zbiórki')
        ->and((bool) $collection->is_active)->toBeTrue()
        ->and($collection->status)->toBe('active');

    $this->assertDatabaseHas('collections', [
        'id' => $collection->id,
        'user_id' => $owner->id,
        'name' => 'Wycieczka szkolna',
        'school_year' => '2024/2025',
        'description' => 'Opis zbiórki',
        'is_active' => 1,
        'status' => 'active',
    ]);
});

it('applies sensible defaults when optional fields are missing', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $data = [
        'name' => 'Bez dodatków',
        // no school_year, description, status, is_active
    ];

    $action = new CreateCollectionAction;
    $collection = $action->execute($owner, $data);

    expect($collection->name)->toBe('Bez dodatków')
        ->and($collection->user_id)->toBe($owner->id)
        ->and($collection->school_year)->toBeNull()
        ->and($collection->description)->toBeNull()
        ->and($collection->status)->toBe('active')
        ->and((bool) $collection->is_active)->toBeFalse();
});
