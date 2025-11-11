<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Eloquent Builder for the Collection model.
 */
class CollectionBuilder extends Builder
{
    /**
     * Limit collections visible to a given user (admin: all, collector: own, others: none).
     */
    public function visibleTo(User $user): self
    {
        if ($user->isAdmin()) {
            return $this;
        }

        if ($user->isCollector()) {
            return $this->where('user_id', $user->id);
        }

        return $this->whereRaw('1 = 0');
    }

    /**
     * Shortcut for only active collections.
     */
    public function active(): self
    {
        return $this->where('is_active', true);
    }

    /**
     * Apply common filters in a safe, optional way.
     *
     * @param  \App\Filters\CollectionFilters  $filters  Normalized filters
     */
    public function applyFilters(\App\Filters\CollectionFilters $filters): self
    {
        $name = $filters->name;
        $schoolYear = $filters->schoolYear;
        $isActive = $filters->isActive; // already normalized to ?bool

        $this
            ->when($name !== '', function (self $q) use ($name) {
                $term = "%{$name}%";
                $q->where('name', 'like', $term);
            })
            ->when($schoolYear !== '', function (self $q) use ($schoolYear) {
                $q->where('school_year', $schoolYear);
            })
            ->when($isActive !== null, function (self $q) use ($isActive) {
                $q->where('is_active', $isActive);
            });

        return $this;
    }

    /**
     * payments_sum_amount, expenses_sum_amount
     */
    public function withFinancialSums(): self
    {
        return $this
            ->withSum('payments', 'amount')
            ->withSum('expenses', 'amount');
    }

    /**
     * Adds common counts and sums used on list/dashboard views.
     */
    public function withDashboardAggregates(): self
    {
        return $this
            ->withCount([
                'guardians',
                'payments',
                'expenses',
            ])
            ->withFinancialSums();
    }
}
