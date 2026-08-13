<x-filament-panels::page>
    <div class="flex gap-3 overflow-x-auto pb-4">
        @foreach ($this->getColumns() as $status => $apps)
            <div class="min-w-[240px] w-60 shrink-0 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-200">
                        {{ str_replace('_', ' ', $status) }}
                    </span>
                    <span class="text-xs font-semibold text-gray-500">{{ $apps->count() }}</span>
                </div>
                <div class="space-y-2 p-2 max-h-[70vh] overflow-y-auto">
                    @forelse ($apps as $app)
                        <div wire:key="app-{{ $app->id }}" class="rounded-lg bg-white dark:bg-gray-800 p-3 shadow-sm border border-gray-100 dark:border-gray-700 space-y-2">
                            <a href="{{ $this->applicationEditUrl($app) }}" class="block font-semibold text-sm text-primary-600 hover:underline">
                                {{ $app->application_no }}
                            </a>
                            <p class="text-xs text-gray-500 truncate">{{ $app->student?->user?->name ?? '—' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $app->consultant?->name ?? 'Unassigned' }}</p>
                            @if ($app->next_action)
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 truncate">{{ $app->next_action }}</p>
                            @endif
                            <div class="flex flex-wrap gap-1 pt-1">
                                @php $next = $this->nextStatus($status); @endphp
                                @if ($next)
                                    <x-filament::button
                                        size="xs"
                                        color="gray"
                                        wire:click="advanceApplication({{ $app->id }}, '{{ $next }}')"
                                        wire:loading.attr="disabled"
                                    >
                                        → {{ str_replace('_', ' ', $next) }}
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 px-1 py-4 text-center">Empty</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
