@extends('layouts.app')

@section('title', 'Edit User')

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
                <h2 class="font-playfair text-2xl font-bold text-[#0D0D14] dark:text-white">Edit User</h2>
                <p class="text-sm text-[#6B7280] dark:text-[#9CA3AF] mt-0.5">Updating account for {{ $user->name }}</p>
            </div>
        </div>

        {{-- Form card --}}
        <div
            class="bg-white dark:bg-[#13131F] rounded-2xl border border-black/[0.08] dark:border-white/[0.07]
                shadow-[0_2px_16px_rgba(0,0,0,0.06)] p-6 space-y-5">

            {{-- ✅ EDIT FORM — delete form is fully outside this one --}}
            <form id="editForm" method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
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
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
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
                    <select name="role" required {{ $user->id === auth()->id() ? 'disabled' : '' }}
                        class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border {{ $errors->has('role') ? 'border-red-400' : 'border-black/[0.1] dark:border-white/[0.08]' }}
                           text-[#0D0D14] dark:text-white
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150
                           disabled:opacity-60 disabled:cursor-not-allowed">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>🛡️ Admin
                        </option>
                        <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>🧑‍💼
                            Cashier</option>
                    </select>
                    @if ($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-xs text-amber-500 mt-1">⚠️ You cannot change your own role.</p>
                    @endif
                    @error('role')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Divider --}}
                <div class="h-px bg-black/[0.06] dark:bg-white/[0.06]"></div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-[#374151] dark:text-[#D1D5DB]">
                        New Password
                        <span class="text-[#9CA3AF] font-normal text-xs ml-1">(leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" placeholder="Minimum 8 characters"
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
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Re-enter new password"
                        class="w-full px-4 py-2.5 rounded-[10px] text-sm
                           bg-[#F9FAFB] dark:bg-[#0D0D14]
                           border border-black/[0.1] dark:border-white/[0.08]
                           text-[#0D0D14] dark:text-white placeholder-[#9CA3AF]
                           focus:outline-none focus:border-[#003087] dark:focus:border-[#4a90d9]
                           focus:ring-2 focus:ring-[rgba(0,48,135,0.15)]
                           transition-all duration-150">
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">

                    {{-- Delete button — triggers the separate delete form below via JS --}}
                    @if ($user->id !== auth()->id())
                        <button type="button" onclick="confirmDelete()"
                            class="px-4 py-2.5 rounded-[10px] text-sm font-semibold
                               text-red-500 dark:text-red-400
                               bg-red-50 dark:bg-red-900/20
                               border border-red-200 dark:border-red-800
                               hover:bg-red-100 dark:hover:bg-red-900/40
                               transition-all duration-150 cursor-pointer">
                            🗑️ Delete User
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-3">
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
                            Save Changes
                        </button>
                    </div>
                </div>

            </form>{{-- ✅ Edit form ends here --}}
        </div>
    </div>

    {{-- ✅ DELETE FORM — completely outside the edit form --}}
    @if ($user->id !== auth()->id())
        <form id="deleteForm" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif

    {{-- Confirm dialog --}}
    <div id="deleteModal"
        style="display:none; position:fixed; inset:0; z-index:200;
           background:rgba(0,0,0,0.55); backdrop-filter:blur(4px);
           align-items:center; justify-content:center; padding:1rem;">

        <div
            style="background:#1C1C2E; border-radius:1rem; padding:1.5rem;
                width:100%; max-width:360px; border:1px solid rgba(255,255,255,0.07);
                box-shadow:0 24px 64px rgba(0,0,0,0.5); position:relative;">

            <div style="text-align:center; margin-bottom:1.25rem;">
                <div style="font-size:2rem; margin-bottom:0.5rem;">🗑️</div>
                <h3 class="font-playfair"
                    style="font-size:1.1rem; font-weight:700;
                color:#fff; margin:0 0 0.5rem;">Delete User?
                </h3>
                <p style="font-size:0.875rem; color:#9CA3AF; margin:0;">
                    You are about to delete
                    <strong style="color:#fff;">{{ $user->name }}</strong>.
                    This action cannot be undone.
                </p>
            </div>

            <div style="display:flex; gap:0.75rem;">
                <button type="button" onclick="closeModal()"
                    style="flex:1; padding:0.6rem 1rem; border-radius:0.625rem;
                       font-size:0.875rem; font-weight:600; cursor:pointer;
                       color:#9CA3AF; background:rgba(255,255,255,0.05);
                       border:1px solid rgba(255,255,255,0.08);">
                    Cancel
                </button>
                <button type="button" onclick="submitDelete()"
                    style="flex:1; padding:0.6rem 1rem; border-radius:0.625rem;
                       font-size:0.875rem; font-weight:600; cursor:pointer;
                       color:#fff; background:linear-gradient(to right,#ef4444,#dc2626);
                       border:0; box-shadow:0 4px 14px rgba(220,38,38,0.4);">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                document.getElementById('deleteModal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }

            function submitDelete() {
                document.getElementById('deleteForm').submit();
            }

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        </script>
    @endpush

@endsection
