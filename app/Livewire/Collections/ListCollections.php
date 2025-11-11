<?php

namespace App\Livewire\Collections;

use App\Filters\CollectionFilters;
use App\Models\Collection;
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
     * Zbiorcze filtry przekazywane również w query string jako filters[...].
     *
     * @var array{name: string, school_year: string, is_active: string}
     */
    public array $filters = [
        'name' => '',
        'school_year' => '',
        'is_active' => '',
    ];

    public array $schoolYearOptions = [];

    protected $queryString = [
        'filters' => [
            'except' => [
                'name' => '',
                'school_year' => '',
                'is_active' => '',
            ],
        ],
    ];

    public function mount(): void
    {
        // Kompatybilność ze starszymi URL: ?name=&school_year=&is_active=
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
    public function render(): View
    {
        $this->authorize('viewAny', Collection::class);
        /* @var \App\Models\User $user */
        $user = auth()->user();

        $this->schoolYearOptions = Collection::query() // todo move queries to Service or Repository
            ->visibleTo($user)
            ->select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->filter()
            ->values()
            ->all();

        $collections = Collection::query()
            ->visibleTo($user)
            ->applyFilters(CollectionFilters::fromArray($this->filters))
            ->withDashboardAggregates()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.collections.list-collections', compact('collections'));
    }
}
