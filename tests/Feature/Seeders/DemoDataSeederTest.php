<?php

use App\Models\Collection;
use App\Models\Expense;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;

it('seeds demo data graph: one treasurer, multiple collections, guardians, payments, expenses', function () {
    // Run only the demo seeder
    $this->seed(DemoDataSeeder::class);

    // Users
    expect(User::count())->toBe(2);
    $treasurer = User::where('email', 'treasurer@example.com')->first();
    $admin = User::where('email', 'admin@example.com')->first();

    expect($treasurer)->not->toBeNull()
        ->and($treasurer->role)->toEqual('collector')
        ->and($admin)->not->toBeNull()
        ->and($admin->role)->toEqual('admin');

    // Collections (fixed 3 in seeder) for treasurer
    $collections = Collection::where('user_id', $treasurer->id)->get();
    expect($collections->count())->toBe(3);

    // For each collection: guardians 3–5, expenses 2–4, and each guardian has 1–2 payments
    foreach ($collections as $collection) {
        $guardians = Guardian::where('collection_id', $collection->id)->get();
        expect($guardians->count())->toBeGreaterThanOrEqual(3)
            ->and($guardians->count())->toBeLessThanOrEqual(5);

        $expenses = Expense::where('collection_id', $collection->id)->get();
        expect($expenses->count())->toBeGreaterThanOrEqual(2)
            ->and($expenses->count())->toBeLessThanOrEqual(4);

        foreach ($guardians as $guardian) {
            $payments = Payment::where('collection_id', $collection->id)
                ->where('guardian_id', $guardian->id)
                ->get();

            expect($payments->count())->toBeGreaterThanOrEqual(1)
                ->and($payments->count())->toBeLessThanOrEqual(2);
        }
    }
});
