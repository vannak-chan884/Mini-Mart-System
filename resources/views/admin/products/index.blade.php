@extends('layouts.app')
@section('title', 'Products')

@push('styles')
    <style>
        :root {
            --blue: #003087;
            --blue-mid: #1a4db3;
            --red: #CC0001;
            --border: rgba(255, 255, 255, 0.07);
            --card: rgba(255, 255, 255, 0.03);
            --muted: #6B7280;
            --muted-lt: #9CA3AF;
            --text: #E8E4DC;
        }

        /* Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-heading {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }

        .page-heading span {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 400;
            color: var(--muted);
            margin-left: 10px;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            color: #fff;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 3px 12px rgba(0, 48, 135, 0.35);
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
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.02);
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
            padding: 12px 16px;
            font-size: 13px;
            color: var(--text);
            vertical-align: middle;
        }

        tbody td.right {
            text-align: right;
        }

        /* Row number */
        .row-num {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--muted);
        }

        /* Barcode */
        .barcode-val {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11.5px;
            color: #93C5FD;
            background: rgba(0, 48, 135, 0.15);
            border: 1px solid rgba(0, 48, 135, 0.25);
            padding: 2px 8px;
            border-radius: 5px;
            white-space: nowrap;
        }

        /* Product name + image */
        .product-cell {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .product-thumb {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .product-name {
            font-weight: 600;
            color: #fff;
            font-size: 13.5px;
        }

        /* Category badge */
        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 12px;
            color: var(--muted-lt);
            white-space: nowrap;
        }

        /* Price */
        .price-val {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #4ADE80;
        }

        /* Stock */
        .stock-ok {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .stock-low {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #FCA5A5;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .stock-low::before {
            content: '⚠';
            font-size: 11px;
        }

        /* Actions */
        .action-wrap {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
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
    <div class="page-header">
        <div class="page-heading">
            📦 Products
            <span>{{ $products->count() }} total</span>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.create') }}" class="btn-add">+ Add Product</a>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.products.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-[10px] text-sm font-semibold no-underline
           text-red-600 dark:text-red-400
           bg-red-50 dark:bg-red-900/20
           border border-red-200 dark:border-red-800
           hover:bg-red-100 transition-all duration-150">
                    🗑️ Trash
                </a>
            @endif
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">All Products</div>
            <span class="table-total">{{ $products->count() }} records</span>
        </div>

        @if ($products->count())
            <table>
                <thead>
                    <tr>
                        <th style="width:48px">#</th>
                        <th>Product</th>
                        <th>Barcode</th>
                        <th>Category</th>
                        <th>Sell Price</th>
                        <th>Stock</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><span class="row-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></td>

                            <td>
                                <div class="product-cell">
                                    <img class="product-thumb"
                                        src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                        alt="{{ $product->name }}">
                                    <span class="product-name">{{ $product->name }}</span>
                                </div>
                            </td>

                            <td>
                                @if ($product->barcode)
                                    <span class="barcode-val">{{ $product->barcode }}</span>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="cat-badge">🗂️ {{ $product->category->name ?? '—' }}</span>
                            </td>

                            <td><span class="price-val">${{ number_format($product->sell_price, 2) }}</span></td>

                            <td>
                                @if ($product->stock <= $product->low_stock_alert)
                                    <span class="stock-low">{{ $product->stock }}</span>
                                @else
                                    <span class="stock-ok">{{ $product->stock }}</span>
                                @endif
                            </td>

                            <td class="right">
                                <div class="action-wrap">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="action-btn action-edit">✏️ Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        onsubmit="return confirm('Delete « {{ $product->name }} »? This cannot be undone.')">
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
                <div class="empty-icon">📦</div>
                <div class="empty-title">No products yet</div>
                <div class="empty-sub">Add your first product to start selling.</div>
                <a href="{{ route('admin.products.create') }}" class="btn-add">+ Add First Product</a>
            </div>
        @endif
    </div>

@endsection
