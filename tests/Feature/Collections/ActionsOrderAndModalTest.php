<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

it('shows delete action before details and uses modal instead of confirm', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($collector)->create(['name' => 'Test Zbiórka']);

    $this->actingAs($collector);

    $response = $this->get(route('collections.index'));

    $response->assertSuccessful();

    // Ensure the modal component markup exists (by name attribute)
    $response->assertSee('confirm-collection-deletion', escape: false);

    // Ensure no window.confirm usage remains
    $response->assertDontSee('confirm(');

    // Ensure order: the delete button (has sr-only label "Usuń") appears before the "Szczegóły" link
    $response->assertSeeInOrder([
        'Usuń',
        'Szczegóły',
    ], false);
});
