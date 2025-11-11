<?php

use App\Enums\UserRole;
use App\Models\User;

it('returns true for admin users using isAdmin and false otherwise', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    expect($admin->isAdmin())->toBeTrue()
        ->and($collector->isAdmin())->toBeFalse();
});

it('returns true for collectors using isCollector and false otherwise', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    expect($collector->isCollector())->toBeTrue()
        ->and($admin->isCollector())->toBeFalse();
});
