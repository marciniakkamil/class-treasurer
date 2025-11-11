<?php

use App\Models\Collection;
use App\Models\Guardian;
use App\Models\Payment;
use Illuminate\Support\Carbon;

it('belongs to a collection and a guardian', function () {
    $payment = Payment::factory()
        ->for(Collection::factory())
        ->for(Guardian::factory(), 'guardian')
        ->create();

    expect($payment->collection)->toBeInstanceOf(Collection::class)
        ->and($payment->guardian)->toBeInstanceOf(Guardian::class);
});

it('casts payment_date to a Carbon instance', function () {
    $payment = Payment::factory()->create(['payment_date' => '2025-01-02']);

    expect($payment->payment_date)->toBeInstanceOf(Carbon::class)
        ->and($payment->payment_date->toDateString())->toEqual('2025-01-02');
});

it('supports soft deletes', function () {
    $payment = Payment::factory()->create();
    $payment->delete();

    expect($payment->refresh()->deleted_at)->not->toBeNull();
});
