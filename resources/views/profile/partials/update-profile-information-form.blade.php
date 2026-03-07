<section class="relative">

    {{-- ── Decorative accent line ─────────────────────── --}}
    <div class="absolute top-0 left-0 w-10 h-[3px] rounded-full"
         style="background:linear-gradient(90deg,#1D4ED8,#3B82F6);"></div>

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="pt-5 mb-7">
        <h2 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:800;letter-spacing:-.3px;"
            class="text-[#1A1A2E] dark:text-[#EEEAE2]">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-[13px] text-[#6B7280] dark:text-[#8B909E]"
           style="font-family:'DM Sans',sans-serif;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>

    {{-- ── Hidden verification form ────────────────────── --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- ── Main form ────────────────────────────────────── --}}
    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        {{-- Name ──────────────────────────────────────── --}}
        <div class="group">
            <label for="name"
                class="block text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                       text-[#6B7280] dark:text-[#555968] transition-colors duration-200
                       group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]"
                style="font-family:'DM Sans',sans-serif;">
                {{ __('Full Name') }}
            </label>
            <div class="relative">
                {{-- Icon --}}
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                            text-[#9CA3AF] dark:text-[#555968]
                            group-focus-within:text-[#3B82F6] dark:group-focus-within:text-[#3B82F6]
                            transition-colors duration-200">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <input
                    id="name" name="name" type="text"
                    value="{{ old('name', $user->name) }}"
                    required autofocus autocomplete="name"
                    class="w-full pl-10 pr-4 py-2.5 rounded-[10px] text-[13.5px]
                           bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                           border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                           text-[#1A1A2E] dark:text-[#EEEAE2]
                           placeholder:text-[#9CA3AF] dark:placeholder:text-[#555968]
                           outline-none transition-all duration-200
                           focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                           focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                           dark:focus:border-[rgba(59,130,246,0.45)] dark:focus:bg-[rgba(59,130,246,0.06)]"
                    style="font-family:'DM Sans',sans-serif;">
            </div>
            @error('name')
                <p class="mt-1.5 text-[11.5px] text-red-500 dark:text-red-400 flex items-center gap-1"
                   style="font-family:'DM Sans',sans-serif;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Email ─────────────────────────────────────── --}}
        <div class="group">
            <label for="email"
                class="block text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                       text-[#6B7280] dark:text-[#555968] transition-colors duration-200
                       group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]"
                style="font-family:'DM Sans',sans-serif;">
                {{ __('Email Address') }}
            </label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                            text-[#9CA3AF] dark:text-[#555968]
                            group-focus-within:text-[#3B82F6] transition-colors duration-200">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="3"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </div>
                <input
                    id="email" name="email" type="email"
                    value="{{ old('email', $user->email) }}"
                    required autocomplete="username"
                    class="w-full pl-10 pr-4 py-2.5 rounded-[10px] text-[13.5px]
                           bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                           border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                           text-[#1A1A2E] dark:text-[#EEEAE2]
                           placeholder:text-[#9CA3AF] dark:placeholder:text-[#555968]
                           outline-none transition-all duration-200
                           focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                           focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                           dark:focus:border-[rgba(59,130,246,0.45)] dark:focus:bg-[rgba(59,130,246,0.06)]"
                    style="font-family:'DM Sans',sans-serif;">
            </div>
            @error('email')
                <p class="mt-1.5 text-[11.5px] text-red-500 dark:text-red-400 flex items-center gap-1"
                   style="font-family:'DM Sans',sans-serif;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror

            {{-- Email verification notice ──────────────── --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2.5 rounded-[10px] px-3.5 py-3
                            bg-[rgba(245,158,11,0.07)] border border-[rgba(245,158,11,0.22)]
                            dark:bg-[rgba(245,158,11,0.08)] dark:border-[rgba(245,158,11,0.2)]">
                    <div class="flex items-start gap-2.5">
                        <svg class="flex-shrink-0 mt-0.5 text-amber-500" width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-[12px] font-medium text-amber-700 dark:text-amber-400"
                               style="font-family:'DM Sans',sans-serif;">
                                {{ __('Your email address is unverified.') }}
                            </p>
                            <button form="send-verification"
                                class="mt-0.5 text-[12px] text-[#1D4ED8] dark:text-[#3B82F6] underline
                                       underline-offset-2 font-semibold cursor-pointer bg-transparent border-0
                                       hover:text-[#1e40af] dark:hover:text-[#93C5FD] transition-colors duration-150 p-0"
                                style="font-family:'DM Sans',sans-serif;">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>
                    </div>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-[11.5px] font-semibold text-green-600 dark:text-green-400 flex items-center gap-1"
                           style="font-family:'DM Sans',sans-serif;">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('Verification link sent to your email.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Role ──────────────────────────────────────── --}}
        <div class="group">
            <label for="role"
                class="block text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                       text-[#6B7280] dark:text-[#555968]"
                style="font-family:'DM Sans',sans-serif;">
                {{ __('Role') }}
            </label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                            text-[#9CA3AF] dark:text-[#555968]">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <input
                    id="role" name="role" type="text"
                    value="{{ old('role', $user->role) }}"
                    required autocomplete="role" readonly
                    class="w-full pl-10 pr-4 py-2.5 rounded-[10px] text-[13.5px] cursor-default
                           bg-[rgba(0,0,0,0.03)] dark:bg-[rgba(255,255,255,0.02)]
                           border border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.05)]
                           text-[#6B7280] dark:text-[#555968]
                           outline-none select-none"
                    style="font-family:'DM Sans',sans-serif;">
                {{-- Read-only badge --}}
                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                    <span class="text-[9px] font-bold uppercase tracking-[0.6px] px-1.5 py-0.5 rounded-md
                                 bg-[rgba(0,0,0,0.05)] dark:bg-[rgba(255,255,255,0.06)]
                                 text-[#9CA3AF] dark:text-[#555968]"
                          style="font-family:'DM Sans',sans-serif;">
                        Read-only
                    </span>
                </div>
            </div>
            @error('role')
                <p class="mt-1.5 text-[11.5px] text-red-500 dark:text-red-400 flex items-center gap-1"
                   style="font-family:'DM Sans',sans-serif;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- ── Divider ──────────────────────────────────── --}}
        <div class="h-px bg-[rgba(0,0,0,0.06)] dark:bg-[rgba(255,255,255,0.05)] my-1"></div>

        {{-- ── Save row ─────────────────────────────────── --}}
        <div class="flex items-center gap-3 pt-1">
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px]
                       text-[13px] font-bold text-white cursor-pointer border-0
                       transition-all duration-200
                       hover:-translate-y-[1px] hover:shadow-[0_6px_20px_rgba(37,99,235,0.38)]
                       active:translate-y-0"
                style="font-family:'DM Sans',sans-serif;
                       background:linear-gradient(135deg,#1D4ED8,#3B82F6);
                       box-shadow:0 3px 12px rgba(59,130,246,0.28);">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition.opacity
                    x-init="setTimeout(() => show = false, 2500)"
                    class="flex items-center gap-1.5 text-[12.5px] font-semibold text-green-600 dark:text-green-400"
                    style="font-family:'DM Sans',sans-serif;">
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-100 dark:bg-[rgba(34,197,94,0.12)]">
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    {{ __('Changes saved!') }}
                </p>
            @endif
        </div>
    </form>
</section>