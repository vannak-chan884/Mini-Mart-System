@extends('layouts.app')
@section('title', 'Update Product')

@section('content')
    <div class="max-w-5xl mx-auto py-6">

        <h2 class="dark:text-white text-2xl font-bold mb-6">Edit Product</h2>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-6">

                    {{-- Category --}}
                    <div>
                        <label class="block mb-1 font-medium">Category</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="block mb-1 font-medium">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block mb-1 font-medium">Product Name</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    {{-- Cost Price --}}
                    <div>
                        <label class="block mb-1 font-medium">Cost Price</label>
                        <input type="number" step="0.01" name="cost_price"
                            value="{{ old('cost_price', $product->cost_price) }}" class="w-full border rounded px-3 py-2"
                            required>
                    </div>

                    {{-- Sell Price --}}
                    <div>
                        <label class="block mb-1 font-medium">Sell Price</label>
                        <input type="number" step="0.01" name="sell_price"
                            value="{{ old('sell_price', $product->sell_price) }}" class="w-full border rounded px-3 py-2"
                            required>
                    </div>

                    {{-- Stock --}}
                    <div>
                        <label class="block mb-1 font-medium">Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    {{-- Low Stock Alert --}}
                    <div>
                        <label class="block mb-1 font-medium">Low Stock Alert</label>
                        <input type="number" name="low_stock_alert"
                            value="{{ old('low_stock_alert', $product->low_stock_alert ?? 5) }}"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Product Image</label>

                        @if (isset($product) && $product->image)
                            <img src="{{ $product->image_url }}" class="h-20 mb-2 rounded">
                        @endif

                        <input type="file" name="image" class="w-full border rounded px-3 py-2">
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror   
                    </div>

                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                        Cancel
                    </a>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
