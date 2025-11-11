<div class="p-6 bg-white shadow-md rounded-2xl">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Nowa zbiórka</h1>
        <a href="{{ route('collections.index') }}" class="text-blue-600 hover:underline">← Powrót do listy</a>
    </div>

    <form wire:submit.prevent="save" class="grid gap-4 max-w-3xl">
        <flux:field label="Nazwa" :error="$errors->first('name')">
            <flux:input wire:model.live="name" placeholder="np. Wycieczka do teatru" />
        </flux:field>

        <div class="grid md:grid-cols-2 gap-4">
            <flux:field label="Rok szkolny" :hint="'np. 2024/2025'" :error="$errors->first('school_year')">
                <flux:input wire:model.live="school_year" placeholder="2024/2025" />
            </flux:field>

            <flux:field label="Aktywna" :error="$errors->first('is_active')">
                <div class="flex items-center h-10">
                    <flux:switch wire:model="is_active" />
                    <span class="ml-2 text-sm text-gray-700">Zbiórka jest aktywna</span>
                </div>
            </flux:field>
        </div>

        <flux:field label="Opis" :error="$errors->first('description')">
            <flux:textarea wire:model.live="description" placeholder="Opcjonalny opis zbiórki" rows="4" />
        </flux:field>

        <div class="flex items-center gap-3 pt-2">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                Zapisz
            </flux:button>
            <a href="{{ route('collections.index') }}" class="text-gray-700 hover:underline">Anuluj</a>
        </div>
    </form>
</div>
