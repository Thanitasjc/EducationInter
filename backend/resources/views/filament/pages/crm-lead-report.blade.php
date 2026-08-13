<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3 mb-6">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs uppercase text-gray-500">Follow-ups due today</p>
            <p class="mt-1 text-3xl font-bold">{{ $this->getDueCount() }}</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/50 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Source</th>
                    <th class="px-4 py-3 font-semibold">Leads</th>
                    <th class="px-4 py-3 font-semibold">With application</th>
                    <th class="px-4 py-3 font-semibold">In pipeline+</th>
                    <th class="px-4 py-3 font-semibold">Success</th>
                    <th class="px-4 py-3 font-semibold">App rate</th>
                    <th class="px-4 py-3 font-semibold">Success rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getRows() as $row)
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 font-medium">{{ $row['source'] }}</td>
                        <td class="px-4 py-3">{{ $row['total'] }}</td>
                        <td class="px-4 py-3">{{ $row['with_application'] }}</td>
                        <td class="px-4 py-3">{{ $row['pipeline_converted'] }}</td>
                        <td class="px-4 py-3">{{ $row['success'] }}</td>
                        <td class="px-4 py-3">{{ $row['convert_rate'] }}%</td>
                        <td class="px-4 py-3">{{ $row['success_rate'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No lead data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
