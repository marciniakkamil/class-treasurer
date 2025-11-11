<?php

use App\Models\Collection;
use App\Models\Expense;
use Illuminate\Support\Carbon;

it('belongs to a collection', function () {
    $expense = Expense::factory()->for(Collection::factory())->create();

    expect($expense->collection)->toBeInstanceOf(Collection::class);
});

it('casts expense_date to a Carbon instance', function () {
    $expense = Expense::factory()->create(['expense_date' => '2025-02-03']);

    expect($expense->expense_date)->toBeInstanceOf(Carbon::class)
        ->and($expense->expense_date->toDateString())->toEqual('2025-02-03');
});

it('supports soft deletes', function () {
    $expense = Expense::factory()->create();
    $expense->delete();

    expect($expense->refresh()->deleted_at)->not->toBeNull();
});
