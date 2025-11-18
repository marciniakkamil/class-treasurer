<?php

namespace App\Actions\Collections;

use Illuminate\Support\Facades\DB;

class DeleteCollectionAction
{
    public function execute(mixed $collection): mixed
    {
        return DB::transaction(function () use ($collection) {
            $collection->delete();
        });
    }
}
