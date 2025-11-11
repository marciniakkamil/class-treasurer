<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ShowCollection extends Component
{
    use AuthorizesRequests;

    public Collection $collection;

    public function mount(Collection $collection): void
    {
        $this->authorize('view', $collection);

        $collection->loadCount(['guardians', 'payments', 'expenses'])
            ->loadSum('payments', 'amount')
            ->loadSum('expenses', 'amount');

        $this->collection = $collection;
    }

    public function render(): View
    {
        return view('livewire.collections.show-collection');
    }
}
