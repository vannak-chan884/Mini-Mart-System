@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}"
            class="w-9 h-9 rounded-[10px] flex items-center justify-center no-underline
                   bg-black/[0.04] dark:bg-white/[0.04]
                   border border-black/[0.08] dark:border-white/[0.07]
                   text-[#6B7280] dark:text-[#9CA3AF]
                   hover:text-[#0D0D14] dark:hover:text-white
                   transition-all duration-150">
            ←
        </a>
        <div>
            <h2 class="font-playfair text-2xl font-bold text-[#0D0D14] dark:text-white">Add User</h2>
            <p class="text-sm text-[#6B7280] dark:text-[#9CA3AF] mt-0.5">Create a new admin or cashier account</p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="bg-white dark:bg-[#13131F] rounded-2xl border border-black/[0.08] dark:border-white/[0.07]
                shadow-[0_2px_16px_rgba(0,0,0,0.06)] p-6 space-y-5">

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="e.g. Dara Chan"
                    class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border {{ $errors->has('name') ? 'border-red-400' : 'border-black/[0.1] dark:border-white/[0.08]' }}
                           text-[#0D0D14] dark:text-white placeholder-[#9CA3AF]
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="e.g. dara@minimart.com"
                    class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border {{ $errors->has('email') ? 'border-red-400' : 'border-black/[0.1] dark:border-white/[0.08]' }}
                           text-[#0D0D14] dark:text-white placeholder-[#9CA3AF]
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role" required
                    class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border {{ $errors->has('role') ? 'border-red-400' : 'border-black/[0.1] dark:border-white/[0.08]' }}
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role…</option>
                    <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>🛡️ Admin</option>
                    <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>🧑‍💼 Cashier</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="h-px bg-black/[0.06] dark:bg-white/[0.06]"></div>

            {{-- Password --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" required
                    placeholder="Minimum 8 characters"
                    class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border {{ $errors->has('password') ? 'border-red-400' : 'border-black/[0.1] dark:border-white/[0.08]' }}
                           text-[#0D0D14] dark:text-white placeholder-[#9CA3AF]
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" required
                    placeholder="Re-enter password"
                    class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white placeholder-[#9CA3AF]
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}"
                    class="px-5 py-2.5 rounded-[10px] text-sm font-semibold no-underline
                           text-[#6B7280] dark:text-[#9CA3AF]
                           bg-black/[0.04] dark:bg-white/[0.04]
                           border border-black/[0.08] dark:border-white/[0.07]
                           hover:text-[#0D0D14] dark:hover:text-white
                           transition-all duration-150">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-[10px] text-sm font-semibold text-white
                           bg-gradient-to-r from-[#003087] to-[#1a4db3]
                           hover:from-[#002570] hover:to-[#1541a0]
                           shadow-[0_4px_14px_rgba(0,48,135,0.35)]
                           transition-all duration-200 cursor-pointer border-0">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection