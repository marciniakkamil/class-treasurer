<div class="p-6 bg-white shadow-md rounded-2xl">
    <h1 class="text-2xl font-semibold mb-4">Zbiórki</h1>

    <!-- Filtry todo przerobic na komponent -->
    <div class="mb-4 grid gap-3 md:grid-cols-4">
        <div>
            <flux:field label="Nazwa">
                <flux:input
                    wire:model.live.debounce.300ms="filters.name"
                    placeholder="Szukaj po nazwie..."
                />
            </flux:field>
        </div>
        <div>
            <flux:field label="Rok szkolny">
                <flux:select wire:model.live="filters.school_year">
                    <option value="">Wszystkie</option>
                    @foreach($this->schoolYearOptions as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
        <div>
            <flux:field label="Status">
                <flux:select wire:model.live="filters.is_active">
                    <option value="">Wszystkie</option>
                    <option value="1">Aktywne</option>
                    <option value="0">Nieaktywne</option>
                </flux:select>
            </flux:field>
        </div>
        <div class="flex items-end">
            <flux:button wire:click="clearFilters" variant="outline" class="w-full" wire:loading.attr="disabled">
                Wyczyść filtry
            </flux:button>
        </div>
    </div>

    @if($collections->isEmpty())
        <p class="text-gray-600">Brak zbiórek do wyświetlenia.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50">
                <tr>
                    <th class="p-2 border-b">Nazwa</th>
                    <th class="p-2 border-b">Rok szkolny</th>
                    <th class="p-2 border-b text-center">Rodzice</th>
                    <th class="p-2 border-b text-center">Wpłaty</th>
                    <th class="p-2 border-b text-center">Wydatki</th>
                    <th class="p-2 border-b text-center">Saldo</th>
                    <th class="p-2 border-b text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                @foreach($collections as $collection)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 font-medium text-gray-800">{{ $collection->name }}</td>
                        <td class="p-2">{{ $collection->school_year }}</td>
                        <td class="p-2 text-center">{{ $collection->guardians_count }}</td>
                        <td class="p-2 text-center">{{ $collection->payments_count }}</td>
                        <td class="p-2 text-center">{{ $collection->expenses_count }}</td>
                        <td class="p-2 text-center">
                            {{ ($collection->payments_sum_amount ?? 0) - ($collection->expenses_sum_amount ?? 0) }} zł
                        </td>
                        <td class="p-2">
                            <div class="flex items-center justify-center gap-3">
                                @can('update', $collection)
                                    <a href="{{ route('collections.edit', $collection) }}" class="inline-flex items-center cursor-pointer" title="Edytuj">
                                        <flux:icon name="pencil-square" class="size-5 text-blue-600" />
                                        <span class="sr-only">Edytuj</span>
                                    </a>
                                @endcan

                                @can('delete', $collection)
                                    <button type="button" class="inline-flex items-center cursor-pointer" title="Usuń"
                                            wire:click="confirmDelete({{ $collection->id }}, @js($collection->name))">
                                        <flux:icon name="trash" class="size-5 text-blue-600" />
                                        <span class="sr-only">Usuń</span>
                                    </button>
                                @endcan

                                <a href="{{ route('collections.show', $collection) }}"
                                   class="text-blue-600 hover:underline font-medium rounded-md inline-flex items-center gap-1">
                                    <span>Szczegóły</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginacja -->
        <div class="mt-4">
            {{ $collections->links() }}
        </div>
    @endif

    <div class="mt-4">
        @can('create', App\Models\Collection::class)
            <a href="{{ route('collections.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <flux:icon name="plus" class="size-5" />
                <span>Nowa zbiórka</span>
            </a>
        @endcan
    </div>

    <x-confirm-modal
        name="confirm-collection-deletion"
        wire:model="showDeleteModal"
        :title="'Potwierdź usunięcie'"
        :description="'Czy na pewno chcesz usunąć zbiórkę: <strong>' . e($pendingDeleteName) . '</strong>? Tej operacji nie można cofnąć.'"
        :confirm-url="$pendingDeleteId ? route('collections.delete', $pendingDeleteId) : null"
        confirm-http="DELETE"
        confirm-label="Usuń"
        cancel-label="Anuluj"
        variant="danger"
        cancel-method="cancelDelete"
    />
</div>
