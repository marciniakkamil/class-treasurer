<div class="p-6 bg-white shadow-md rounded-2xl">
    <h1 class="text-2xl font-semibold mb-4">Zbiórki</h1>

    @if($collections->isEmpty())
        <p class="text-gray-600">Brak zbiórek do wyświetlenia.</p>
    @else
        <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
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
                    <td class="p-2 text-center">
{{--                        <a href="{{ route('collections.show', $collection) }}"--}}
{{--                           class="text-blue-600 hover:underline font-medium">--}}
{{--                            Szczegóły--}}
{{--                        </a>--}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <div class="mt-4">
        @can('create', App\Models\Collection::class)
{{--            <a href="{{ route('collections.create') }}"--}}
{{--               class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">--}}
{{--                ➕ Nowa zbiórka--}}
{{--            </a>--}}
        @endcan
    </div>
</div>
