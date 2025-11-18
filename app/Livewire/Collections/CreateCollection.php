<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Actions\Collections\CreateCollectionAction;
use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateCollection extends Component
{
    use AuthorizesRequests;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $school_year = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    public function mount(): void
    {
        $this->authorize('create', Collection::class);
    }

    public function save(CreateCollectionAction $action): mixed
    {
        $this->authorize('create', Collection::class);
        $validated = $this->validate();

        /* @var \App\Models\User $user */
        $user = auth()->user();

        $collection = $action->execute($user, $validated);

        session()->flash('success', 'Zbiórka została utworzona.');

        return redirect()->route('collections.show', $collection);
    }

    public function render(): View
    {
        return view('livewire.collections.create-collection');
    }
}
