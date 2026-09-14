# Session 6: KHQR Payment Integration & ABA PayWay Roadmap

## Overview
Integrate local payment solutions, starting with **Bakong KHQR** dynamic QR generation for a demo checkout, and preparing the architecture for a future **ABA PayWay** API integration.

## Objectives
- Generate a dynamic **KHQR** code for each transaction.
- Build order status polling (Pending → Success).
- Document the upcoming **ABA PayWay** integration workflow.

---

## Step 1 — Install the Bakong KHQR package

```bash
docker-compose exec backend composer require bakong-khqr/khqr-sdk
```
*(Package name illustrative — use the official Bakong KHQR SDK or generate the EMV QR payload manually per the [Bakong KHQR spec](https://bakong.nbc.gov.kh).)*

## Step 2 — Build the payment controller

`app/Http/Controllers/Api/PaymentController.php`:
```php
class PaymentController extends Controller
{
    public function generateKhqr(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:top_up_packages,id',
            'game_id'    => 'required|exists:games,id',
            'user_game_id' => 'required|string',
            'user_zone_id' => 'nullable|string',
            'account_username' => 'required|string',
        ]);

        $package = TopUpPackage::findOrFail($request->package_id);
        $reference = 'SAI-' . strtoupper(uniqid());

        $transaction = Transaction::create([
            'transaction_reference' => $reference,
            'game_id' => $request->game_id,
            'user_game_id' => $request->user_game_id,
            'user_zone_id' => $request->user_zone_id,
            'account_username' => $request->account_username,
            'package_id' => $package->id,
            'payment_method' => 'khqr',
            'status' => 'pending',
        ]);

        // Demo QR payload — replace with real Bakong KHQR generation
        $qrString = "KHQR-DEMO|{$reference}|{$package->price_khr}KHR";

        return response()->json([
            'transaction_reference' => $reference,
            'qr_string' => $qrString,
            'amount_khr' => $package->price_khr,
        ]);
    }

    public function status(string $reference)
    {
        $transaction = Transaction::where('transaction_reference', $reference)->firstOrFail();
        return response()->json(['status' => $transaction->status]);
    }
}
```

## Step 3 — Register the payment routes

`routes/api.php`:
```php
use App\Http\Controllers\Api\PaymentController;

Route::post('/payment/khqr', [PaymentController::class, 'generateKhqr']);
Route::get('/payment/status/{reference}', [PaymentController::class, 'status']);
```

## Step 4 — Build the payment page on the frontend

`frontend/src/pages/Checkout.vue`:
```vue
<script setup>
import { ref, onUnmounted } from "vue";
import api from "../axios";

const qrString = ref(null);
const status = ref("pending");
let poller = null;

async function pay(payload) {
  const res = await api.post("/payment/khqr", payload);
  qrString.value = res.data.qr_string;

  poller = setInterval(async () => {
    const check = await api.get(`/payment/status/${res.data.transaction_reference}`);
    status.value = check.data.status;
    if (status.value === "success") clearInterval(poller);
  }, 3000);
}

onUnmounted(() => clearInterval(poller));
</script>

<template>
  <div v-if="qrString">
    <p>Scan this KHQR code to pay:</p>
    <pre>{{ qrString }}</pre>
    <p>Status: {{ status }}</p>
  </div>
</template>
```

## Step 5 — (Demo) Simulate a successful payment

For local testing without a live bank connection, add an artisan command or manual DB update to flip the transaction to `success`:
```bash
docker-compose exec backend php artisan tinker
>>> Transaction::where('transaction_reference', 'SAI-XXXX')->update(['status' => 'success']);
```

## 🗺️ ABA PayWay Roadmap (Planned — not yet implemented)

ABA PayWay will be added as a second payment method alongside KHQR:
1. Register a merchant account and obtain `merchant_id` + `api_key` from ABA PayWay.
2. Build `AbaPaywayController` to generate a checkout request signed with HMAC SHA-512, per ABA's [PayWay API docs](https://www.ababank.com/api-payway).
3. Add a "Pay with ABA PayWay" option next to KHQR on the Checkout page.
4. Handle ABA's server-to-server callback (`payway/callback`) to confirm payment and update `transactions.status`.
5. Store `payment_method` as `aba_payway` for these transactions to distinguish them from KHQR ones.

This will be implemented in a future session once merchant credentials are available.

## ✅ Result
The app now supports a working KHQR demo payment flow with live status polling, and the transactions table is already structured to support ABA PayWay as a second payment method.
