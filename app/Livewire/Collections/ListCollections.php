<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ListCollections extends Component
{
    use AuthorizesRequests;

    public $collections;

    public function mount(): void
    {
        $this->authorize('viewAny', Collection::class);

        $this->collections = Collection::query()
            ->withCount(['guardians', 'payments', 'expenses'])
            ->withSum('payments', 'amount')
            ->withSum('expenses', 'amount')
            ->visibleTo(auth()->user())
            ->get();
    }

    public function render()
    {
        return view('livewire.collections.list-collections');
    }
}
