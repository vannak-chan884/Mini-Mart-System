<?php

namespace App\Services;

use App\Models\ClosingReport;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClosingReportService
{
    // ── Public entry points ───────────────────────────────────────────────────

    public static function generateDaily(?User $triggeredBy = null, bool $manual = false): ClosingReport
    {
        $tz    = 'Asia/Phnom_Penh';
        $start = Carbon::now($tz)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

        return static::generate('daily', $start, $end, $triggeredBy, $manual);
    }

    public static function generateWeekly(?User $triggeredBy = null, bool $manual = false): ClosingReport
    {
        $tz    = 'Asia/Phnom_Penh';
        $start = Carbon::now($tz)->startOfWeek();
        $end   = Carbon::now($tz)->endOfWeek();

        return static::generate('weekly', $start, $end, $triggeredBy, $manual);
    }

    public static function generateMonthly(?User $triggeredBy = null, bool $manual = false): ClosingReport
    {
        $tz    = 'Asia/Phnom_Penh';
        $start = Carbon::now($tz)->startOfMonth();
        $end   = Carbon::now($tz)->endOfMonth();

        return static::generate('monthly', $start, $end, $triggeredBy, $manual);
    }

    // ── Core generator ────────────────────────────────────────────────────────

    private static function generate(
        string  $type,
        Carbon  $start,
        Carbon  $end,
        ?User   $triggeredBy,
        bool    $manual
    ): ClosingReport {

        // ── Sales data ────────────────────────────────────────────────────────
        $sales = Sale::whereBetween('created_at', [$start, $end])->get();

        $totalRevenue      = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $cashAmount        = $sales->where('payment_method', 'cash')->sum('paid_amount');
        // $khqrAmount = $sales->where('payment_method', 'khqr', 'khqr_usd', 'khqr_khr')->sum('paid_amount');
        $khqrKHRAmount        = $sales->where('payment_method', 'khqr_khr')->sum('paid_amount');
        $khqrUSDAmount        = $sales->where('payment_method', 'khqr_usd')->sum('paid_amount');
        $khqrAmount = $khqrKHRAmount + $khqrUSDAmount;
        $abaAmount         = $sales->where('payment_method', 'aba_payway')->sum('paid_amount');

        // ── Expenses ──────────────────────────────────────────────────────────
        $totalExpenses = Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $netProfit     = $totalRevenue - $totalExpenses;

        // ── Top products ──────────────────────────────────────────────────────
        $topProducts = DB::table('sale_items')
            ->join('sales',    'sales.id',    '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'name'    => $p->name,
                'qty'     => (int) $p->total_qty,
                'revenue' => (float) $p->total_revenue,
            ])
            ->toArray();

        // ── Staff performance ─────────────────────────────────────────────────
        $staffPerformance = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->select(
                'users.id',
                'users.name',
                'users.role',
                DB::raw('SUM(sale_items.quantity) as items_sold'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as transactions')
            )
            ->groupBy('users.id', 'users.name', 'users.role')
            ->orderByDesc('items_sold')
            ->get()
            ->map(fn($u) => [
                'name'         => $u->name,
                'role'         => $u->role,
                'items_sold'   => (int) $u->items_sold,
                'revenue'      => (float) $u->revenue,
                'transactions' => (int) $u->transactions,
            ])
            ->toArray();

        // ── Save report ───────────────────────────────────────────────────────
        $report = ClosingReport::create([
            'type'                  => $type,
            'period_start'          => $start->toDateString(),
            'period_end'            => $end->toDateString(),
            'total_revenue'         => $totalRevenue,
            'total_transactions'    => $totalTransactions,
            'cash_amount'           => $cashAmount,
            'khqr_amount'           => $khqrAmount,
            'aba_amount'            => $abaAmount,
            'total_expenses'        => $totalExpenses,
            'net_profit'            => $netProfit,
            'top_products'          => $topProducts,
            'staff_performance'     => $staffPerformance,
            'triggered_by'          => $manual ? 'manual' : 'scheduler',
            'triggered_by_user_id'  => $triggeredBy?->id,
            'telegram_sent'         => false,
        ]);

        // ── Send Telegram ─────────────────────────────────────────────────────
        static::sendTelegram($report);

        return $report;
    }

    // ── Telegram ──────────────────────────────────────────────────────────────

    public static function sendTelegram(ClosingReport $report): void
    {
        $token  = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::warning('ClosingReportService: Telegram credentials not configured.');
            return;
        }

        $message = static::buildTelegramMessage($report);

        try {
            $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $report->update([
                    'telegram_sent'    => true,
                    'telegram_sent_at' => now(),
                ]);
            } else {
                Log::error('ClosingReportService: Telegram send failed.', [
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ClosingReportService: Telegram exception.', ['error' => $e->getMessage()]);
        }
    }

    // ── Build Telegram message ────────────────────────────────────────────────

    private static function buildTelegramMessage(ClosingReport $r): string
    {
        $tz       = 'Asia/Phnom_Penh';
        $now      = Carbon::now($tz);
        $typeEmoji = $r->getTypeEmoji();
        $typeLabel = $r->getTypeLabel();

        // Header
        $lines = [
            "{$typeEmoji} <b>Mini Mart POS — {$typeLabel}</b>",
            "🇰🇭 Cambodia  |  " . $now->format('D, d M Y  H:i') . " (ICT)",
            "━━━━━━━━━━━━━━━━━━━━━━━━",
        ];

        // Period
        if ($r->type !== 'daily') {
            $lines[] = "📆 Period: <b>{$r->getPeriodLabel()}</b>";
            $lines[] = "━━━━━━━━━━━━━━━━━━━━━━━━";
        }

        // Revenue & profit
        $lines[] = "💰 Revenue:       <b>\${$r->total_revenue}</b>";
        $lines[] = "💸 Expenses:      <b>\${$r->total_expenses}</b>";
        $profitSign  = $r->net_profit >= 0 ? '+' : '';
        $profitEmoji = $r->net_profit >= 0 ? '📈' : '📉';
        $lines[] = "{$profitEmoji} Net Profit:    <b>{$profitSign}\${$r->net_profit}</b>";
        $lines[] = "━━━━━━━━━━━━━━━━━━━━━━━━";

        // Sales summary
        $lines[] = "🧾 Transactions:  <b>{$r->total_transactions}</b>";
        $lines[] = "💵 Cash:          \${$r->cash_amount}";
        $lines[] = "📱 KHQR Bakong:   \${$r->khqr_amount}";
        $lines[] = "🏦 ABA PayWay:    \${$r->aba_amount}";
        $lines[] = "━━━━━━━━━━━━━━━━━━━━━━━━";

        // Top products
        if (!empty($r->top_products)) {
            $lines[] = "🏆 <b>Top Selling Products</b>";
            $medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
            foreach ($r->top_products as $i => $product) {
                $medal   = $medals[$i] ?? "  {$i}.";
                $revenue = number_format($product['revenue'], 2);
                $lines[] = "  {$medal} {$product['name']}";
                $lines[] = "      ↳ {$product['qty']} items · \${$revenue}";
            }
            $lines[] = "━━━━━━━━━━━━━━━━━━━━━━━━";
        }

        // Staff performance
        if (!empty($r->staff_performance)) {
            $lines[] = "👥 <b>Staff Performance</b>";
            $medals = ['🥇', '🥈', '🥉'];
            foreach ($r->staff_performance as $i => $staff) {
                $medal    = $medals[$i] ?? "  •";
                $role     = ucfirst($staff['role']);
                $revenue  = number_format($staff['revenue'], 2);
                $lines[]  = "  {$medal} {$staff['name']} <i>({$role})</i>";
                $lines[]  = "      ↳ {$staff['items_sold']} items · {$staff['transactions']} sales · \${$revenue}";
            }
            $lines[] = "━━━━━━━━━━━━━━━━━━━━━━━━";
        }

        // Footer
        $triggeredLabel = $r->triggered_by === 'manual' ? '👆 Manual' : '🤖 Auto';
        $lines[] = "⚙️ Triggered: {$triggeredLabel}";
        $lines[] = "🏪 <i>Mini Mart POS · ហាងលក់គ្រឿងទំនិញ</i>";

        return implode("\n", $lines);
    }
}