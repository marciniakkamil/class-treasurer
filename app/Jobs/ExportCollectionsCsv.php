<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Filters\CollectionFilters;
use App\Models\User;
use App\Notifications\CollectionsExportReady;
use App\Services\CollectionReadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportCollectionsCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array{name: string, school_year: string, is_active: string}  $filters
     */
    public function __construct(public int $userId, public array $filters = [], public string $sort = '-created_at') {}

    public function handle(): void
    {
        /** @var User $user */
        $user = User::query()->findOrFail($this->userId);

        /** @var CollectionReadService $service */
        $service = app(CollectionReadService::class);

        $filters = CollectionFilters::fromArray($this->filters);

        $query = $service->queryForExport($user, $filters, $this->sort);

        $headers = [
            'ID',
            'Nazwa',
            'Rok szkolny',
            'Aktywna',
            'Utworzono',
        ];

        $stream = fopen('php://temp', 'w+');
        if ($stream === false) {
            return;
        }

        // Write BOM for Excel compatibility (UTF-8)
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, $headers, ';');

        $query->chunk(500, function ($rows) use ($stream): void {
            foreach ($rows as $row) {
                fputcsv($stream, [
                    $row->id,
                    $row->name,
                    $row->school_year,
                    $row->is_active ? 'tak' : 'nie',
                    $row->created_at?->toDateTimeString(),
                ], ';');
            }
        });

        rewind($stream);

        $dir = 'exports/'.$user->id;
        $filename = 'collections-'.now()->format('Ymd-His').'.csv';
        $path = $dir.'/'.$filename;

        Storage::disk('local')->put($path, stream_get_contents($stream) ?: '');

        fclose($stream);

        // Notify user that export is ready
        $user->notify(new CollectionsExportReady($path, $filename));
    }
}
