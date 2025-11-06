<?php

namespace App\Livewire\Collections;

use App\Filters\CollectionFilters;
use App\Models\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class ListCollections extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $name = '';

    public string $school_year = '';

    public string $is_active = '';

    public array $schoolYearOptions = [];

    protected $queryString = [
        'name' => ['except' => ''],
        'school_year' => ['except' => ''],
        'is_active' => ['except' => ''],
    ];

    public function updatedName(): void
    {
        $this->resetPage();
    }

    public function updatedSchoolYear(): void
    {
        $this->resetPage();
    }

    public function updatedIsActive(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->name = '';
        $this->school_year = '';
        $this->is_active = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorize('viewAny', Collection::class);
        /* @var \App\Models\User $user */
        $user = auth()->user();

        $this->schoolYearOptions = Collection::query()
            ->visibleTo($user)
            ->select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->filter()
            ->values()
            ->all();

        $filters = CollectionFilters::fromArray([
            'name' => $this->name,
            'school_year' => $this->school_year,
            'is_active' => $this->is_active,
        ]);

        $collections = Collection::query()
            ->visibleTo($user)
            ->applyFilters($filters)
            ->withDashboardAggregates()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.collections.list-collections', compact('collections'));
    }
}
