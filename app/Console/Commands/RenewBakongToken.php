<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RenewBakongToken extends Command
{
    protected $signature   = 'bakong:renew-token {--force : Force renew even if not expiring soon}';
    protected $description = 'Auto-renew Bakong JWT token before it expires';

    public function handle(): void
    {
        $this->info('🔍 Checking Bakong token expiry...');

        // ── Read directly from .env file (bypass config cache) ───────
        $currentToken = $this->readEnvValue('BAKONG_TOKEN');

        if (empty($currentToken)) {
            $this->error('❌ BAKONG_TOKEN is not set in .env');
            $this->sendTelegram('❌ *Bakong Token Renew FAILED*' . "\n" . 'BAKONG_TOKEN is not set in .env');
            return;
        }

        $this->info('✅ Token found. Checking expiry...');

        // ── Decode JWT to check expiry ────────────────────────────────
        $parts = explode('.', $currentToken);

        if (count($parts) !== 3) {
            $this->error('❌ BAKONG_TOKEN does not look like a valid JWT');
            $this->sendTelegram('❌ *Bakong Token Renew FAILED*' . "\n" . 'Token is not a valid JWT format.');
            return;
        }

        $base64  = strtr($parts[1], '-_', '+/');
        $padded  = str_pad($base64, strlen($base64) + (4 - strlen($base64) % 4) % 4, '=');
        $payload = json_decode(base64_decode($padded), true);

        if (!isset($payload['exp'])) {
            $this->warn('⚠️  Cannot read expiry from token. Forcing renewal...');
        } else {
            $expiresAt = $payload['exp'];
            $now       = time();
            $daysLeft  = round(($expiresAt - $now) / 86400, 1);

            $this->info("📅 Token expires in: {$daysLeft} days");

            if ($daysLeft > 7 && !$this->option('force')) {
                $this->info("✅ Token still valid for {$daysLeft} days. No renewal needed.");
                return;
            }

            $this->info("⚠️  Token expiring in {$daysLeft} days. Renewing now...");
        }

        // ── Get email from .env ───────────────────────────────────────
        $email = $this->readEnvValue('BAKONG_EMAIL');

        if (empty($email)) {
            $this->error('❌ BAKONG_EMAIL is not set in .env');
            $this->sendTelegram('❌ *Bakong Token Renew FAILED*' . "\n" . 'BAKONG_EMAIL is not set in .env');
            return;
        }

        $this->info("📧 Using email: {$email}");

        // ── Call Bakong renew API ─────────────────────────────────────
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->post('https://api-bakong.nbc.gov.kh/v1/renew_token', [
                    'email' => $email,
                ]);

            if (!$response->successful()) {
                throw new \Exception('HTTP ' . $response->status() . ': ' . $response->body());
            }

            $data = $response->json();

            if (empty($data['data']['token'])) {
                throw new \Exception('No token in response: ' . json_encode($data));
            }

            $newToken = $data['data']['token'];

            $this->updateEnvValue('BAKONG_TOKEN', $newToken);

            $this->info('✅ Bakong token renewed and .env updated successfully!');

            $this->sendTelegram(
                '✅ *Bakong Token Renewed Successfully*' . "\n" .
                '🏪 Sreynoy Mart' . "\n" .
                '📅 Renewed: ' . now('Asia/Phnom_Penh')->format('d M Y H:i') . ' (PNH)' . "\n" .
                '🔁 Next auto-check: tomorrow 8:00 AM'
            );

        } catch (\Exception $e) {
            $this->error('❌ Renewal failed: ' . $e->getMessage());
            $this->sendTelegram(
                '❌ *Bakong Token Renew FAILED*' . "\n" .
                '🏪 Sreynoy Mart' . "\n" .
                '⚠️ Error: ' . $e->getMessage() . "\n" .
                '👉 Please renew manually!'
            );
        }
    }

    // ── Read value directly from .env file (not from config cache) ───
    private function readEnvValue(string $key): ?string
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return null;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;

            if (str_starts_with($line, $key . '=')) {
                $value = substr($line, strlen($key) + 1);
                return trim($value, '"\'');
            }
        }

        return null;
    }

    // ── Update value in .env file ─────────────────────────────────────
    private function updateEnvValue(string $key, string $newValue): void
    {
        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, $key . '=')) {
            $envContent = preg_replace(
                '/^' . preg_quote($key, '/') . '=.*/m',
                $key . '=' . $newValue,
                $envContent
            );
        } else {
            $envContent .= "\n" . $key . '=' . $newValue;
        }

        file_put_contents($envPath, $envContent);
        \Artisan::call('config:clear');

        $this->info('📝 .env updated with new token.');
    }

    // ── Send Telegram notification ────────────────────────────────────
    private function sendTelegram(string $message): void
    {
        $token  = $this->readEnvValue('TELEGRAM_BOT_TOKEN');
        $chatId = $this->readEnvValue('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) return;

        try {
            Http::withoutVerifying()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'Markdown',
                ]);
        } catch (\Exception $e) {
            $this->warn('⚠️  Telegram notification failed: ' . $e->getMessage());
        }
    }
}