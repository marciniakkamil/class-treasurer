<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Jobs\ExportCollectionsCsv;
use App\Livewire\Collections\ListCollections;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;

it('dispatches export job from ListCollections component', function (): void {
    $collector = User::factory()->create(['role' => UserRole::COLLECTOR]);
    $this->actingAs($collector);

    Bus::fake();

    Livewire::test(ListCollections::class)
        ->set('filters', [
            'name' => 'Trip',
            'school_year' => '2024/2025',
            'is_active' => '1',
        ])
        ->set('sort', '-name')
        ->call('exportCsv');

    Bus::assertDispatched(ExportCollectionsCsv::class, function (ExportCollectionsCsv $job) use ($collector): bool {
        expect($job->userId)->toBe($collector->id);
        expect($job->filters['name'])->toBe('Trip');
        expect($job->filters['school_year'])->toBe('2024/2025');
        expect($job->filters['is_active'])->toBe('1');
        expect($job->sort)->toBe('-name');

        return true;
    });
});
