<?php

namespace App\Actions\Collections;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateCollectionAction
{
    public function execute(User $owner, array $data): Collection
    {
        return DB::transaction(function () use ($owner, $data) {
            return Collection::query()->create([
                'user_id' => $owner->id,
                'name' => $data['name'],
                'school_year' => $data['school_year'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);
        });
    }
}
