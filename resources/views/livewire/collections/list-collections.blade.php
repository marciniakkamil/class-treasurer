<div class="p-6 bg-white shadow-md rounded-2xl">
    <h1 class="text-2xl font-semibold mb-4">Zbiórki</h1>

    <!-- Filters (todo: refactor into components) -->
    <div class="mb-4">
        <div class="grid gap-3 md:grid-cols-4">
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
                    <flux:select id="status-filter" wire:model.live="filters.is_active" data-testid="status-filter">
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
    </div>

    <!-- Sorting and pagination -->
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            @if (session('export-status'))
                <div class="mb-2 text-green-700">
                    {{ session('export-status') }}
                </div>
            @endif
            <flux:button wire:click="exportCsv" variant="primary" wire:loading.attr="disabled">
                Eksportuj CSV
            </flux:button>
        </div>
        <div class="md:w-64">
            <div class="md:w-24 mb-2">
                <flux:heading>Sortuj wg:</flux:heading>
            </div>
            <flux:field label="Sortuj wg">
                <flux:select wire:model.live="sort">
                    <option value="-created_at">Najnowsze</option>
                    <option value="created_at">Najstarsze</option>
                    <option value="name">Nazwa A→Z</option>
                    <option value="-name">Nazwa Z→A</option>
                    <option value="school_year">Rok szkolny rosnąco</option>
                    <option value="-school_year">Rok szkolny malejąco</option>
                </flux:select>
            </flux:field>
        </div>
        <div class="md:w-48">
            <div class="md:w-24 mb-2">
                <flux:heading>Na stronę:</flux:heading>
            </div>
            <flux:field label="Na stronę">
                <flux:select wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </flux:select>
            </flux:field>
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
                                   data-testid="collection-details-{{ $collection->id }}"
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

        <!-- Pagination -->
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
