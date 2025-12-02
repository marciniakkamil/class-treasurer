<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Jobs\ExportCollectionsCsv;
use App\Models\Collection;
use App\Models\User;
use App\Notifications\CollectionsExportReady;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

it('creates CSV file and sends notification', function (): void {
    Storage::fake('local');
    Notification::fake();

    $user = User::factory()->create(['role' => UserRole::COLLECTOR]);

    // Create some collections for export
    Collection::factory()->for($user)->create(['name' => 'Alpha', 'school_year' => '2024/2025', 'is_active' => true]);
    Collection::factory()->for($user)->create(['name' => 'Beta', 'school_year' => '2023/2024', 'is_active' => false]);

    $job = new ExportCollectionsCsv(userId: $user->id, filters: [
        'name' => '',
        'school_year' => '',
        'is_active' => '',
    ], sort: 'name');

    // Run the job synchronously
    $job->handle();

    // Assert notification was sent
    Notification::assertSentTo($user, CollectionsExportReady::class, function (CollectionsExportReady $n) use ($user): bool {
        // Ensure file exists in local storage
        expect($n->path)->toStartWith('exports/'.$user->id.'/');
        Storage::disk('local')->assertExists($n->path);

        return true;
    });
});
