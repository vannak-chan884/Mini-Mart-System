@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Stat card accent bar — cannot be done in Tailwind */
        .stat-card { position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; }
        .stat-card.blue::before   { background: linear-gradient(90deg, #003087, #1a4db3); }
        .stat-card.green::before  { background: linear-gradient(90deg, #15803D, #16A34A); }
        .stat-card.gold::before   { background: linear-gradient(90deg, #92400E, #F4A900); }
        .stat-card.red::before    { background: linear-gradient(90deg, #7F1D1D, #CC0001); }
        .stat-card.teal::before   { background: linear-gradient(90deg, #0F766E, #14B8A6); }
        .stat-card.purple::before { background: linear-gradient(90deg, #581C87, #9333EA); }

        /* Low stock row pulse */
        .stock-critical { animation: stockPulse 2s ease infinite; }
        @keyframes stockPulse {
            0%,100% { background: rgba(204,0,1,0.06); }
            50%      { background: rgba(204,0,1,0.12); }
        }
    </style>
@endpush

@section('content')

    {{-- ── Greeting ─────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-7 flex-wrap gap-3">
        <div>
            <h1 class="font-serif font-bold text-2xl leading-tight text-[#0D0D14] dark:text-white">
                Good {{ now('Asia/Phnom_Penh')->hour < 12 ? 'morning' : (now('Asia/Phnom_Penh')->hour < 18 ? 'afternoon' : 'evening') }},
                {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ now('Asia/Phnom_Penh')->format('l, d F Y') }} · Here's what's happening today.
            </p>
        </div>
        <a href="{{ route('admin.pos.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white
                   bg-gradient-to-br from-[#15803D] to-[#16A34A] border-none no-underline
                   shadow-[0_3px_12px_rgba(22,163,74,0.3)] hover:-translate-y-0.5
                   hover:shadow-[0_6px_20px_rgba(22,163,74,0.4)] transition-all">
            🖥️ Open POS Terminal
        </a>
    </div>

    {{-- ── Stat cards ───────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-7">

        {{-- Today Revenue --}}
        <div class="stat-card gold col-span-2 lg:col-span-1 xl:col-span-2
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Today's Revenue</div>
                <span class="text-xl">💰</span>
            </div>
            <div class="font-mono text-3xl font-bold text-[#0D0D14] dark:text-white mb-1">
                ${{ number_format($todayRevenue ?? 0, 2) }}
            </div>
            <div class="text-[12px] text-gray-500 dark:text-gray-600">
                {{ $todaySales ?? 0 }} transaction{{ ($todaySales ?? 0) != 1 ? 's' : '' }} today
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="stat-card green
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Revenue</div>
                <span class="text-xl">📈</span>
            </div>
            <div class="font-mono text-2xl font-bold text-[#0D0D14] dark:text-white mb-1">
                ${{ number_format($totalRevenue ?? 0, 2) }}
            </div>
            <div class="text-[12px] text-gray-500 dark:text-gray-600">All time</div>
        </div>

        {{-- Total Sales --}}
        <div class="stat-card blue
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Sales</div>
                <span class="text-xl">🧾</span>
            </div>
            <div class="font-mono text-2xl font-bold text-[#0D0D14] dark:text-white mb-1">
                {{ number_format($totalSales ?? 0) }}
            </div>
            <div class="text-[12px] text-gray-500 dark:text-gray-600">Orders</div>
        </div>

        {{-- Products --}}
        <div class="stat-card teal
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Products</div>
                <span class="text-xl">📦</span>
            </div>
            <div class="font-mono text-2xl font-bold text-[#0D0D14] dark:text-white mb-1">
                {{ $totalProducts ?? 0 }}
            </div>
            <div class="text-[12px] text-gray-500 dark:text-gray-600">In catalog</div>
        </div>

        {{-- Low Stock --}}
        <div class="stat-card red
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Low Stock</div>
                <span class="text-xl">⚠️</span>
            </div>
            <div class="font-mono text-2xl font-bold mb-1
                        {{ ($lowStockCount ?? 0) > 0 ? 'text-red-400' : 'text-[#0D0D14] dark:text-white' }}">
                {{ $lowStockCount ?? 0 }}
            </div>
            <div class="text-[12px] text-gray-500 dark:text-gray-600">Items to restock</div>
        </div>

    </div>

    {{-- ── Middle row: Recent Sales + Top Products ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Recent Sales (2/3 width) --}}
        <div class="lg:col-span-2
                    bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4
                        border-b border-black/[0.08] dark:border-white/[0.07]
                        bg-black/[0.02] dark:bg-white/[0.02]">
                <div class="text-[13px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Recent Sales</div>
                <a href="{{ route('admin.sales.index') }}"
                    class="text-[12px] text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 no-underline transition-colors">
                    View all →
                </a>
            </div>

            @if (isset($recentSales) && $recentSales->count())
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-black/[0.08] dark:border-white/[0.07]">
                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-600">Invoice</th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-600">Time</th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-600">Payment</th>
                            <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentSales as $sale)
                            <tr class="border-b border-black/[0.05] dark:border-white/[0.04]
                                       hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-3">
                                    <span class="font-mono text-[12px] text-blue-600 dark:text-blue-400
                                                 bg-blue-900/20 dark:bg-blue-100
                                                 border border-blue-700/25 dark:border-blue-300/50
                                                 px-2 py-0.5 rounded">
                                        #{{ $sale->invoice_no ?? str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-[12.5px] text-gray-500 dark:text-gray-400">
                                    {{ $sale->created_at->timezone('Asia/Phnom_Penh')->format('d M, h:i A') }}
                                </td>
                                <td class="px-5 py-3">
                                    @if ($sale->payment_method === 'cash')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md
                                                     bg-green-100 dark:bg-green-900/20
                                                     border border-green-300/50 dark:border-green-700/30
                                                     text-green-700 dark:text-green-400">
                                            💵 Cash
                                        </span>
                                    @elseif($sale->payment_method === 'khqr_usd')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md
                                                     bg-blue-100 dark:bg-blue-900/20
                                                     border border-blue-300/50 dark:border-blue-700/30
                                                     text-blue-700 dark:text-blue-400">
                                            $ KHQR USD
                                        </span>
                                    @else
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md
                                                     bg-red-100 dark:bg-red-900/20
                                                     border border-red-300/50 dark:border-red-700/30
                                                     text-red-700 dark:text-red-400">
                                            ៛ KHQR KHR
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-mono text-[13px] font-bold text-[#0D0D14] dark:text-white">
                                    ${{ number_format($sale->total_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400 dark:text-gray-600">
                    <span class="text-4xl mb-3 opacity-30">🧾</span>
                    <p class="text-sm">No sales yet today.</p>
                </div>
            @endif
        </div>

        {{-- Top Products (1/3 width) --}}
        <div class="bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4
                        border-b border-black/[0.08] dark:border-white/[0.07]
                        bg-black/[0.02] dark:bg-white/[0.02]">
                <div class="text-[13px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Top Products</div>
                <span class="text-[11px] font-mono text-gray-400 dark:text-gray-600
                             bg-black/[0.04] dark:bg-white/[0.04]
                             border border-black/[0.08] dark:border-white/[0.07]
                             px-2 py-0.5 rounded-full">
                    30 days
                </span>
            </div>

            @if (isset($topProducts) && $topProducts->count())
                <div class="divide-y divide-black/[0.06] dark:divide-white/[0.04]">
                    @foreach ($topProducts as $i => $item)
                        <div class="flex items-center gap-3 px-5 py-3.5
                                    hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-colors">
                            <span class="font-mono text-[11px] font-bold w-5 text-center
                                         {{ $i === 0 ? 'text-amber-400' : ($i === 1 ? 'text-gray-500 dark:text-gray-400' : ($i === 2 ? 'text-amber-700' : 'text-gray-700 dark:text-gray-400')) }}">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-semibold text-gray-700 dark:text-gray-200 truncate">
                                    {{ $item->product->name ?? 'Unknown' }}
                                </div>
                                <div class="text-[11px] text-gray-400 dark:text-gray-600 mt-0.5">
                                    {{ $item->total_qty }} sold
                                </div>
                            </div>
                            <span class="font-mono text-[13px] font-bold text-green-600 dark:text-green-400 flex-shrink-0">
                                ${{ number_format($item->total_revenue, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400 dark:text-gray-600">
                    <span class="text-4xl mb-3 opacity-30">📦</span>
                    <p class="text-sm">No data yet.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ── Bottom row: Low Stock + Payment breakdown ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Low Stock Alert --}}
        <div class="bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4
                        border-b border-black/[0.08] dark:border-white/[0.07]
                        bg-black/[0.02] dark:bg-white/[0.02]">
                <div class="text-[13px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">⚠️ Low Stock Alerts</div>
                <a href="{{ route('admin.products.index') }}"
                    class="text-[12px] text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 no-underline transition-colors">
                    Manage →
                </a>
            </div>

            @if (isset($lowStockProducts) && $lowStockProducts->count())
                <div class="divide-y divide-black/[0.06] dark:divide-white/[0.04]">
                    @foreach ($lowStockProducts as $product)
                        <div class="flex items-center gap-3 px-5 py-3 {{ $product->stock == 0 ? 'stock-critical' : '' }}">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}"
                                class="w-9 h-9 rounded-lg object-cover
                                       border border-black/[0.08] dark:border-white/[0.07]
                                       flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-semibold text-gray-700 dark:text-gray-200 truncate">
                                    {{ $product->name }}
                                </div>
                                <div class="text-[11px] text-gray-400 dark:text-gray-600">
                                    Alert at {{ $product->low_stock_alert }}
                                </div>
                            </div>
                            <span class="font-mono text-[13px] font-bold px-2.5 py-1 rounded-lg
                                         {{ $product->stock == 0
                                             ? 'text-red-700 bg-red-100 dark:bg-red-900/20 border border-red-300/50 dark:border-red-700/30 dark:text-red-400'
                                             : 'text-amber-700 bg-amber-100 dark:bg-amber-900/20 border border-amber-300/50 dark:border-amber-700/30 dark:text-amber-400' }}">
                                {{ $product->stock == 0 ? 'Out' : $product->stock }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-14 text-gray-400 dark:text-gray-600">
                    <span class="text-4xl mb-3 opacity-30">✅</span>
                    <p class="text-sm font-medium text-green-500 dark:text-green-600">All products well stocked!</p>
                </div>
            @endif
        </div>

        {{-- Payment method breakdown --}}
        <div class="bg-black/[0.03] dark:bg-white/[0.03]
                    border border-black/[0.08] dark:border-white/[0.07]
                    rounded-2xl overflow-hidden">
            <div class="px-6 py-4
                        border-b border-black/[0.08] dark:border-white/[0.07]
                        bg-black/[0.02] dark:bg-white/[0.02]">
                <div class="text-[13px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment Breakdown</div>
                <div class="text-[11px] text-gray-400 dark:text-gray-600 mt-0.5">All time · by revenue</div>
            </div>

            <div class="p-6 flex flex-col gap-4">
                @php
                    $cashRev = $paymentBreakdown['cash'] ?? 0;
                    $usdRev  = $paymentBreakdown['khqr_usd'] ?? 0;
                    $khrRev  = $paymentBreakdown['khqr_khr'] ?? 0;
                    $grandTot = $cashRev + $usdRev + $khrRev ?: 1;
                    $bars = [
                        ['label' => '💵 Cash',      'value' => $cashRev, 'color' => 'bg-green-500', 'pct' => round(($cashRev / $grandTot) * 100)],
                        ['label' => '🇺🇸 KHQR USD', 'value' => $usdRev,  'color' => 'bg-blue-500',  'pct' => round(($usdRev  / $grandTot) * 100)],
                        ['label' => '🇰🇭 KHQR KHR', 'value' => $khrRev,  'color' => 'bg-red-500',   'pct' => round(($khrRev  / $grandTot) * 100)],
                    ];
                @endphp

                @foreach ($bars as $bar)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[12.5px] font-semibold text-gray-600 dark:text-gray-300">{{ $bar['label'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[12px] font-bold text-[#0D0D14] dark:text-white">
                                    ${{ number_format($bar['value'], 2) }}
                                </span>
                                <span class="font-mono text-[11px] text-gray-400 dark:text-gray-600">{{ $bar['pct'] }}%</span>
                            </div>
                        </div>
                        <div class="h-2 bg-black/[0.08] dark:bg-white/[0.06] rounded-full overflow-hidden">
                            <div class="{{ $bar['color'] }} h-full rounded-full transition-all duration-700"
                                style="width: {{ $bar['pct'] }}%"></div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-2 pt-4 border-t border-black/[0.08] dark:border-white/[0.07] flex justify-between items-center">
                    <span class="text-[12px] text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider">Grand Total</span>
                    <span class="font-mono text-lg font-bold text-[#0D0D14] dark:text-white">
                        ${{ number_format($grandTot == 1 ? 0 : $grandTot, 2) }}
                    </span>
                </div>
            </div>
        </div>

    </div>

@endsection