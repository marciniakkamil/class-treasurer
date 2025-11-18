<?php

declare(strict_types=1);

use App\Actions\Collections\DeleteCollectionAction;
use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('soft deletes a collection and returns null', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    /** @var Collection $collection */
    $collection = Collection::factory()->for($owner)->create();

    $action = new DeleteCollectionAction;
    $result = $action->execute($collection);

    // Action itself returns null (transaction closure has no return)
    expect($result)->toBeNull();

    // Model should be soft-deleted
    $this->assertSoftDeleted('collections', ['id' => $collection->id]);
});

it('is idempotent on already soft-deleted model', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    /** @var Collection $collection */
    $collection = Collection::factory()->for($owner)->create();

    $action = new DeleteCollectionAction;
    $action->execute($collection);
    $this->assertSoftDeleted('collections', ['id' => $collection->id]);

    // Calling again should not throw and should remain soft-deleted
    $result = $action->execute($collection);
    expect($result)->toBeNull();
    $this->assertSoftDeleted('collections', ['id' => $collection->id]);
});
