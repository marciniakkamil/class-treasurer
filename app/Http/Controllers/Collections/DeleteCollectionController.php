<?php

declare(strict_types=1);

namespace App\Http\Controllers\Collections;

use App\Actions\Collections\DeleteCollectionAction;
use App\Models\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

final class DeleteCollectionController
{
    use AuthorizesRequests;

    public function __invoke(Collection $collection, DeleteCollectionAction $action): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $action->execute($collection);

        session()->flash('success', 'Zbiórka została usunięta.');

        return redirect()->route('collections.index');
    }
}
