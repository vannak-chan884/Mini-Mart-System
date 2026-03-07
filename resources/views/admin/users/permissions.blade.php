@extends('layouts.app')

@section('title', 'User Permissions — ' . $user->name)

@push('styles')
<style>
.perm-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
html.dark .perm-card {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.07);
    box-shadow: none;
}
.perm-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    background: rgba(0,0,0,0.015);
}
html.dark .perm-card-header {
    border-color: rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.025);
}
.perm-card-title {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #1A1A2E;
}
html.dark .perm-card-title { color: #EEEAE2; }

.perm-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    gap: 12px;
}
html.dark .perm-row { border-color: rgba(255,255,255,0.04); }
.perm-row:last-child { border-bottom: none; }
.perm-row:hover { background: rgba(0,0,0,0.012); }
html.dark .perm-row:hover { background: rgba(255,255,255,0.02); }

.perm-info { flex: 1; min-width: 0; }
.perm-label { font-size: 13.5px; font-weight: 500; color: #1A1A2E; }
html.dark .perm-label { color: #E8E4DC; }
.perm-key { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: #9CA3AF; margin-top: 1px; }
html.dark .perm-key { color: #555968; }

/* ── 3-state radio pill group ─────────────────────────────── */
.state-group {
    display: inline-flex;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.1);
    flex-shrink: 0;
}
html.dark .state-group { border-color: rgba(255,255,255,0.1); }

.state-group input[type="radio"] { display: none; }

.state-group label {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
    background: rgba(0,0,0,0.03);
    color: #9CA3AF;
    border-right: 1px solid rgba(0,0,0,0.08);
    user-select: none;
}
html.dark .state-group label {
    background: rgba(255,255,255,0.04);
    color: #6B7280;
    border-color: rgba(255,255,255,0.07);
}
.state-group label:last-of-type { border-right: none; }

/* Inherit = default gray */
.state-group input[value="inherit"]:checked + label {
    background: rgba(0,0,0,0.08);
    color: #374151;
}
html.dark .state-group input[value="inherit"]:checked + label {
    background: rgba(255,255,255,0.1);
    color: #D1D5DB;
}

/* Allow = green */
.state-group input[value="allow"]:checked + label {
    background: rgba(22,163,74,0.12);
    color: #15803D;
}
html.dark .state-group input[value="allow"]:checked + label {
    background: rgba(34,197,94,0.15);
    color: #86EFAC;
}

/* Deny = red */
.state-group input[value="deny"]:checked + label {
    background: rgba(220,38,38,0.1);
    color: #DC2626;
}
html.dark .state-group input[value="deny"]:checked + label {
    background: rgba(239,68,68,0.15);
    color: #FCA5A5;
}

/* Role badge pill */
.role-has {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 999px;
    white-space: nowrap;
    flex-shrink: 0;
}
.role-has.yes {
    background: rgba(59,130,246,0.1);
    color: #2563EB;
    border: 1px solid rgba(59,130,246,0.2);
}
html.dark .role-has.yes { color: #93C5FD; background: rgba(59,130,246,0.12); }
.role-has.no {
    background: rgba(0,0,0,0.04);
    color: #9CA3AF;
    border: 1px solid rgba(0,0,0,0.08);
}
html.dark .role-has.no { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.07); }

/* Sticky save bar */
.save-bar {
    position: sticky;
    bottom: 0;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(12px);
    border-top: 1px solid rgba(0,0,0,0.07);
    padding: 14px 0;
    margin-top: 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    z-index: 10;
}
html.dark .save-bar {
    background: rgba(7,8,15,0.92);
    border-color: rgba(255,255,255,0.07);
}
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────── --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.users.index') }}"
        class="w-9 h-9 rounded-[10px] flex items-center justify-center no-underline
               bg-black/[0.04] dark:bg-white/[0.04]
               border border-black/[0.08] dark:border-white/[0.07]
               text-[#6B7280] dark:text-[#9CA3AF]
               hover:text-[#0D0D14] dark:hover:text-white transition-all duration-150">
        ←
    </a>
    <div class="flex-1">
        <h1 class="font-playfair text-[22px] font-bold text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
            🔑 User Permissions
        </h1>
        <p class="text-[12.5px] text-[#9CA3AF] dark:text-[#555968] mt-0.5">
            Custom overrides for
            <span class="font-bold text-[#1A1A2E] dark:text-[#EEEAE2]">{{ $user->name }}</span>
            —
            @if ($user->role === 'admin')
                <span class="text-blue-500">Admin (overrides have no effect — admin always has full access)</span>
            @else
                overrides take priority over the <span class="font-semibold capitalize">{{ $user->role }}</span> role defaults
            @endif
        </p>
    </div>

    {{-- User badge --}}
    <div class="hidden md:flex items-center gap-2.5 px-4 py-2 rounded-[10px]
                bg-black/[0.03] dark:bg-white/[0.04]
                border border-black/[0.08] dark:border-white/[0.07]">
        <div class="w-7 h-7 rounded-[8px] flex items-center justify-center text-xs font-bold
                    text-white bg-gradient-to-br from-[#003087] to-[#1a4db3]">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div class="text-[13px] font-semibold text-[#0D0D14] dark:text-white">{{ $user->name }}</div>
            <div class="text-[10px] text-[#9CA3AF] capitalize">{{ $user->role }}</div>
        </div>
    </div>
</div>

{{-- ── Legend ────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-3 mb-5 px-4 py-3 rounded-[10px]
            bg-black/[0.02] dark:bg-white/[0.03]
            border border-black/[0.06] dark:border-white/[0.06]">
    <span class="text-[11.5px] font-bold text-[#9CA3AF] uppercase tracking-wide mr-1">Legend:</span>

    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-[#374151] dark:text-[#D1D5DB]">
        <span class="w-3 h-3 rounded-sm bg-black/[0.08] dark:bg-white/[0.1] inline-block"></span>
        Inherit — use role default
    </span>
    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 dark:text-green-400">
        <span class="w-3 h-3 rounded-sm bg-green-100 dark:bg-green-900/30 inline-block border border-green-300 dark:border-green-700"></span>
        Allow — force grant for this user
    </span>
    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 dark:text-red-400">
        <span class="w-3 h-3 rounded-sm bg-red-100 dark:bg-red-900/30 inline-block border border-red-300 dark:border-red-700"></span>
        Deny — force deny for this user
    </span>
    <span class="ml-auto inline-flex items-center gap-1 text-[11px] text-[#9CA3AF]">
        <span class="role-has yes">Role ✓</span> = role grants this by default
    </span>
</div>

{{-- ── Flash ─────────────────────────────────────────────────── --}}
@if (session('success'))
    <div class="flex items-center gap-2.5 rounded-[10px] px-4 py-3 mb-5
                bg-[rgba(22,163,74,0.08)] border border-[rgba(22,163,74,0.25)]
                text-[13px] font-medium text-green-700 dark:text-[#86EFAC]">
        ✅ {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.users.permissions.update', $user) }}">
@csrf

<div class="space-y-4">
    @foreach ($allPermissions as $group => $perms)
    <div class="perm-card">
        <div class="perm-card-header">
            <span class="perm-card-title">{{ $group }}</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="setGroupAll('{{ $group }}', 'inherit')"
                    class="text-[11px] font-semibold text-[#9CA3AF] hover:text-[#374151] dark:hover:text-white
                           bg-none border-0 cursor-pointer p-0 underline underline-offset-2">
                    Reset all
                </button>
            </div>
        </div>

        @foreach ($perms as $perm)
            @php
                $override = $userOverrides->get($perm->key);
                $currentState = $override ? ($override->granted ? 'allow' : 'deny') : 'inherit';
                $roleHas = $rolePermissions->has($perm->key);
                $uid = 'perm_' . str_replace('.', '_', $perm->key);
            @endphp
        <div class="perm-row">
            <div class="perm-info">
                <div class="perm-label">{{ $perm->label }}</div>
                <div class="perm-key">{{ $perm->key }}</div>
            </div>

            {{-- Role default badge --}}
            <span class="role-has {{ $roleHas ? 'yes' : 'no' }}">
                {{ $roleHas ? 'Role ✓' : 'Role ✗' }}
            </span>

            {{-- 3-state selector --}}
            <div class="state-group" data-group="{{ $group }}">
                <input type="radio" id="{{ $uid }}_inherit"
                       name="overrides[{{ $perm->key }}]"
                       value="inherit"
                       {{ $currentState === 'inherit' ? 'checked' : '' }}>
                <label for="{{ $uid }}_inherit">Inherit</label>

                <input type="radio" id="{{ $uid }}_allow"
                       name="overrides[{{ $perm->key }}]"
                       value="allow"
                       {{ $currentState === 'allow' ? 'checked' : '' }}>
                <label for="{{ $uid }}_allow">Allow</label>

                <input type="radio" id="{{ $uid }}_deny"
                       name="overrides[{{ $perm->key }}]"
                       value="deny"
                       {{ $currentState === 'deny' ? 'checked' : '' }}>
                <label for="{{ $uid }}_deny">Deny</label>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

{{-- ── Sticky save bar ──────────────────────────────────────── --}}
<div class="save-bar">
    <button type="submit"
        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-[10px]
               text-[13px] font-bold text-white border-0 cursor-pointer
               bg-gradient-to-br from-[#003087] to-[#3B82F6]
               shadow-[0_3px_12px_rgba(0,48,135,0.3)]
               hover:-translate-y-px hover:shadow-[0_6px_18px_rgba(0,48,135,0.4)]
               active:translate-y-0 transition-all duration-150">
        💾 Save Overrides
    </button>
    <button type="button" onclick="resetAll()"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px]
               text-[13px] font-semibold cursor-pointer
               text-[#6B7280] dark:text-[#9CA3AF]
               bg-black/[0.04] dark:bg-white/[0.04]
               border border-black/[0.08] dark:border-white/[0.07]
               hover:text-[#0D0D14] dark:hover:text-white transition-all duration-150">
        ↺ Clear All Overrides
    </button>
    <span class="text-[12.5px] text-[#9CA3AF] dark:text-[#555968]">
        Only overrides are saved — "Inherit" rows use the role default.
    </span>
</div>

</form>
@endsection

@push('scripts')
<script>
    // Set all radios in a group to a given state
    function setGroupAll(group, state) {
        document.querySelectorAll(`.state-group[data-group="${group}"] input[value="${state}"]`)
            .forEach(r => r.checked = true);
    }

    // Reset every radio on the page to "inherit"
    function resetAll() {
        if (!confirm('Reset all overrides for {{ addslashes($user->name) }} to role defaults?')) return;
        document.querySelectorAll('.state-group input[value="inherit"]')
            .forEach(r => r.checked = true);
    }
</script>
@endpush