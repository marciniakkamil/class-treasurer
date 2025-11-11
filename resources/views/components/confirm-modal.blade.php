    @props([
        'title' => 'Potwierdzenie',
        'description' => null,
        'confirmLabel' => 'Potwierdź',
        'cancelLabel' => 'Anuluj',
        // If provided, will submit a form to this URL
        'confirmUrl' => null,
        // HTTP method for the form (POST, DELETE, PUT, PATCH)
        'confirmHttp' => 'POST',
        // If provided and no confirmUrl, will call a Livewire method
        'confirmMethod' => null,
        // Optional: parameters for the Livewire method (array/json-serializable)
        'confirmParams' => null,
        // Optional: Livewire cancel method to call when closing
        'cancelMethod' => null,
        // Visual intent for confirm button
        'variant' => 'primary', // primary|danger|outline etc.
    ])

<flux:modal {{ $attributes->merge(['class' => 'max-w-lg']) }}>
    <div class="space-y-6">
        <div class="space-y-2">
            <flux:heading size="lg">{{ $title }}</flux:heading>
            @if($description)
                <flux:text>{!! $description !!}</flux:text>
            @endif
        </div>

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                @if($cancelMethod)
                    <flux:button type="button" variant="filled" wire:click="{{ $cancelMethod }}">
                        {{ $cancelLabel }}
                    </flux:button>
                @else
                    <flux:button type="button" variant="filled">
                        {{ $cancelLabel }}
                    </flux:button>
                @endif
            </flux:modal.close>

            @if($confirmUrl)
                <form method="POST" action="{{ $confirmUrl }}">
                    @csrf
                    @php($method = strtoupper($confirmHttp))
                    @if(!in_array($method, ['GET','POST']))
                        @method($method)
                    @endif
                    <flux:button type="submit" variant="{{ $variant === 'danger' ? 'danger' : 'primary' }}">
                        {{ $confirmLabel }}
                    </flux:button>
                </form>
            @elseif($confirmMethod)
                @php($params = is_array($confirmParams) ? \Illuminate\Support\Js::from($confirmParams) : null)
                @if($params !== null)
                    <flux:button type="button"
                                 variant="{{ $variant === 'danger' ? 'danger' : 'primary' }}"
                                 wire:click="{{ $confirmMethod }}({{ $params }})"
                    >
                        {{ $confirmLabel }}
                    </flux:button>
                @else
                    <flux:button type="button"
                                 variant="{{ $variant === 'danger' ? 'danger' : 'primary' }}"
                                 wire:click="{{ $confirmMethod }}"
                    >
                        {{ $confirmLabel }}
                    </flux:button>
                @endif
            @endif
        </div>
    </div>
</flux:modal>
