<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditCollection extends Component
{
    use AuthorizesRequests;

    public Collection $collectionModel;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $school_year = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    public function mount(Collection|int|string $collection): void
    {
        if (! $collection instanceof Collection) {
            $collection = Collection::query()->findOrFail($collection);
        }

        $this->authorize('update', $collection);

        $this->collectionModel = $collection;

        $this->name = (string) $collection->name;
        $this->school_year = (string) ($collection->school_year ?? '');
        $this->description = (string) ($collection->description ?? '');
        $this->is_active = (bool) $collection->is_active;
    }

    public function update(): mixed
    {
        $this->authorize('update', $this->collectionModel);

        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $this->collectionModel->fill([
                'name' => $validated['name'],
                'school_year' => ($validated['school_year'] ?? '') !== '' ? $validated['school_year'] : null,
                'description' => ($validated['description'] ?? '') !== '' ? $validated['description'] : null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ])->save();
        });

        session()->flash('success', 'Zbiórka została zaktualizowana.');

        return redirect()->route('collections.show', $this->collectionModel);
    }

    public function render(): View
    {
        return view('livewire.collections.edit-collection');
    }
}
