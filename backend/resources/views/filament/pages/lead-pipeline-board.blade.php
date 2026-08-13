<x-filament-panels::page>
    <div class="flex gap-3 overflow-x-auto pb-4">
        @foreach ($this->getColumns() as $status => $leads)
            <div class="min-w-[240px] w-60 shrink-0 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-200">
                        {{ $status }}
                    </span>
                    <span class="text-xs font-semibold text-gray-500">{{ $leads->count() }}</span>
                </div>
                <div class="space-y-2 p-2 max-h-[70vh] overflow-y-auto">
                    @forelse ($leads as $lead)
                        <div wire:key="lead-{{ $lead->id }}" class="rounded-lg bg-white dark:bg-gray-800 p-3 shadow-sm border border-gray-100 dark:border-gray-700 space-y-2">
                            <a href="{{ $this->leadEditUrl($lead) }}" class="block font-semibold text-sm text-primary-600 hover:underline">
                                {{ $lead->name }}
                            </a>
                            <p class="text-xs text-gray-500 truncate">{{ $lead->email ?: $lead->phone ?: '—' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $lead->assignee?->name ?? 'Unassigned' }}</p>
                            <div class="flex flex-wrap gap-1 pt-1">
                                @php $next = $this->nextStatus($status); @endphp
                                @if ($next)
                                    <x-filament::button
                                        size="xs"
                                        color="gray"
                                        wire:click="advanceLead({{ $lead->id }}, '{{ $next }}')"
                                        wire:loading.attr="disabled"
                                    >
                                        → {{ $next }}
                                    </x-filament::button>
                                @endif
                                @if ($this->canConvert($lead))
                                    <x-filament::button
                                        size="xs"
                                        color="success"
                                        wire:click="convertLead({{ $lead->id }})"
                                        wire:loading.attr="disabled"
                                    >
                                        Convert
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
