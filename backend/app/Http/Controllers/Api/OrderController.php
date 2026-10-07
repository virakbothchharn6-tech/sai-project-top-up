<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KhqrService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private const QR_TTL = 300; // seconds (5 minutes)

    public function __construct(
        private KhqrService $khqr,
        private TelegramService $telegram
    ) {
    }

    /** Create an order and its KHQR */
    public function store(Request $request)
    {
        $data = $request->validate([
            'game_id'      => 'required|string|max:50',
            'player_id'    => 'required|string|max:50',
            'server_id'    => 'nullable|string|max:100',
            'username'     => 'nullable|string|max:100',
            'package_name' => 'required|string|max:150',
            'price'        => 'required|numeric|min:0.01|max:1000',
        ]);

        $code = 'SAI' . strtoupper(Str::random(6));
        $qr   = $this->khqr->generate((float) $data['price'], $code, self::QR_TTL);

        $order = Order::create($data + [
            'order_code' => $code,
            'status'     => 'pending',
            'khqr'       => $qr['qr'],
            'khqr_md5'   => $qr['md5'],
            'expires_at' => now()->addSeconds(self::QR_TTL),
        ]);

        // ផ្ញើសារជូនដំណឹងទៅ Telegram Bot ជាមួយប៊ូតុង Confirm / Reject
        try {
            $msgId = $this->telegram->sendOrderForConfirmation($order);
            if ($msgId) {
                $order->update(['telegram_message_id' => $msgId]);
            }
        } catch (\Throwable $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'order_code'   => $order->order_code,
                'player_id'    => $order->player_id,
                'server_id'    => $order->server_id,
                'package_name' => $order->package_name,
                'price'        => $order->price,
                'qr'           => $order->khqr,
                'expires_in'   => self::QR_TTL,
            ],
        ], 201);
    }

    /** The frontend polls this every 3 seconds to see if the order has been paid */
    public function status(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        if ($order->status === 'pending') {
            $tx = $this->khqr->checkByMd5($order->khqr_md5);

            if ($tx && $this->txMatchesOrder($tx, $order)) {
                $order->update([
                    'status'      => 'paid',
                    'paid_at'     => now(),
                    'bakong_hash' => $tx['hash'] ?? null,
                ]);
            } elseif ($order->expires_at && now()->greaterThan($order->expires_at->addSeconds(10))) {
                $order->update(['status' => 'expired']);
            }
        }

        return response()->json(['success' => true, 'status' => $order->status]);
    }

    /** Make sure the paid amount and currency match the order (when Bakong returns them) */
    private function txMatchesOrder(array $tx, Order $order): bool
    {
        if (isset($tx['amount']) && abs((float) $tx['amount'] - (float) $order->price) > 0.001) {
            return false;
        }
        if (isset($tx['currency']) && strtoupper($tx['currency']) !== config('services.bakong.currency', 'USD')) {
            return false;
        }
        // The same Bakong transaction must not be used for two different orders
        if (! empty($tx['hash']) && Order::where('bakong_hash', $tx['hash'])->where('id', '!=', $order->id)->exists()) {
            return false;
        }
        return true;
    }
}