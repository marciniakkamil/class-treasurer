@volt
<?php
use function Livewire\Volt\{state, mount};
use App\Models\Collection;

state(['activeCollectionsCount' => 0]);

mount(function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    $this->activeCollectionsCount = Collection::query()
        ->visibleTo($user)
        ->active()
        ->count();
});
?>

<a href="{{ route('collections.index') }}"
   class="group relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
    <div class="flex h-full flex-col justify-between">
        <div class="flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
            <flux:icon name="layout-grid" class="size-5" />
            <span class="text-sm font-medium">Aktywne zbiórki</span>
        </div>
        <div class="text-4xl font-semibold text-neutral-900 dark:text-neutral-100">
            {{ $activeCollectionsCount }}
        </div>
        <div class="mt-2 text-sm text-blue-600 group-hover:underline dark:text-blue-400">
            Przejdź do listy →
        </div>
    </div>
</a>
@endvolt
