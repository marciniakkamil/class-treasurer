<?php

use App\Models\User;

it('returns true for admin users using isAdmin and false otherwise', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $collector = User::factory()->create(['role' => 'collector']);
    $other = User::factory()->create(['role' => 'member']);

    expect($admin->isAdmin())->toBeTrue()
        ->and($collector->isAdmin())->toBeFalse()
        ->and($other->isAdmin())->toBeFalse();
});

it('returns true for collectors using isCollector and false otherwise', function () {
    $collector = User::factory()->create(['role' => 'collector']);
    $admin = User::factory()->create(['role' => 'admin']);
    $other = User::factory()->create(['role' => 'member']);

    expect($collector->isCollector())->toBeTrue()
        ->and($admin->isCollector())->toBeFalse()
        ->and($other->isCollector())->toBeFalse();
});
