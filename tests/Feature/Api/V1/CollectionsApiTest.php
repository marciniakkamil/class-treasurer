<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Collection;
use App\Models\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

function bearer(User $user): array
{
    $token = $user->createToken('test')->plainTextToken;

    return ['Authorization' => 'Bearer '.$token];
}

it('rejects guest without token (401)', function () {
    getJson('/api/v1/collections')->assertUnauthorized();
});

it('allows collector to see only own collections', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);

    Collection::factory()->for($owner)->create(['name' => 'Owner A', 'is_active' => true]);
    Collection::factory()->for($other)->create(['name' => 'Other B', 'is_active' => true]);

    $res = getJson('/api/v1/collections', bearer($owner));

    $res->assertOk()
        ->assertSee('Owner A')
        ->assertDontSee('Other B');
});

it('allows admin to see all collections', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $c1 = Collection::factory()->create(['name' => 'C1', 'is_active' => true]);
    $c2 = Collection::factory()->create(['name' => 'C2', 'is_active' => false]);

    $res = getJson('/api/v1/collections', bearer($admin));

    $res->assertOk()->assertSee('C1')->assertSee('C2');
});

it('creates collection (201) and assigns to authenticated user', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    $payload = [
        'name' => 'Wycieczka szkolna',
        'school_year' => '2024/2025',
        'description' => 'Opis',
        'is_active' => true,
    ];

    $res = postJson('/api/v1/collections', $payload, bearer($collector));

    $res->assertCreated()
        ->assertJsonPath('data.name', 'Wycieczka szkolna');

    expect(Collection::query()->where('user_id', $collector->id)->where('name', 'Wycieczka szkolna')->exists())->toBeTrue();
});

it('validates on store (422)', function () {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);

    postJson('/api/v1/collections', ['name' => ''], bearer($collector))
        ->assertUnprocessable();
});

it('updates collection when owner (200) and forbids non-owner (403)', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $other = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $collection = Collection::factory()->for($owner)->create(['name' => 'Old']);

    // owner can update
    withHeaders(bearer($owner))
        ->putJson("/api/v1/collections/{$collection->id}", ['name' => 'New'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New');

    // other cannot
    withHeaders(bearer($other))
        ->putJson("/api/v1/collections/{$collection->id}", ['name' => 'Hack'])
        ->assertForbidden();
});

it('deletes collection when owner or admin', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $c1 = Collection::factory()->for($owner)->create();
    $c2 = Collection::factory()->create();

    withHeaders(bearer($owner))
        ->deleteJson("/api/v1/collections/{$c1->id}")
        ->assertNoContent();
    expect(Collection::withTrashed()->find($c1->id)->trashed())->toBeTrue();

    withHeaders(bearer($admin))
        ->deleteJson("/api/v1/collections/{$c2->id}")
        ->assertNoContent();
    expect(Collection::withTrashed()->find($c2->id)->trashed())->toBeTrue();
});

it('filters, sorts and paginates', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    Collection::factory()->for($owner)->create(['name' => 'Alpha', 'school_year' => '2023/2024', 'is_active' => true]);
    Collection::factory()->for($owner)->create(['name' => 'Beta', 'school_year' => '2024/2025', 'is_active' => false]);

    // filter by name
    getJson('/api/v1/collections?filters[name]=Alpha', bearer($owner))
        ->assertOk()
        ->assertSee('Alpha')
        ->assertDontSee('Beta');

    // filter by school_year and is_active
    getJson('/api/v1/collections?filters[school_year]=2024/2025&filters[is_active]=false', bearer($owner))
        ->assertOk()
        ->assertSee('Beta')
        ->assertDontSee('Alpha');

    // sort by name asc
    $res = getJson('/api/v1/collections?sort=name', bearer($owner))->assertOk();
    $data = $res->json('data');
    expect($data[0]['name'])->toBe('Alpha');

    // pagination per_page
    $res = getJson('/api/v1/collections?per_page=1&sort=name', bearer($owner))->assertOk();
    expect($res->json('meta.per_page') ?? $res->json('meta.per_page'))->toBe(1);
});

it('includes aggregates when requested', function () {
    $owner = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $c = Collection::factory()->for($owner)->create();

    $res = getJson("/api/v1/collections/{$c->id}?include[]=aggregates", bearer($owner))
        ->assertOk();

    expect(data_get($res->json(), 'data.aggregates'))->not->toBeNull();
});
