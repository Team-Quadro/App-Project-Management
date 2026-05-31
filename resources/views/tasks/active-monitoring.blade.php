<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[13px]">
            <span class="text-ink-subtle">Global Monitoring</span>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">Task Active</span>
        </div>
    </x-slot>

    <div class="py-8 bg-canvas min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Title Page --}}
            <div class="flex flex-col gap-1 mb-2">
                <h1 class="text-2xl font-semibold text-ink tracking-tight">Monitoring Tugas Aktif</h1>
                <p class="text-[14px] text-ink-subtle">Memantau seluruh pekerjaan dengan status stage aktif (Doing) dari lintas proyek perusahaan.</p>
            </div>

            {{-- LOOPING GROUP BY PROJECT --}}
            @forelse($activeTasksGrouped as $projectTitle => $tasks)
                <div class="bg-surface-1 border border-hairline rounded-xl shadow-sm overflow-hidden p-5">

                    {{-- Project Title Header Group --}}
                    <div class="flex items-center justify-between border-b border-hairline/60 pb-3 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="p-1.5 bg-primary/10 rounded-lg text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-ink tracking-tight">{{ $projectTitle }}</h3>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ $tasks->count() }} Tugas Berjalan
                        </span>
                    </div>

                    {{-- Task List Rows (Tabel Grid Style mirip Kanban Board milikmu) --}}
                    <div class="space-y-1">
                        {{-- Mini Table Header --}}
                        <div class="grid grid-cols-[1fr_140px_110px_100px_90px] gap-3 px-4 py-1.5 text-[11px] font-medium text-ink-muted uppercase tracking-wider select-none">
                            <div>Nama Tugas</div>
                            <div>Ditugaskan</div>
                            <div>Tenggat Waktu</div>
                            <div>Prioritas</div>
                            <div>Stage</div>
                        </div>

                        {{-- Item Looping --}}
                        @foreach($tasks as $task)
                            <div class="grid grid-cols-[1fr_140px_110px_100px_90px] gap-3 items-center py-3 px-4 border border-hairline/40 rounded-lg bg-canvas/40 hover:bg-surface-2/40 hover:border-hairline transition-all group/row">

                                {{-- Task Title --}}
                                <div class="flex items-center gap-3">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0 shadow-sm" style="background-color: {{ $task->stage->color ?? '#6b7280' }}"></span>
                                    <span class="text-[13px] font-medium text-ink group-hover/row:text-primary transition-colors">{{ $task->title }}</span>
                                </div>

                                {{-- Assignee --}}
                                <div class="text-[12px] text-ink-subtle truncate flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full bg-gray-500 text-white flex items-center justify-center text-[8px] font-bold shrink-0">
                                        {{ strtoupper(substr($task->assignee->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <span class="truncate">{{ $task->assignee->name ?? 'Tidak Ditugaskan' }}</span>
                                </div>

                                {{-- Deadline --}}
                                <div class="text-[12px] {{ $task->deadline && $task->deadline->isPast() ? 'text-red-400 font-medium' : 'text-ink-subtle' }}">
                                    {{ $task->deadline ? $task->deadline->format('M j, Y') : '-' }}
                                </div>

                                {{-- Priority --}}
                                <div>
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-surface-3 text-ink-subtle">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>

                                {{-- Stage Badge --}}
                                <div class="text-[12px] text-ink-subtle font-medium">
                                    {{ $task->stage->name ?? $task->status }}
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            @empty
                {{-- Empty State State --}}
                <div class="bg-surface-1 border border-hairline rounded-xl text-center p-16 shadow-sm">
                    <div class="w-12 h-12 bg-surface-2 text-ink-muted rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-ink mb-1">Semua Pekerjaan Beres!</h3>
                    <p class="text-[13px] text-ink-subtle max-w-sm mx-auto">Tidak ada tugas dengan kategori stage aktif (Doing) yang sedang berjalan saat ini di seluruh proyek.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
