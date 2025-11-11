<div class="p-6 bg-white shadow-md rounded-2xl">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Szczegóły zbiórki</h1>
        <a href="{{ route('collections.index') }}" class="text-blue-600 hover:underline">← Powrót do listy</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <div class="mb-4">
                <div class="text-sm text-gray-500">Nazwa</div>
                <div class="text-lg font-medium text-gray-900">{{ $collection->name }}</div>
            </div>

            <div class="mb-4">
                <div class="text-sm text-gray-500">Rok szkolny</div>
                <div class="text-lg font-medium text-gray-900">{{ $collection->school_year ?: '—' }}</div>
            </div>

            <div class="mb-4">
                <div class="text-sm text-gray-500">Status</div>
                <div class="text-lg font-medium text-gray-900">
                    @if($collection->is_active)
                        <span class="text-green-700">Aktywna</span>
                    @else
                        <span class="text-gray-600">Nieaktywna</span>
                    @endif
                </div>
            </div>

            @if($collection->description)
                <div class="mb-4">
                    <div class="text-sm text-gray-500">Opis</div>
                    <div class="text-gray-800">{{ $collection->description }}</div>
                </div>
            @endif
        </div>

        <div>
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Rodzice</div>
                    <div class="text-xl font-semibold text-center">{{ $collection->guardians_count }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Wpłaty</div>
                    <div class="text-xl font-semibold text-center">{{ $collection->payments_count }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Wydatki</div>
                    <div class="text-xl font-semibold text-center">{{ $collection->expenses_count }}</div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-4">
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Suma wpłat</div>
                    <div class="text-xl font-semibold text-center">{{ number_format((float) ($collection->payments_sum_amount ?? 0), 2, ',', ' ') }} zł</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Suma wydatków</div>
                    <div class="text-xl font-semibold text-center">{{ number_format((float) ($collection->expenses_sum_amount ?? 0), 2, ',', ' ') }} zł</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3">
                    <div class="text-sm text-gray-500 text-center">Saldo</div>
                    <div class="text-xl font-semibold text-center">
                        @php($balance = (float) ($collection->payments_sum_amount ?? 0) - (float) ($collection->expenses_sum_amount ?? 0))
                        {{ number_format($balance, 2, ',', ' ') }} zł
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
