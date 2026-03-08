@extends('layouts.app')

@section('title', 'Product Trash')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}"
                    class="w-9 h-9 rounded-[10px] flex items-center justify-center no-underline
                       bg-black/[0.04] dark:bg-white/[0.04]
                       border border-black/[0.08] dark:border-white/[0.07]
                       text-[#6B7280] dark:text-[#9CA3AF]
                       hover:text-[#0D0D14] dark:hover:text-white transition-all duration-150">
                    ←
                </a>
                <div>
                    <h2 class="font-playfair text-2xl font-bold text-[#0D0D14] dark:text-white">🗑️ Product Trash</h2>
                    <p class="text-sm text-[#6B7280] dark:text-[#9CA3AF] mt-0.5">
                        Items deleted by cashiers — restore or permanently remove
                    </p>
                </div>
            </div>

            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px]
                     text-xs font-bold
                     bg-red-50 dark:bg-red-900/20
                     border border-red-200 dark:border-red-800
                     text-red-600 dark:text-red-400">
                🗑️ {{ $products->total() }} item(s) in trash
            </span>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2.5 rounded-[10px] px-4 py-3
                    bg-[rgba(22,163,74,0.08)] border border-[rgba(22,163,74,0.25)]
                    text-[13px] font-medium text-green-700 dark:text-[#86EFAC]">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div
            class="bg-white dark:bg-[#13131F] rounded-2xl
                border border-black/[0.08] dark:border-white/[0.07]
                shadow-[0_2px_16px_rgba(0,0,0,0.06)] overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-black/[0.06] dark:border-white/[0.06]
                               bg-black/[0.02] dark:bg-white/[0.02]">
                            <th
                                class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">
                                Product</th>
                            <th
                                class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">
                                Price</th>
                            <th
                                class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">
                                Stock</th>
                            <th
                                class="text-left px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">
                                Deleted At</th>
                            <th
                                class="text-right px-5 py-3.5 text-[11px] font-bold tracking-[0.8px] uppercase text-[#9CA3AF]">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                        @forelse ($products as $product)
                            <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-colors duration-150">

                                {{-- Product --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                class="w-10 h-10 rounded-[8px] object-cover opacity-50">
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-[8px] flex items-center justify-center
                                                    bg-black/[0.05] dark:bg-white/[0.05] text-xl opacity-50">
                                                📦
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-[#0D0D14] dark:text-white line-through opacity-60">
                                                {{ $product->name }}
                                            </div>
                                            @if ($product->barcode)
                                                <div class="font-mono-ibm text-[11px] text-[#9CA3AF]">
                                                    {{ $product->barcode }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Price --}}
                                <td class="px-5 py-3.5 text-[#6B7280] dark:text-[#9CA3AF]">
                                    ${{ number_format($product->sell_price, 2) }}
                                </td>

                                {{-- Stock --}}
                                <td class="px-5 py-3.5 text-[#6B7280] dark:text-[#9CA3AF]">
                                    {{ $product->stock }}
                                </td>

                                {{-- Deleted at --}}
                                <td class="px-5 py-3.5">
                                    <div class="font-mono-ibm text-xs text-red-400 dark:text-red-500">
                                        {{ $product->deleted_at->setTimezone('Asia/Phnom_Penh')->format('d M Y') }}
                                    </div>
                                    <div class="font-mono-ibm text-[11px] text-[#9CA3AF]">
                                        {{ $product->deleted_at->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Restore --}}
                                        <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                   text-xs font-semibold cursor-pointer
                                                   text-emerald-700 dark:text-emerald-400
                                                   bg-emerald-50 dark:bg-emerald-900/20
                                                   border border-emerald-200 dark:border-emerald-800
                                                   hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                                   transition-all duration-150">
                                                ♻️ Restore
                                            </button>
                                        </form>

                                        {{-- Permanent delete (admin only) --}}
                                        @if (auth()->user()->role === 'admin')
                                            <form method="POST"
                                                action="{{ route('admin.products.forceDestroy', $product->id) }}"
                                                onsubmit="return confirm('Permanently delete \'{{ addslashes($product->name) }}\'? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                   text-xs font-semibold cursor-pointer
                                                   text-red-600 dark:text-red-400
                                                   bg-red-50 dark:bg-red-900/20
                                                   border border-red-200 dark:border-red-800
                                                   hover:bg-red-100 dark:hover:bg-red-900/40
                                                   transition-all duration-150">
                                                    🗑️ Delete Forever
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="text-4xl mb-3">✨</div>
                                    <div class="text-[#6B7280] dark:text-[#9CA3AF] text-sm">Trash is empty</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="px-5 py-4 border-t border-black/[0.06] dark:border-white/[0.06]">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
