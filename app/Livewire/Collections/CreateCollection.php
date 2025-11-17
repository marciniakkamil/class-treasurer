<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
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

    public function save(): mixed
    {
        $this->authorize('create', Collection::class);
        $validated = $this->validate();

        /* @var \App\Models\User $user */
        $user = auth()->user();

        $collection = DB::transaction(function () use ($user, $validated) {
            return Collection::query()->create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'school_year' => $validated['school_year'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'active',
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);
        });

        session()->flash('success', 'Zbiórka została utworzona.');

        return redirect()->route('collections.show', $collection);
    }

    public function render(): View
    {
        return view('livewire.collections.create-collection');
    }
}
