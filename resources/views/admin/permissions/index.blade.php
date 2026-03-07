@extends('layouts.app')

@section('title', 'Role Permissions')

@push('styles')
    <style>
        /* ── Permission group card ───────────────────────────────── */
        .perm-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        html.dark .perm-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.07);
            box-shadow: none;
        }

        .perm-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            background: rgba(0, 0, 0, 0.015);
        }

        html.dark .perm-card-header {
            border-color: rgba(255, 255, 255, 0.06);
            background: rgba(255, 255, 255, 0.025);
        }

        .perm-card-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #1A1A2E;
        }

        html.dark .perm-card-title {
            color: #EEEAE2;
        }

        .group-select-all {
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: #3B82F6;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        html.dark .group-select-all {
            color: #60A5FA;
        }

        /* Permission rows */
        .perm-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: background .12s;
        }

        html.dark .perm-row {
            border-color: rgba(255, 255, 255, 0.04);
        }

        .perm-row:last-child {
            border-bottom: none;
        }

        .perm-row:hover {
            background: rgba(0, 0, 0, 0.015);
        }

        html.dark .perm-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .perm-info {
            flex: 1;
            min-width: 0;
        }

        .perm-label {
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            color: #1A1A2E;
        }

        html.dark .perm-label {
            color: #E8E4DC;
        }

        .perm-key {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 1px;
        }

        html.dark .perm-key {
            color: #555968;
        }

        /* Always-on badge for admin column */
        .badge-always-on {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #15803D;
            background: rgba(34, 197, 94, 0.09);
            border: 1px solid rgba(34, 197, 94, 0.22);
            padding: 2px 9px;
            border-radius: 999px;
            white-space: nowrap;
        }

        html.dark .badge-always-on {
            color: #86EFAC;
            background: rgba(34, 197, 94, 0.06);
            border-color: rgba(34, 197, 94, 0.15);
        }

        /* ── Custom toggle switch ────────────────────────────────── */
        .toggle-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .toggle-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-track {
            display: flex;
            align-items: center;
            width: 42px;
            height: 24px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all .2s;
            position: relative;
        }

        html.dark .toggle-track {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .toggle-thumb {
            position: absolute;
            left: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
            transition: transform .2s cubic-bezier(.34, 1.4, .64, 1);
            pointer-events: none;
        }

        .toggle-input:checked+.toggle-track {
            background: linear-gradient(135deg, #003087, #3B82F6);
            border-color: transparent;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
        }

        .toggle-input:checked+.toggle-track .toggle-thumb {
            transform: translateX(18px);
        }

        /* ── Sticky save bar ─────────────────────────────────────── */
        .save-bar {
            position: sticky;
            bottom: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(0, 0, 0, 0.07);
            padding: 14px 0;
            margin-top: 28px;
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 10;
        }

        html.dark .save-bar {
            background: rgba(7, 8, 15, 0.92);
            border-color: rgba(255, 255, 255, 0.07);
        }
    </style>
@endpush

@section('content')

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1
                class="font-['Playfair_Display'] text-[22px] font-bold
                   text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
                🔐 Role Permissions
            </h1>
            <p class="font-['DM_Sans'] text-[12.5px] text-[#9CA3AF] dark:text-[#555968] mt-0.5">
                Control what the
                <span class="font-bold text-[#1A1A2E] dark:text-[#EEEAE2]">Cashier</span>
                role can access and perform
            </p>
        </div>

        {{-- Role pills --}}
        <div class="flex items-center gap-2">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px]
                     font-['DM_Sans'] text-[12px] font-bold
                     bg-gradient-to-br from-[#003087] to-[#3B82F6] text-white
                     shadow-[0_2px_8px_rgba(0,48,135,0.3)]">
                👑 Admin — Full Access
            </span>
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px]
                     font-['DM_Sans'] text-[12px] font-bold
                     bg-[rgba(245,158,11,0.1)] border border-[rgba(245,158,11,0.25)]
                     text-[#92400E] dark:text-[#FCD34D]">
                🧾 Cashier — Configurable
            </span>
        </div>
    </div>

    {{-- ── Success flash ────────────────────────────────────────── --}}
    @if (session('success'))
        <div
            class="flex items-center gap-2.5 rounded-[10px] px-4 py-3 mb-5
                bg-[rgba(22,163,74,0.08)] border border-[rgba(22,163,74,0.25)]
                font-['DM_Sans'] text-[13px] font-medium text-green-700 dark:text-[#86EFAC]">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.permissions.update') }}">
        @csrf

        {{-- ── Two-column matrix ────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ══ LEFT: Admin (read-only) ════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div
                        class="w-7 h-7 rounded-[8px] flex items-center justify-center text-sm
                        bg-gradient-to-br from-[#003087] to-[#3B82F6]
                        shadow-[0_2px_8px_rgba(0,48,135,0.3)]">
                        👑
                    </div>
                    <div>
                        <div
                            class="font-['DM_Sans'] text-[14px] font-bold
                            text-[#1A1A2E] dark:text-[#EEEAE2]">
                            Admin Role
                        </div>
                        <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968]">
                            Always has full access — cannot be restricted
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($permissions as $group => $perms)
                        <div class="perm-card">
                            <div class="perm-card-header">
                                <span class="perm-card-title">{{ $group }}</span>
                            </div>
                            @foreach ($perms as $perm)
                                <div class="perm-row">
                                    <div class="perm-info">
                                        <div class="perm-label">{{ $perm->label }}</div>
                                        <div class="perm-key">{{ $perm->key }}</div>
                                    </div>
                                    <span class="badge-always-on">
                                        <svg width="10" height="10" fill="none" stroke="currentColor"
                                            stroke-width="2.8" viewBox="0 0 24 24">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                        Always on
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ RIGHT: Cashier (toggleable) ════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div
                        class="w-7 h-7 rounded-[8px] flex items-center justify-center text-sm
                        bg-[rgba(245,158,11,0.12)] border border-[rgba(245,158,11,0.25)]">
                        🧾
                    </div>
                    <div>
                        <div
                            class="font-['DM_Sans'] text-[14px] font-bold
                            text-[#1A1A2E] dark:text-[#EEEAE2]">
                            Cashier Role
                        </div>
                        <div class="font-['DM_Sans'] text-[11.5px] text-[#9CA3AF] dark:text-[#555968]">
                            Toggle each permission on or off
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($permissions as $group => $perms)
                        <div class="perm-card">
                            <div class="perm-card-header">
                                <span class="perm-card-title">{{ $group }}</span>
                                <button type="button" class="group-select-all" data-group="{{ $group }}">
                                    Select all
                                </button>
                            </div>
                            @foreach ($perms as $perm)
                                <div class="perm-row">
                                    <div class="perm-info">
                                        <div class="perm-label">{{ $perm->label }}</div>
                                        <div class="perm-key">{{ $perm->key }}</div>
                                    </div>
                                    <label class="toggle-wrap cursor-pointer">
                                        <input type="checkbox" class="toggle-input perm-toggle" name="permissions[]"
                                            value="{{ $perm->key }}" data-group="{{ $group }}"
                                            {{ isset($granted[$perm->key]) ? 'checked' : '' }}>
                                        <span class="toggle-track">
                                            <span class="toggle-thumb"></span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Sticky save bar ──────────────────────────────────────── --}}
        <div class="save-bar">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-[10px]
               font-['DM_Sans'] text-[13px] font-bold text-white border-0 cursor-pointer
               bg-gradient-to-br from-[#003087] to-[#3B82F6]
               shadow-[0_3px_12px_rgba(0,48,135,0.3)]
               hover:-translate-y-px hover:shadow-[0_6px_18px_rgba(0,48,135,0.4)]
               active:translate-y-0 transition-all duration-150">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                Save Permissions
            </button>
            <span class="font-['DM_Sans'] text-[12.5px] text-[#9CA3AF] dark:text-[#555968]">
                Changes apply immediately for all cashier accounts.
            </span>
        </div>

    </form>

@endsection

@push('scripts')
    <script>
        // Toggle all checkboxes in a group on/off
        document.querySelectorAll('.group-select-all').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const group = btn.dataset.group;
                const checkboxes = document.querySelectorAll(`.perm-toggle[data-group="${group}"]`);
                const allChecked = [...checkboxes].every(c => c.checked);

                checkboxes.forEach(c => {
                    c.checked = !allChecked;
                });
                btn.textContent = allChecked ? 'Select all' : 'Deselect all';
            });
        });
    </script>
@endpush
