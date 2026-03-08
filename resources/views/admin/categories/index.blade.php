@extends('layouts.app')
@section('title', 'Categories')

@push('styles')
    <style>
        :root {
            --blue: #003087;
            --blue-mid: #1a4db3;
            --red: #CC0001;
            --muted: #6B7280;
            --muted-lt: #9CA3AF;
            --border: rgba(255, 255, 255, 0.07);
            --card: rgba(255, 255, 255, 0.03);
            --text: #E8E4DC;
        }

        .page-header {
            margin-bottom: 24px;
            gap: 12px;
        }

        .page-heading {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
        }

        .page-heading span {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 400;
            color: var(--muted);
            margin-left: 10px;
        }

        .btn-add {
            gap: 7px;
            padding: 10px 18px;
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 3px 12px rgba(0, 48, 135, 0.35);
            border: none;
            cursor: pointer;
        }

        .btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 48, 135, 0.45);
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #86EFAC;
            font-weight: 500;
        }

        .table-card-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--muted-lt);
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .table-total {
            font-size: 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 999px;
            font-family: 'IBM Plex Mono', monospace;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 11px 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        thead th.right {
            text-align: right;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            transition: background 0.15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: var(--text);
            vertical-align: middle;
        }

        tbody td.right {
            text-align: right;
        }

        .row-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            width: 48px;
        }

        .cat-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(0, 48, 135, 0.2);
            border: 1px solid rgba(0, 48, 135, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .cat-name {
            font-weight: 600;
            font-size: 14px;
        }

        .action-wrap {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 13px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .action-edit {
            background: rgba(0, 48, 135, 0.15);
            border-color: rgba(0, 48, 135, 0.3);
            color: #93C5FD;
        }

        .action-edit:hover {
            background: rgba(0, 48, 135, 0.3);
            border-color: rgba(0, 48, 135, 0.5);
        }

        .action-delete {
            background: rgba(204, 0, 1, 0.1);
            border-color: rgba(204, 0, 1, 0.25);
            color: #FCA5A5;
        }

        .action-delete:hover {
            background: rgba(204, 0, 1, 0.22);
            border-color: rgba(204, 0, 1, 0.45);
        }

        .empty-state {
            padding: 56px 24px;
            text-align: center;
        }

        .empty-icon {
            font-size: 44px;
            margin-bottom: 14px;
            opacity: 0.35;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 6px;
        }

        .empty-sub {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        /* ── Delete Modal ── */
        #deleteModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(4px);
            padding: 16px;
        }

        #deleteModal.open {
            display: flex;
        }

        .modal-card {
            background: #1C1C2E;
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 18px;
            padding: 28px 28px 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: modalIn 0.2s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(204, 0, 1, 0.12);
            border: 1px solid rgba(204, 0, 1, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .modal-body {
            font-size: 13.5px;
            color: #9CA3AF;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .modal-body strong {
            color: #E8E4DC;
            font-weight: 600;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 9px 20px;
            border-radius: 9px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #9CA3AF;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.09);
            color: #E8E4DC;
        }

        .btn-confirm-delete {
            padding: 9px 20px;
            border-radius: 9px;
            border: 1px solid rgba(204, 0, 1, 0.4);
            background: rgba(204, 0, 1, 0.15);
            color: #FCA5A5;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-confirm-delete:hover {
            background: rgba(204, 0, 1, 0.28);
            border-color: rgba(204, 0, 1, 0.6);
            color: #fff;
        }
    </style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header flex text-center justify-between flex-wrap">
        <div class="page-heading text-white">
            🗂️ Categories
            <span>{{ $categories->count() }} total</span>
        </div>
        @canDo('categories.create')
        <a href="{{ route('admin.categories.create') }}" class="btn-add inline-flex items-center text-white dark:text-white">
            + Add Category
        </a>
        @endCanDo
    </div>

    {{-- Success alert --}}
    @if (session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div
        class="bg-black/[0.03] dark:bg-white/[0.03] border border-black/[0.08] dark:border-white/[0.07] rounded-2xl overflow-hidden">
        <div
            class="flex items-center justify-between bg-black/[0.02] dark:bg-white/[0.02] px-[16px] py-[20px] border-b border-black/[0.08] dark:border-white/[0.07]">
            <div class="table-card-title">All Categories</div>
            <span class="table-total">{{ $categories->count() }} records</span>
        </div>

        @if ($categories->count())
            <table>
                <thead>
                    <tr>
                        <th style="width:56px">#</th>
                        <th>Category Name</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="row-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="cat-name-wrap flex items-center gap-2">
                                    <div class="cat-icon">🗂️</div>
                                    <div class="cat-name text-gray-700 dark:text-white">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td class="right">
                                <div class="action-wrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="action-btn action-edit">
                                        ✏️ Edit
                                    </a>
                                    @canDo('categories.delete')
                                    {{-- type="button" — never submits, never triggers loader --}}
                                    <button type="button" class="action-btn action-delete"
                                        onclick="openDeleteModal('{{ route('admin.categories.destroy', $category) }}', '{{ addslashes($category->name) }}')">
                                        🗑 Delete
                                    </button>
                                    @endCanDo
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">🗂️</div>
                <div class="empty-title">No categories yet</div>
                <div class="empty-sub">Create your first category to start organizing products.</div>
                <a href="{{ route('admin.categories.create') }}" class="btn-add inline-flex items-center text-white">
                    + Add First Category
                </a>
            </div>
        @endif
    </div>

    {{-- ── Delete Confirmation Modal ────────────────────────────── --}}
    <div id="deleteModal">
        <div class="modal-card">
            <div class="modal-icon">🗑️</div>
            <div class="modal-title">Delete Category?</div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="modalCatName"></strong>?
                This action cannot be undone.
            </div>
            <div class="modal-actions">
                {{-- Cancel: data-no-loader + type="button" = never triggers loader --}}
                <button type="button" class="btn-cancel" data-no-loader onclick="closeDeleteModal()">
                    Cancel
                </button>
                {{-- Confirm: submits hidden form → loader shows "Deleting…" correctly --}}
                <button type="button" class="btn-confirm-delete" id="modalConfirmBtn">
                    🗑 Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Single shared delete form — action URL set dynamically by JS --}}
    <form id="sharedDeleteForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        let _deleteUrl = null;

        function openDeleteModal(url, name) {
            _deleteUrl = url;
            document.getElementById('modalCatName').textContent = name;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
            _deleteUrl = null;
        }

        // Confirm → set form action → submit → loader shows "Deleting…" ✅
        document.getElementById('modalConfirmBtn').addEventListener('click', function() {
            if (_deleteUrl) {
                const form = document.getElementById('sharedDeleteForm');
                form.action = _deleteUrl;
                closeDeleteModal();
                // requestSubmit() fires the submit event (triggers loader)
                // form.submit() silently submits without firing the event
                if (form.requestSubmit) {
                    form.requestSubmit();
                } else {
                    // Fallback for older browsers
                    form.dispatchEvent(new Event('submit', {
                        bubbles: true,
                        cancelable: true
                    }));
                    form.submit();
                }
            }
        });

        // Close on backdrop click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
@endpush
