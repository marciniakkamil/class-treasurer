<?php

use App\Models\Collection;
use App\Models\Expense;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\User;

it('has a user relationship', function () {
    $collection = Collection::factory()->for(User::factory())->create();

    expect($collection->user)->toBeInstanceOf(User::class);
});

it('has guardians, payments and expenses relationships', function () {
    $collection = Collection::factory()->create();
    $guardians = Guardian::factory()->count(2)->for($collection)->create();
    $payments = Payment::factory()->count(3)->for($collection)->for($guardians->first(), 'guardian')->create();
    $expenses = Expense::factory()->count(2)->for($collection)->create();

    expect($collection->guardians)->toHaveCount(2)
        ->and($collection->payments)->toHaveCount(3)
        ->and($collection->expenses)->toHaveCount(2);
});

it('supports soft deletes', function () {
    $collection = Collection::factory()->create();
    $collection->delete();

    expect($collection->refresh()->deleted_at)->not->toBeNull();
});
