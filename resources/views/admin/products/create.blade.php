@extends('layouts.app')
@section('title', 'Add Product')
@section('content')
<div class="max-w-5xl mx-auto py-6">

    <h2 class="dark:text-white text-2xl font-bold mb-6">Create Product</h2>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-6">

                <div>
                    <label class="block mb-1 font-medium">Category</label>
                    <select name="category_id"
                            class="w-full border rounded px-3 py-2"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Barcode</label>
                    <input type="text" name="barcode"
                           class="w-full border rounded px-3 py-2"
                           required>
                    @error('barcode')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Product Name</label>
                    <input type="text" name="name"
                           class="w-full border rounded px-3 py-2"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Cost Price</label>
                    <input type="number" step="0.01" name="cost_price"
                           class="w-full border rounded px-3 py-2"
                           required>
                    @error('cost_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Sell Price</label>
                    <input type="number" step="0.01" name="sell_price"
                           class="w-full border rounded px-3 py-2"
                           required>
                    @error('sell_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Stock</label>
                    <input type="number" name="stock"
                           class="w-full border rounded px-3 py-2"
                           required>
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Low Stock Alert</label>
                    <input type="number" name="low_stock_alert"
                           class="w-full border rounded px-3 py-2"
                           value="5">
                    @error('low_stock_alert')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Product Image</label>

                    @if(isset($product) && $product->image)
                        <img src="{{ $product->image_url }}"
                            class="h-20 mb-2 rounded">
                    @endif

                    <input type="file"
                        name="image"
                        class="w-full border rounded px-3 py-2">
                        
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.products.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-lg">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Save
                </button>
            </div>

        </form>
    </div>

</div>
@endsection