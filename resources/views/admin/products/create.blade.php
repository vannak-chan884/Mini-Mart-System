@extends('layouts.app')
@section('title', 'Add Product')

@push('styles')
    <style>
        /* 3 things Tailwind can't express without custom config */

        /* Focus ring with arbitrary rgba */
        .field-input:focus,
        .field-select:focus {
            border-color: rgba(0, 80, 200, 0.5) !important;
            background: rgba(0, 48, 135, 0.08) !important;
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.12) !important;
            outline: none;
        }

        /* Absolute $ prefix inside price input */
        .price-prefix {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #4ADE80;
            pointer-events: none;
        }

        /* Upload zone hover with arbitrary rgba */
        .upload-zone:hover {
            border-color: rgba(0, 80, 200, 0.4);
            background: rgba(0, 48, 135, 0.06);
        }

        /* Save button lift on hover */
        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 48, 135, 0.45);
        }

        .btn-save:active {
            transform: translateY(0);
        }
    </style>
@endpush

@section('content')

    {{-- Back link --}}
    <a href="{{ route('admin.products.index') }}"
        class="inline-flex items-center gap-1.5 text-[13px] text-gray-500
          hover:text-gray-200 no-underline mb-5 transition-colors">
        ← Back to Products
    </a>

    {{-- Form card --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-center gap-3 px-7 pt-5 pb-4
                border-b border-white/[0.07] bg-white/[0.02]">
            <div
                class="w-[38px] h-[38px] flex-shrink-0 flex items-center justify-center
                    text-lg rounded-xl bg-blue-900/20 border border-blue-700/35">
                📦
            </div>
            <div>
                <div class="font-serif text-white font-bold text-[17px] leading-tight">Add Product</div>
                <div class="text-xs text-gray-500 mt-0.5">Fill in the details to create a new product</div>
            </div>
        </div>

        {{-- Card body --}}
        <div class="p-7">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ── Basic Info ──────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500
                       pb-3 border-b border-white/[0.07] mb-5">
                    Basic Information
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Category --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Category
                        </label>
                        <select name="category_id"
                            class="field-select w-full dark:bg-white/[0.05] border dark:border-white/[0.07]
                                   rounded-xl px-3 py-2.5 text-[13.5px] dark:text-gray-200 transition-all
                                   {{ $errors->has('category_id') ? 'dark:border-red-700/50 dark:bg-red-950/[0.05]' : '' }}"
                            required>
                            <option value="">Select category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }} class="bg-gray-900">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Barcode --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Barcode
                        </label>
                        <input type="text" name="barcode"
                            class="field-input w-full dark:bg-white/[0.05] border dark:border-white/[0.07]
                                  rounded-xl px-3 py-2.5 text-[13.5px] dark:text-gray-200
                                  placeholder-gray-600 transition-all
                                  {{ $errors->has('barcode') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                            placeholder="e.g. 8850006151227" value="{{ old('barcode') }}" required>
                        @error('barcode')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Name (full width) --}}
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider dark:text-gray-400">
                            Product Name
                        </label>
                        <input type="text" name="name"
                            class="field-input w-full dark:bg-white/[0.05] border dark:border-white/[0.07]
                                  rounded-xl px-3 py-2.5 text-[13.5px] dark:text-gray-200
                                  placeholder-gray-600 transition-all
                                  {{ $errors->has('name') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                            placeholder="e.g. Cambodia Cola 330ml" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Pricing ──────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest dark:text-gray-500
                       pb-3 border-b dark:border-white/[0.07] mb-5">
                    Pricing
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Cost Price --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider dark:text-gray-400">
                            Cost Price
                        </label>
                        <div class="relative">
                            <span class="price-prefix">$</span>
                            <input type="number" step="0.01" name="cost_price"
                                class="field-input w-full bg-white/[0.05] border dark:border-white/[0.07]
                                      rounded-xl pl-6 pr-3 py-2.5 text-[13.5px] dark:text-gray-200
                                      font-mono transition-all
                                      {{ $errors->has('cost_price') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                                placeholder="0.00" value="{{ old('cost_price') }}" required>
                        </div>
                        @error('cost_price')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sell Price --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider dark:text-gray-400">
                            Sell Price
                        </label>
                        <div class="relative">
                            <span class="price-prefix">$</span>
                            <input type="number" step="0.01" name="sell_price"
                                class="field-input w-full bg-white/[0.05] border dark:border-white/[0.07]
                                      rounded-xl pl-6 pr-3 py-2.5 text-[13.5px] dark:text-gray-200
                                      font-mono transition-all
                                      {{ $errors->has('sell_price') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                                placeholder="0.00" value="{{ old('sell_price') }}" required>
                        </div>
                        @error('sell_price')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Inventory ────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest dark:text-gray-500
                       pb-3 border-b dark:border-white/[0.07] mb-5">
                    Inventory
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Stock --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider dark:text-gray-400">
                            Initial Stock
                        </label>
                        <input type="number" name="stock"
                            class="field-input w-full bg-white/[0.05] border dark:border-white/[0.07]
                                  rounded-xl px-3 py-2.5 text-[13.5px] dark:text-gray-200 font-mono transition-all
                                  {{ $errors->has('stock') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                            placeholder="0" value="{{ old('stock') }}" required>
                        @error('stock')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Low Stock Alert --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider dark:text-gray-400">
                            Low Stock Alert
                        </label>
                        <input type="number" name="low_stock_alert"
                            class="field-input w-full bg-white/[0.05] border dark:border-white/[0.07]
                                  rounded-xl px-3 py-2.5 text-[13.5px] dark:text-gray-200 font-mono transition-all
                                  {{ $errors->has('low_stock_alert') ? 'border-red-700/50 bg-red-950/[0.05]' : '' }}"
                            placeholder="5" value="{{ old('low_stock_alert', 5) }}">
                        @error('low_stock_alert')
                            <p class="text-xs text-red-300 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                        <p class="text-[11.5px] text-gray-600 leading-snug">
                            ⚠️ A warning badge appears on the product list when stock drops to or below this number.
                        </p>
                    </div>

                </div>

                {{-- ── Image ────────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest dark:text-gray-500
                       pb-3 border-b dark:border-white/[0.07] mb-5">
                    Product Image
                </p>
                <div class="mb-2">

                    {{-- Preview --}}
                    <div id="imagePreview" class="hidden relative mb-3">
                        <img id="previewImg" src="" alt="Preview"
                            class="w-full max-h-[180px] object-cover rounded-xl border dark:border-white/[0.07]">
                        <button type="button" onclick="clearImage()"
                            class="absolute top-2 right-2 bg-red-700/85 hover:bg-red-600
                                   dark:text-white text-[11px] font-bold px-2.5 py-1 rounded-lg
                                   border-none cursor-pointer transition-colors">
                            ✕ Remove
                        </button>
                    </div>

                    {{-- Upload zone --}}
                    <div id="uploadArea">
                        <label
                            class="upload-zone flex flex-col items-center justify-content-center gap-2
                                  py-7 px-4 dark:bg-white/[0.03] border-2 border-dashed dark:border-white/10
                                  rounded-xl cursor-pointer text-center transition-all relative">
                            <span class="text-[30px] opacity-45">🖼️</span>
                            <span class="text-[13px] font-semibold dark:text-gray-400">Click to upload image</span>
                            <span class="text-[11px] dark:text-gray-600">JPG, PNG, WEBP — max 2MB</span>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                onchange="previewImage(event)"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        </label>
                    </div>

                    @error('image')
                        <p class="text-xs text-red-300 flex items-center gap-1 mt-2">⚠ {{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2.5 pt-6 mt-2 border-t dark:border-white/[0.07]">
                    <a href="{{ route('admin.products.index') }}"
                        class="px-[18px] py-2.5 rounded-xl text-[13px] font-semibold dark:text-gray-400
                          bg-white/[0.04] border dark:border-white/[0.07] no-underline transition-all
                          hover:bg-white/[0.08] dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                        class="btn-save inline-flex items-center gap-1.5 px-[22px] py-2.5 rounded-xl
                               text-[13px] font-bold text-white border-none cursor-pointer transition-all
                               bg-gradient-to-br from-[#003087] to-[#1a4db3]
                               shadow-[0_3px_12px_rgba(0,48,135,0.35)]">
                        💾 Save Product
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('uploadArea').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function clearImage() {
            document.getElementById('imageInput').value = '';
            document.getElementById('previewImg').src = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadArea').classList.remove('hidden');
        }
    </script>
@endpush
