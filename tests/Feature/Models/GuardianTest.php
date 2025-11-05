<?php

use App\Models\Collection;
use App\Models\Guardian;
use App\Models\Payment;

it('belongs to a collection', function () {
    $guardian = Guardian::factory()->for(Collection::factory())->create();

    expect($guardian->collection)->toBeInstanceOf(Collection::class);
});

it('has many payments', function () {
    $guardian = Guardian::factory()->create();
    Payment::factory()->count(2)->for($guardian, 'guardian')->for($guardian->collection)->create();

    expect($guardian->payments)->toHaveCount(2);
});

it('supports soft deletes', function () {
    $guardian = Guardian::factory()->create();
    $guardian->delete();

    expect($guardian->refresh()->deleted_at)->not->toBeNull();
});
