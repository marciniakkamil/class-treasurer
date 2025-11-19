<?php

namespace App\Services;

use App\Filters\CollectionFilters;
use App\Models\Builders\CollectionBuilder;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CollectionReadService
{
    public function scholYearsOptions(User $user): array
    {
        return Collection::query()
            ->visibleTo($user)
            ->select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year')
            ->filter()
            ->values()
            ->all();
    }

    public function paginateForList(
        User $user,
        CollectionFilters $filters,
        int $perPage = 15,
        string $sort = '-created_at'
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 100));

        /** @var CollectionBuilder $query */
        $query = Collection::query()
            ->visibleTo($user)
            ->applyFilters($filters)
            ->withDashboardAggregates();

        $this->applySorting($query, $sort);

        return $query->paginate($perPage);
    }

    /**
     * Paginate for API with optional aggregates and safe sorting.
     */
    public function paginateForApi(
        User $user,
        CollectionFilters $filters,
        string $sort = '-created_at',
        int $perPage = 15,
        bool $withAggregates = false
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 100));

        /** @var CollectionBuilder $query */
        $query = Collection::query()
            ->visibleTo($user)
            ->applyFilters($filters);

        if ($withAggregates) {
            $query->withDashboardAggregates();
        }

        $this->applySorting($query, $sort);

        return $query->paginate($perPage);
    }

    /**
     * Apply safe sorting rules shared by API/UI.
     */
    private function applySorting(CollectionBuilder $query, string $sortParam): void
    {
        $fields = array_filter(array_map('trim', explode(',', $sortParam)));
        $allowed = [
            'created_at' => 'created_at',
            'name' => 'name',
            'school_year' => 'school_year',
        ];

        if ($fields === []) {
            $query->orderByDesc('created_at');

            return;
        }

        foreach ($fields as $field) {
            $direction = 'asc';
            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }

            if (! array_key_exists($field, $allowed)) {
                continue;
            }

            $query->orderBy($allowed[$field], $direction);
        }
    }
}
