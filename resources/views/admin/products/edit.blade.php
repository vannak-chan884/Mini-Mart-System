@extends('layouts.app')
@section('title', 'Edit Product')

@push('styles')
    <style>
        /* Custom styles that extend Tailwind */
        .field-input:focus,
        .field-select:focus {
            border-color: rgba(0, 80, 200, 0.5) !important;
            background: rgba(0, 48, 135, 0.08) !important;
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.12) !important;
            outline: none;
        }

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

        .image-upload-label:hover {
            border-color: rgba(0, 80, 200, 0.4);
            background: rgba(0, 48, 135, 0.06);
        }

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
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-200 mb-5 transition-colors no-underline">
        ← Back to Products
    </a>

    {{-- Card --}}
    <div class="rounded-2xl overflow-hidden border border-white/[0.07] bg-white/[0.03]">

        {{-- Card header --}}
        <div class="flex items-center gap-3 px-7 py-5 border-b border-white/[0.07] bg-white/[0.02]">
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center text-lg
                    bg-amber-900/20 border border-amber-500/25">
                ✏️
            </div>
            <div>
                <div class="font-serif text-white font-bold text-lg leading-tight">Edit Product</div>
                <div class="text-xs text-gray-500 mt-0.5">Update product details</div>
            </div>
            {{-- Editing badge --}}
            <div
                class="ml-auto flex items-center gap-2 bg-amber-500/10 border border-amber-500/25
                    rounded-full px-3 py-1 text-xs font-mono font-semibold text-amber-300">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                {{ Str::limit($product->name, 28) }}
            </div>
        </div>

        {{-- Form --}}
        <div class="p-7">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ── Basic Info ──────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 pb-3
                       border-b border-white/[0.07] mb-5">
                    Basic Information
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Category --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Category</label>
                        <select name="category_id"
                            class="field-select w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                   px-3 py-2.5 text-sm text-gray-200 font-sans transition-all
                                   {{ $errors->has('category_id') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                            required>
                            <option value="">Select category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                                    class="bg-gray-900">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Barcode --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Barcode</label>
                        <input type="text" name="barcode"
                            class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                  px-3 py-2.5 text-sm text-gray-200 font-sans transition-all
                                  placeholder-gray-600
                                  {{ $errors->has('barcode') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                            placeholder="e.g. 8850006151227" value="{{ old('barcode', $product->barcode) }}" required>
                        @error('barcode')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Name (full width) --}}
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Product Name</label>
                        <input type="text" name="name"
                            class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                  px-3 py-2.5 text-sm text-gray-200 font-sans transition-all
                                  placeholder-gray-600
                                  {{ $errors->has('name') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                            placeholder="e.g. Cambodia Cola 330ml" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Pricing ──────────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 pb-3
                       border-b border-white/[0.07] mb-5">
                    Pricing
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Cost Price --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Cost Price</label>
                        <div class="relative">
                            <span class="price-prefix">$</span>
                            <input type="number" step="0.01" name="cost_price"
                                class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                      pl-6 pr-3 py-2.5 text-sm text-gray-200 font-mono transition-all
                                      {{ $errors->has('cost_price') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                                placeholder="0.00" value="{{ old('cost_price', $product->cost_price) }}" required>
                        </div>
                        @error('cost_price')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sell Price --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Sell Price</label>
                        <div class="relative">
                            <span class="price-prefix">$</span>
                            <input type="number" step="0.01" name="sell_price"
                                class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                      pl-6 pr-3 py-2.5 text-sm text-gray-200 font-mono transition-all
                                      {{ $errors->has('sell_price') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                                placeholder="0.00" value="{{ old('sell_price', $product->sell_price) }}" required>
                        </div>
                        @error('sell_price')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Inventory ─────────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 pb-3
                       border-b border-white/[0.07] mb-5">
                    Inventory
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

                    {{-- Stock --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Stock</label>
                        <input type="number" name="stock"
                            class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                  px-3 py-2.5 text-sm text-gray-200 font-mono transition-all
                                  {{ $errors->has('stock') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                            placeholder="0" value="{{ old('stock', $product->stock) }}" required>
                        @error('stock')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Low Stock Alert --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Low Stock Alert</label>
                        <input type="number" name="low_stock_alert"
                            class="field-input w-full bg-white/[0.05] border border-white/[0.07] rounded-xl
                                  px-3 py-2.5 text-sm text-gray-200 font-mono transition-all
                                  {{ $errors->has('low_stock_alert') ? 'border-red-700/60 bg-red-950/20' : '' }}"
                            placeholder="5" value="{{ old('low_stock_alert', $product->low_stock_alert ?? 5) }}">
                        @error('low_stock_alert')
                            <p class="text-xs text-red-400 flex items-center gap-1">⚠ {{ $message }}</p>
                        @enderror
                        <p class="text-[11.5px] text-gray-600 leading-snug">
                            ⚠️ A warning badge appears on the product list when stock drops to or below this number.
                        </p>
                    </div>

                </div>

                {{-- ── Image ────────────────────────────────── --}}
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 pb-3
                       border-b border-white/[0.07] mb-5">
                    Product Image
                </p>

                <div class="mb-2">

                    {{-- Current image --}}
                    @if ($product->image)
                        <div class="mb-3 relative inline-block" id="currentImageWrap">
                            <img src="{{ $product->image_url ?? asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="h-36 w-auto rounded-xl border border-white/[0.07] object-cover">
                            <span
                                class="absolute top-2 left-2 bg-black/60 text-white text-[10px]
                                 font-bold px-2 py-0.5 rounded-md">Current</span>
                        </div>
                    @endif

                    {{-- New image preview (hidden until file chosen) --}}
                    <div id="imagePreview" class="hidden relative mb-3">
                        <img id="previewImg" src="" alt="New preview"
                            class="h-36 w-auto rounded-xl border border-amber-500/30 object-cover">
                        <span
                            class="absolute top-2 left-2 bg-amber-500/80 text-white text-[10px]
                                 font-bold px-2 py-0.5 rounded-md">New</span>
                        <button type="button" onclick="clearImage()"
                            class="absolute top-2 right-2 bg-red-700/80 hover:bg-red-600 text-white
                                   text-[11px] font-bold px-2 py-0.5 rounded-md transition-colors">
                            ✕ Remove
                        </button>
                    </div>

                    {{-- Upload zone --}}
                    <div id="uploadArea">
                        <label
                            class="image-upload-label flex flex-col items-center justify-center gap-2
                                  py-7 px-4 rounded-xl cursor-pointer text-center transition-all
                                  bg-white/[0.03] border-2 border-dashed border-white/10 relative">
                            <span class="text-3xl opacity-40">🖼️</span>
                            <span class="text-sm font-semibold text-gray-400">
                                {{ $product->image ? 'Click to replace image' : 'Click to upload image' }}
                            </span>
                            <span class="text-xs text-gray-600">JPG, PNG, WEBP — max 2MB</span>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                onchange="previewImage(event)"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        </label>
                    </div>

                    @error('image')
                        <p class="text-xs text-red-400 mt-2 flex items-center gap-1">⚠ {{ $message }}</p>
                    @enderror
                </div>

                {{-- ── Actions ──────────────────────────────── --}}
                <div class="flex items-center justify-end gap-3 pt-6 mt-2 border-t border-white/[0.07]">
                    <a href="{{ route('admin.products.index') }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-400
                          bg-white/[0.04] border border-white/[0.07]
                          hover:bg-white/[0.08] hover:text-white transition-all no-underline">
                        Cancel
                    </a>
                    <button type="submit"
                        class="btn-save inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                               text-sm font-bold text-white
                               bg-gradient-to-br from-[#003087] to-[#1a4db3]
                               border-none cursor-pointer transition-all
                               shadow-[0_3px_12px_rgba(0,48,135,0.35)]">
                        💾 Update Product
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
