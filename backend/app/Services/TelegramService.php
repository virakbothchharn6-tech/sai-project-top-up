<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Small wrapper around the Telegram Bot API.
 * Used to notify the shop owner about orders and to receive the Confirm / Reject button presses.
 */
class TelegramService
{
    private const GAME_NAMES = [
        'mlbb' => 'Mobile Legends',
        'ff'   => 'Free Fire',
        'hok'  => 'Honor of Kings',
    ];

    public function adminChatId(): string
    {
        return (string) config('services.telegram.admin_chat_id');
    }

    public function isConfigured(): bool
    {
        return config('services.telegram.bot_token') && $this->adminChatId() !== '';
    }

    /** Call any Bot API method. Returns the decoded JSON or null on failure. */
    private function api(string $method, array $params = [], int $timeout = 15): ?array
    {
        $token = config('services.telegram.bot_token');
        if (! $token) {
            return null;
        }

        try {
            return Http::timeout($timeout)
                ->post("https://api.telegram.org/bot{$token}/{$method}", $params)
                ->json();
        } catch (\Throwable $e) {
            // Do not log the message: it contains the URL, and the URL contains the bot token.
            Log::warning('Telegram request failed: ' . $method . ' (' . get_class($e) . ')');
            return null;
        }
    }

    /** The text shown to the admin for an order. */
    public function orderText(Order $order, ?string $footer = null): string
    {
        $e    = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $game = self::GAME_NAMES[$order->game_id] ?? $order->game_id;

        $text  = "🔔 <b>Customer says: I HAVE PAID</b>\n\n";
        $text .= "📦 Order: <code>{$e($order->order_code)}</code>\n";
        $text .= "🎮 Game: {$e($game)}\n";
        $text .= "👤 Player: {$e($order->username ?: '-')}\n";
        $text .= "🆔 ID: <code>{$e($order->player_id)}</code>" . ($order->server_id ? " ({$e($order->server_id)})" : '') . "\n";
        $text .= "💎 Package: {$e($order->package_name)}\n";
        $text .= "💵 Amount: <b>$" . number_format((float) $order->price, 2) . "</b>\n";

        $text .= $footer ?? "\nCheck your bank app first. Press Confirm ONLY if this exact amount arrived.";

        return $text;
    }

    /** Send the order to the admin with Confirm / Reject buttons. Returns the message id or null. */
    public function sendOrderForConfirmation(Order $order): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $res = $this->api('sendMessage', [
            'chat_id'      => $this->adminChatId(),
            'text'         => $this->orderText($order),
            'parse_mode'   => 'HTML',
            'reply_markup' => ['inline_keyboard' => [[
                ['text' => '✅ Confirm $' . number_format((float) $order->price, 2), 'callback_data' => 'c:' . $order->order_code],
                ['text' => '❌ Reject', 'callback_data' => 'r:' . $order->order_code],
            ]]],
        ]);

        return isset($res['result']['message_id']) ? (string) $res['result']['message_id'] : null;
    }

    /** Replace the text of an existing message and remove its buttons. */
    public function editMessage(string $chatId, string $messageId, string $text): void
    {
        $this->api('editMessageText', [
            'chat_id'      => $chatId,
            'message_id'   => (int) $messageId,
            'text'         => $text,
            'parse_mode'   => 'HTML',
            'reply_markup' => ['inline_keyboard' => []],
        ]);
    }

    /** Small popup shown on the admin's screen after pressing a button. */
    public function answerCallback(string $callbackId, string $text): void
    {
        $this->api('answerCallbackQuery', ['callback_query_id' => $callbackId, 'text' => $text]);
    }

    /** Long-poll for new updates (button presses). */
    public function getUpdates(int $offset): ?array
    {
        return $this->api('getUpdates', [
            'offset'          => $offset,
            'timeout'         => 25,
            'allowed_updates' => ['callback_query'],
        ], 35);
    }
}