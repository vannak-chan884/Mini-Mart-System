@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-playfair text-2xl font-bold text-[#0D0D14] dark:text-white">Users</h2>
                <p class="text-sm text-[#6B7280] dark:text-[#9CA3AF] mt-0.5">Manage admin and cashier accounts</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px] text-sm font-semibold
                   text-white bg-gradient-to-r from-[#003087] to-[#1a4db3]
                   hover:from-[#002570] hover:to-[#1541a0]
                   shadow-[0_4px_14px_rgba(0,48,135,0.35)]
                   transition-all duration-200 no-underline">
                <span>＋</span> Add User
            </a>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 px-4 py-3 rounded-[10px] text-sm font-medium
                    bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400
                    border border-emerald-200 dark:border-emerald-800">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="flex items-center gap-3 px-4 py-3 rounded-[10px] text-sm font-medium
                    bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400
                    border border-red-200 dark:border-red-800">
                <span>⚠️</span> {{ session('error') }}
            </div>
        @endif

        {{-- Table card --}}
        <div
            class="bg-white dark:bg-[#13131F] rounded-2xl border border-black/[0.08] dark:border-white/[0.07]
                shadow-[0_2px_16px_rgba(0,0,0,0.06)] overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-black/[0.06] dark:border-white/[0.06]
                               bg-black/[0.02] dark:bg-white/[0.02]">
                            <th
                                class="text-left px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                #</th>
                            <th
                                class="text-left px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                Name</th>
                            <th
                                class="text-left px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                Email</th>
                            <th
                                class="text-left px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                Role</th>
                            <th
                                class="text-left px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                Joined</th>
                            <th
                                class="text-right px-6 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase
                                   text-[#9CA3AF] dark:text-[#6B7280]">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                        @forelse ($users as $user)
                            <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-colors duration-150">
                                <td class="px-6 py-4 font-mono-ibm text-xs text-[#9CA3AF]">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-[9px] flex items-center justify-center text-[13px]
                                                font-bold text-white flex-shrink-0
                                                bg-gradient-to-br from-[#003087] to-[#1a4db3]">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-[#0D0D14] dark:text-white">{{ $user->name }}</span>
                                        @if ($user->id === auth()->id())
                                            <span
                                                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                                     bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                                You
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[#6B7280] dark:text-[#9CA3AF]">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->role === 'admin')
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1
                                                 rounded-full bg-gradient-to-r from-[#003087] to-[#1a4db3] text-white">
                                            🛡️ Admin
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1
                                                 rounded-full bg-amber-100 dark:bg-amber-900/30
                                                 text-amber-700 dark:text-amber-400
                                                 border border-amber-200 dark:border-amber-800">
                                            🧑‍💼 Cashier
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[#6B7280] dark:text-[#9CA3AF] text-xs">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                               text-[#6B7280] dark:text-[#9CA3AF] no-underline
                                               bg-black/[0.04] dark:bg-white/[0.04]
                                               border border-black/[0.08] dark:border-white/[0.07]
                                               hover:text-[#003087] dark:hover:text-white
                                               hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.08]
                                               transition-all duration-150">
                                            ✏️ Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                                       text-red-500 dark:text-red-400
                                                       bg-red-50 dark:bg-red-900/20
                                                       border border-red-200 dark:border-red-800
                                                       hover:bg-red-100 dark:hover:bg-red-900/40
                                                       transition-all duration-150 cursor-pointer">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="text-4xl mb-3">👥</div>
                                    <div class="text-[#6B7280] dark:text-[#9CA3AF] text-sm">No users found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-black/[0.06] dark:border-white/[0.06]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
