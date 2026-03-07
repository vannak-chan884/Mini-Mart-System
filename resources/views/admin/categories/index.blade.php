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

        /* Page header */
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

        /* Add button */
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

        /* Alert */
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

        /* Table card */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
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

        /* Table */
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

        /* Row number */
        .row-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            width: 48px;
        }

        /* Category name with icon */
        .cat-name-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
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
            color: #fff;
            font-size: 14px;
        }

        /* Action buttons */
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

        /* Empty state */
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
    </style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header flex text-center justify-between flex-wrap">
        <div class="page-heading text-white">
            🗂️ Categories
            <span>{{ $categories->count() }} total</span>
        </div>
        @canDo('expenses.create')
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
    <div class="table-card">
        <div class="table-card-header">
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
                                <div class="cat-name-wrap">
                                    <div class="cat-icon">🗂️</div>
                                    <div class="cat-name">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td class="right">
                                <div class="action-wrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="action-btn action-edit">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Delete « {{ $category->name }} »? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn action-delete">🗑 Delete</button>
                                    </form>
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
                <a href="{{ route('admin.categories.create') }}" class="btn-add inline-flex items-center text-white">+ Add
                    First Category</a>
            </div>
        @endif
    </div>

@endsection
