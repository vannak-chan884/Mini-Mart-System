@extends('layouts.app')
@section('title', 'Products')
@section('content')
    <div class="max-w-5xl mx-auto py-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="dark:text-white text-2xl font-bold">Products</h2>

            <a href="{{ route('admin.products.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Add Product
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Barcode</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Image</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Sell Price</th>
                        <th class="p-3">Stock</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-t">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $product->barcode }}</td>
                            <td class="p-3">{{ $product->name }}</td>
                            <td class="p-3">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-12 h-12 object-cover rounded-lg">
                            </td>
                            <td class="p-3">{{ $product->category->name }}</td>
                            <td class="p-3">${{ number_format($product->sell_price, 2) }}</td>
                            <td class="p-3">
                                <span
                                    class="{{ $product->stock <= $product->low_stock_alert ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="text-blue-600 hover:underline">Edit</a>

                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
