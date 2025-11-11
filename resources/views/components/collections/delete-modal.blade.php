{{-- Deprecated: Use the generic <x-confirm-modal> component instead. --}}
@props([
    // Arbitrary attributes like: name, wire:model, class will be passed via $attributes
    'pendingId' => null,
    'pendingName' => '',
    'deleteUrl' => null,
])

<flux:modal {{ $attributes->merge(['class' => 'max-w-lg']) }}>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Potwierdź usunięcie</flux:heading>
            <flux:text>
                Czy na pewno chcesz usunąć zbiórkę: <strong>{{ $pendingName }}</strong>? Tej operacji nie można cofnąć.
            </flux:text>
        </div>

        @if($pendingId)
            <form method="POST" action="{{ $deleteUrl }}" class="flex justify-end gap-2">
                @csrf
                @method('DELETE')

                <flux:modal.close>
                    <flux:button type="button" variant="filled" wire:click="cancelDelete">
                        Anuluj
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">
                    Usuń
                </flux:button>
            </form>
        @else
            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button type="button" variant="filled" wire:click="cancelDelete">
                        Zamknij
                    </flux:button>
                </flux:modal.close>
            </div>
        @endif
    </div>
</flux:modal>
