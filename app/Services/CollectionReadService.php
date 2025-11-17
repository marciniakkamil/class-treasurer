<?php
namespace App\Services;

use App\Filters\CollectionFilters;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CollectionReadService {

    /**
     * @param User $user
     * @return array
     */
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

    public function paginateForList(User $user, CollectionFilters $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Collection::query()
            ->visibleTo($user)
            ->applyFilters($filters)
            ->withDashboardAggregates()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
