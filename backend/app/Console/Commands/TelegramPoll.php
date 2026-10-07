<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';

    protected $description = 'Listen for Confirm / Reject button presses from the Telegram bot (keep this running)';

    public function handle(TelegramService $tg): int
    {
        if (! $tg->isConfigured()) {
            $this->error('Set TELEGRAM_BOT_TOKEN and TELEGRAM_ADMIN_CHAT_ID in .env, then run: php artisan config:clear');
            return self::FAILURE;
        }

        $this->info('Listening for Telegram button presses. Press Ctrl+C to stop.');
        $offset = 0;

        while (true) {
            $res = $tg->getUpdates($offset);

            if (! $res || empty($res['ok'])) {
                sleep(3);
                continue;
            }

            foreach ($res['result'] as $update) {
                $offset = $update['update_id'] + 1;

                if (isset($update['callback_query'])) {
                    $this->handleCallback($tg, $update['callback_query']);
                }
            }
        }
    }

    private function handleCallback(TelegramService $tg, array $cb): void
    {
        $callbackId = (string) $cb['id'];

        // Only the shop owner may press the buttons
        if ((string) ($cb['from']['id'] ?? '') !== $tg->adminChatId()) {
            $tg->answerCallback($callbackId, 'Not allowed');
            return;
        }

        [$action, $code] = array_pad(explode(':', (string) ($cb['data'] ?? ''), 2), 2, null);
        $order = $code ? Order::where('order_code', $code)->first() : null;

        if (! $order) {
            $tg->answerCallback($callbackId, 'Order not found');
            return;
        }

        if (in_array($order->status, ['paid', 'completed', 'rejected'], true)) {
            $tg->answerCallback($callbackId, 'Already ' . $order->status);
            return;
        }

        $chatId    = (string) ($cb['message']['chat']['id'] ?? $tg->adminChatId());
        $messageId = (string) ($cb['message']['message_id'] ?? $order->telegram_message_id);
        $time      = now()->format('d/m/Y H:i');

        if ($action === 'c') {
            $order->update(['status' => 'paid', 'paid_at' => now()]);
            $tg->answerCallback($callbackId, 'Confirmed');
            $tg->editMessage($chatId, $messageId, $tg->orderText($order, "\n✅ <b>CONFIRMED</b> at {$time}\nNow deliver the diamonds to the player."));
            $this->info("Confirmed {$order->order_code}");
        } elseif ($action === 'r') {
            $order->update(['status' => 'rejected']);
            $tg->answerCallback($callbackId, 'Rejected');
            $tg->editMessage($chatId, $messageId, $tg->orderText($order, "\n❌ <b>REJECTED</b> at {$time}"));
            $this->info("Rejected {$order->order_code}");
        }
    }
}