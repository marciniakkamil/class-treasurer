<?php

namespace App\Livewire\Collections;

use App\Filters\CollectionFilters;
use App\Jobs\ExportCollectionsCsv;
use App\Models\Collection;
use App\Services\CollectionReadService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ListCollections extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    /**
     * Aggregate filters also passed in the query string as filters[...].
     *
     * @var array{name: string, school_year: string, is_active: string}
     */
    public array $filters = [
        'name' => '',
        'school_year' => '',
        'is_active' => '',
    ];

    public array $schoolYearOptions = [];

    public string $sort = '-created_at';

    public int $perPage = 15;

    /**
     * Deletion confirmation modal state.
     */
    public bool $showDeleteModal = false;

    public ?int $pendingDeleteId = null;

    public string $pendingDeleteName = '';

    protected array $queryString = [
        'filters' => [
            'except' => [
                'name' => '',
                'school_year' => '',
                'is_active' => '',
            ],
        ],
        'sort' => ['except' => '-created_at'],
        'perPage' => ['except' => 15],
    ];

    public function mount(): void
    {
        // Backwards compatibility with older URLs: ?name=&school_year=&is_active=
        $legacy = array_filter([
            'name' => request()->query('name'),
            'school_year' => request()->query('school_year'),
            'is_active' => request()->query('is_active'),
        ], static fn ($v) => $v !== null);

        if ($legacy !== []) {
            $this->filters = array_merge($this->filters, $legacy);
        }
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Dispatch the CSV export job to the queue.
     */
    public function exportCsv(): void
    {
        $this->authorize('viewAny', Collection::class);

        /* @var \App\Models\User $user */
        $user = auth()->user();

        dispatch(new ExportCollectionsCsv(
            userId: $user->id,
            filters: $this->filters,
            sort: $this->sort,
        ));

        session()->flash('export-status', 'Eksport CSV został rozpoczęty. Powiadomimy Cię po zakończeniu.');
    }

    public function confirmDelete(int $collectionId, string $collectionName): void
    {
        $this->pendingDeleteId = $collectionId;
        $this->pendingDeleteName = $collectionName;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->pendingDeleteName = '';
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'name' => '',
            'school_year' => '',
            'is_active' => '',
        ];
        $this->resetPage();
    }

    /**
     * @throws AuthorizationException
     */
    public function render(CollectionReadService $collectionReadService): View
    {
        $this->authorize('viewAny', Collection::class);

        /* @var \App\Models\User $user */
        $user = auth()->user();

        $this->schoolYearOptions = $collectionReadService->scholYearsOptions($user);

        $collections = $collectionReadService->paginateForList(
            $user,
            CollectionFilters::fromArray($this->filters),
            $this->perPage,
            $this->sort,
        );

        return view('livewire.collections.list-collections', compact('collections'));
    }

    /**
     * @param $notificationId
     * @return void
     */
    public function markNotificationAsRead($notificationId): void
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }
}
