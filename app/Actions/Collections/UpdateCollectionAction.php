<?php

namespace App\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class UpdateCollectionAction
{
    public function execute(Collection $collectionModel, array $validated): mixed
    {
        return DB::transaction(function () use ($validated, $collectionModel): void {
            $collectionModel->fill([
                'name' => $validated['name'],
                'school_year' => ($validated['school_year'] ?? '') !== '' ? $validated['school_year'] : null,
                'description' => ($validated['description'] ?? '') !== '' ? $validated['description'] : null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ])->save();
        });
    }
}
