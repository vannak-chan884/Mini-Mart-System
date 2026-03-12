<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.telegram.bot_token');
        $this->chatId  = config('services.telegram.chat_id');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    // ── Core send ─────────────────────────────────────────────────────────
    public function sendMessage(string $message): bool
    {
        if (empty($this->token) || empty($this->chatId)) {
            Log::warning('Telegram: BOT_TOKEN or CHAT_ID not configured.');
            return false;
        }

        try {
            $response = Http::withoutVerifying()
                ->post("{$this->baseUrl}/sendMessage", [
                    'chat_id'    => $this->chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML',
                ]);

            if (!$response->successful()) {
                Log::warning('Telegram send failed: ' . $response->body());
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    // ── New COD order placed ──────────────────────────────────────────────
    public function notifyNewOrder(\App\Models\Sale $sale): bool
    {
        $sale->loadMissing('items.product', 'user');

        $items = $sale->items->map(function ($item) {
            $name = $item->product?->name ?? 'Unknown';
            return "  • {$name} × {$item->quantity} = \${$item->total}";
        })->join("\n");

        $adminUrl = url("/admin/sales/{$sale->id}");

        $message = "🛒 <b>New Order Received!</b>\n\n"
            . "📋 <b>Invoice:</b> {$sale->invoice_no}\n"
            . "👤 <b>Customer:</b> {$sale->user?->name} ({$sale->user?->email})\n"
            . "💳 <b>Payment:</b> Cash on Delivery\n"
            . "🟡 <b>Payment Status:</b> Pending\n"
            . "📦 <b>Delivery Status:</b> Preparing\n\n"
            . "🛍️ <b>Items:</b>\n{$items}\n\n"
            . "💰 <b>Total:</b> \${$sale->total_amount}\n\n"
            . "🔗 <a href=\"{$adminUrl}\">View in Admin Panel</a>";

        return $this->sendMessage($message);
    }

    // ── New KHQR order placed ─────────────────────────────────────────────
    public function notifyNewKhqrOrder(\App\Models\Sale $sale): bool
    {
        $sale->loadMissing('items.product', 'user');

        $items = $sale->items->map(function ($item) {
            $name = $item->product?->name ?? 'Unknown';
            return "  • {$name} × {$item->quantity} = \${$item->total}";
        })->join("\n");

        $adminUrl = url("/admin/sales/{$sale->id}");

        $message = "✅ <b>New KHQR Order — Payment Received!</b>\n\n"
            . "📋 <b>Invoice:</b> {$sale->invoice_no}\n"
            . "👤 <b>Customer:</b> {$sale->user?->name} ({$sale->user?->email})\n"
            . "💳 <b>Payment:</b> KHQR Bakong\n"
            . "🟢 <b>Payment Status:</b> Paid\n"
            . "📦 <b>Delivery Status:</b> Preparing\n\n"
            . "🛍️ <b>Items:</b>\n{$items}\n\n"
            . "💰 <b>Total:</b> \${$sale->total_amount}\n\n"
            . "🔗 <a href=\"{$adminUrl}\">View in Admin Panel</a>";

        return $this->sendMessage($message);
    }

    // ── Payment status updated (COD) ──────────────────────────────────────
    public function notifyStatusUpdate(\App\Models\Sale $sale, string $updatedByName): bool
    {
        $statusEmoji = match($sale->status) {
            'delivering' => '🚚',
            'paid'       => '✅',
            'cancelled'  => '❌',
            default      => '🟡',
        };

        $statusLabel = match($sale->status) {
            'delivering' => 'Out for Delivery',
            'paid'       => 'Payment Confirmed',
            'cancelled'  => 'Cancelled',
            default      => 'Pending',
        };

        $message = "{$statusEmoji} <b>Payment Status Updated</b>\n\n"
            . "📋 <b>Invoice:</b> {$sale->invoice_no}\n"
            . "👤 <b>Customer:</b> {$sale->user?->name}\n"
            . "💳 <b>New Payment Status:</b> {$statusLabel}\n"
            . "💰 <b>Total:</b> \${$sale->total_amount}\n"
            . "🔧 <b>Updated by:</b> {$updatedByName}\n";

        if ($sale->payment_reference) {
            $message .= "🧾 <b>Reference:</b> {$sale->payment_reference}\n";
        }

        return $this->sendMessage($message);
    }

    // ── Delivery status updated (ALL orders) ──────────────────────────────
    public function notifyDeliveryUpdate(\App\Models\Sale $sale, string $updatedByName): bool
    {
        $emoji = match($sale->delivery_status) {
            'delivering' => '🚚',
            'delivered'  => '📬',
            default      => '📦',
        };

        $label = match($sale->delivery_status) {
            'delivering' => 'Out for Delivery',
            'delivered'  => 'Delivered to Customer',
            default      => 'Preparing',
        };

        $message = "{$emoji} <b>Delivery Status Updated</b>\n\n"
            . "📋 <b>Invoice:</b> {$sale->invoice_no}\n"
            . "👤 <b>Customer:</b> {$sale->user?->name}\n"
            . "🚚 <b>New Delivery Status:</b> {$label}\n"
            . "💰 <b>Total:</b> \${$sale->total_amount}\n"
            . "🔧 <b>Updated by:</b> {$updatedByName}\n";

        return $this->sendMessage($message);
    }

    // ── End of day closing report ─────────────────────────────────────────
    public function notifyClosingReport(array $data): bool
    {
        $message = "📊 <b>End of Day Report</b>\n\n"
            . "📅 <b>Date:</b> {$data['date']}\n"
            . "🛒 <b>Total Sales:</b> {$data['total_sales']}\n"
            . "💰 <b>Revenue:</b> \${$data['total_revenue']}\n"
            . "💸 <b>Expenses:</b> \${$data['total_expenses']}\n"
            . "📈 <b>Net Profit:</b> \${$data['net_profit']}\n";

        return $this->sendMessage($message);
    }

    // ── Low stock alert ───────────────────────────────────────────────────
    public function notifyLowStock(string $productName, int $stock): bool
    {
        $message = "⚠️ <b>Low Stock Alert</b>\n\n"
            . "📦 <b>Product:</b> {$productName}\n"
            . "🔢 <b>Remaining:</b> {$stock} units\n"
            . "🔗 <a href=\"" . url('/admin/products') . "\">Manage Products</a>";

        return $this->sendMessage($message);
    }

    // ── Bakong token renewal ──────────────────────────────────────────────
    public function notifyTokenRenewal(string $status, string $message = ''): bool
    {
        $emoji = $status === 'success' ? '✅' : '⚠️';
        $text  = "{$emoji} <b>Bakong Token Renewal</b>\n\n{$message}";
        return $this->sendMessage($text);
    }
}