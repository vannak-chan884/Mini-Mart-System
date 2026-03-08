@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="font-playfair text-2xl font-bold text-[#0D0D14] dark:text-white">Activity Log</h2>
            <p class="text-sm text-[#6B7280] dark:text-[#9CA3AF] mt-0.5">Track all user actions across the system</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.activity-logs.index') }}"
          class="bg-white dark:bg-[#13131F] rounded-2xl border border-black/[0.08] dark:border-white/[0.07]
                 shadow-[0_2px_16px_rgba(0,0,0,0.06)] p-4">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- User filter --}}
            <div class="flex flex-col gap-1 min-w-[160px]">
                <label class="text-[11px] font-bold uppercase tracking-wide text-[#9CA3AF]">User</label>
                <select name="user_id"
                    class="px-3 py-2 rounded-[8px] text-sm bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087]">
                    <option value="">All Users</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Module filter --}}
            <div class="flex flex-col gap-1 min-w-[140px]">
                <label class="text-[11px] font-bold uppercase tracking-wide text-[#9CA3AF]">Module</label>
                <select name="module"
                    class="px-3 py-2 rounded-[8px] text-sm bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087]">
                    <option value="">All Modules</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>
                            {{ $mod }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action filter --}}
            <div class="flex flex-col gap-1 min-w-[130px]">
                <label class="text-[11px] font-bold uppercase tracking-wide text-[#9CA3AF]">Action</label>
                <select name="action"
                    class="px-3 py-2 rounded-[8px] text-sm bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087]">
                    <option value="">All Actions</option>
                    @foreach (['login','logout','created','updated','deleted','checkout'] as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ ucfirst($act) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date filter --}}
            <div class="flex flex-col gap-1 min-w-[150px]">
                <label class="text-[11px] font-bold uppercase tracking-wide text-[#9CA3AF]">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="px-3 py-2 rounded-[8px] text-sm bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087]">
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 pb-0.5">
                <button type="submit"
                    class="px-4 py-2 rounded-[8px] text-sm font-semibold text-white cursor-pointer border-0
                           bg-gradient-to-r from-[#003087] to-[#1a4db3]
                           hover:from-[#002570] hover:to-[#1541a0] transition-all duration-150">
                    Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}"
                    class="px-4 py-2 rounded-[8px] text-sm font-semibold no-underline
                           text-[#6B7280] dark:text-[#9CA3AF]
                           bg-black/[0.04] dark:bg-white/[0.04]
                           border border-black/[0.08] dark:border-white/[0.07]
                           hover:text-[#0D0D14] dark:hover:text-white transition-all duration-150">
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#13131F] rounded-2xl border border-black/[0.08] dark:border-white/[0.07]
                shadow-[0_2px_16px_rgba(0,0,0,0.06)] overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-black/[0.06] dark:border-white/[0.06]
                               bg-black/[0.02] dark:bg-white/[0.02]">
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">Time</th>
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">User</th>
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">Action</th>
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">Module</th>
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">Description</th>
                        <th class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- Time --}}
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-mono-ibm text-xs text-[#6B7280] dark:text-[#9CA3AF]">
                                    {{ $log->created_at->setTimezone('Asia/Phnom_Penh')->format('d M Y') }}
                                </div>
                                <div class="font-mono-ibm text-[11px] text-[#9CA3AF] dark:text-[#555968]">
                                    {{ $log->created_at->setTimezone('Asia/Phnom_Penh')->format('h:i:s A') }}
                                </div>
                            </td>

                            {{-- User --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-[6px] flex items-center justify-center text-[10px]
                                                font-bold text-white flex-shrink-0
                                                bg-gradient-to-br from-[#003087] to-[#1a4db3]">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-[13px] font-medium text-[#0D0D14] dark:text-white whitespace-nowrap">
                                        {{ $log->user->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Action badge --}}
                            <td class="px-5 py-3.5">
                                @php
                                    $colors = [
                                        'login'    => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        'logout'   => 'bg-gray-100 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                                        'created'  => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'updated'  => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        'deleted'  => 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800',
                                        'checkout' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                    ];
                                    $cls = $colors[$log->action] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px]
                                             font-semibold border {{ $cls }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>

                            {{-- Module --}}
                            <td class="px-5 py-3.5">
                                <span class="text-[12px] font-semibold text-[#6B7280] dark:text-[#9CA3AF]
                                             bg-black/[0.04] dark:bg-white/[0.04]
                                             px-2 py-0.5 rounded-md border border-black/[0.06] dark:border-white/[0.06]">
                                    {{ $log->module }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="px-5 py-3.5 text-[13px] text-[#374151] dark:text-[#D1D5DB] max-w-xs">
                                {{ $log->description }}

                                @if ($log->properties && count($log->properties) > 0)
                                    <div class="mt-1.5 space-y-0.5">
                                        @foreach ($log->properties as $field => $change)
                                            @if (is_array($change) && isset($change['from']))
                                                {{-- ✅ Correct format: field => {from, to} --}}
                                                <div class="flex items-center gap-1.5 text-[11px]">
                                                    <span class="font-semibold text-[#6B7280] capitalize">
                                                        {{ str_replace('_', ' ', $field) }}:
                                                    </span>
                                                    <span class="line-through text-red-400">{{ $change['from'] ?? '—' }}</span>
                                                    <span class="text-[#9CA3AF]">→</span>
                                                    <span class="text-emerald-500 font-medium">{{ $change['to'] ?? '—' }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="px-5 py-3.5 font-mono-ibm text-xs text-[#9CA3AF] dark:text-[#555968] whitespace-nowrap">
                                {{ $log->ip_address ?? '—' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="text-4xl mb-3">📋</div>
                                <div class="text-[#6B7280] dark:text-[#9CA3AF] text-sm">No activity found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="px-5 py-4 border-t border-black/[0.06] dark:border-white/[0.06]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection