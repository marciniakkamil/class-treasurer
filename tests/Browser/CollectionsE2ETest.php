<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('przechodzi przepływ: logowanie → lista → filtr Aktywne → Szczegóły', function () {
    // Prepare data
    $password = 'password';
    /** @var User $user */
    // In Laravel 12, the User model uses the 'hashed' cast for the password,
    // so we can provide the plain password here and let Eloquent hash it.
    $user = User::factory()->create([
        'password' => $password,
        'role' => UserRole::COLLECTOR,
    ]);

    /** @var Collection $active */
    $active = Collection::factory()->for($user)->create([
        'name' => 'Aktywna Kolekcja UI',
        'is_active' => true,
    ]);

    /** @var Collection $inactive */
    $inactive = Collection::factory()->for($user)->create([
        'name' => 'Nieaktywna Kolekcja UI',
        'is_active' => false,
    ]);

    // In Pest v4 browser tests we can log in the user directly
    $this->actingAs($user);

    $page = visit(route('collections.index'));

    $page->assertSee('Zbiórki');

    // Set Status filter = "Aktywne" (value="1")
    $page->select('#status-filter', '1');

    // Verify the active one is visible and the inactive one is hidden
    $page->assertSee($active->name)
        ->assertDontSee($inactive->name);

    // Navigate to details using our stable data-testid selector
    $page->click('[data-testid="collection-details-'.$active->id.'"]');

    // Assertions on the details page
    $page->assertSee($active->name)
        ->assertSee((string) $active->school_year)
        ->assertNoJavascriptErrors();
});
