<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Generates a dynamic KHQR (Bakong, individual account)
 * and verifies payments through the Bakong Open API.
 */
class KhqrService
{
    /** Build one TLV field: tag + 2-digit length + value */
    private function tlv(string $tag, string $value): string
    {
        return $tag . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    /**
     * Merchant account template (tag 29).
     * Sub 00 = Bakong account id (required)
     * Sub 01 = account information, e.g. phone number (needed by some banks such as ACLEDA)
     * Sub 02 = acquiring bank name (needed by some banks such as ACLEDA)
     */
    private function accountTemplate(string $account): string
    {
        $tpl  = $this->tlv('00', $account);
        $info = (string) config('services.bakong.account_info');
        $bank = (string) config('services.bakong.acquiring_bank');
        if ($info !== '') {
            $tpl .= $this->tlv('01', $info);
        }
        if ($bank !== '') {
            $tpl .= $this->tlv('02', $bank);
        }
        return $tpl;
    }

    /** CRC16-CCITT-FALSE (poly 0x1021, init 0xFFFF) */
    public function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($b = 0; $b < 8; $b++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * @return array{qr:string, md5:string, expires_at:int}  expires_at is in milliseconds
     */
    public function generate(float $amount, string $billNumber, int $ttlSeconds = 300): array
    {
        $account  = (string) config('services.bakong.account_id');   // e.g. yourname@abaa
        $name     = substr(config('services.bakong.merchant_name', 'SAI TOP-UP'), 0, 25);
        $city     = substr(config('services.bakong.merchant_city', 'Phnom Penh'), 0, 15);
        $currency = config('services.bakong.currency', 'USD');

        $createdMs = (int) round(microtime(true) * 1000);
        $expiresMs = $createdMs + ($ttlSeconds * 1000);

        $amountStr = $currency === 'KHR'
            ? (string) (int) round($amount)
            : number_format($amount, 2, '.', '');

        $payload  = $this->tlv('00', '01');                              // Payload format
        $payload .= $this->tlv('01', '12');                              // Dynamic QR
        $payload .= $this->tlv('29', $this->accountTemplate($account));  // Individual account
        $payload .= $this->tlv('52', '5999');                            // Merchant category code
        $payload .= $this->tlv('53', $currency === 'KHR' ? '116' : '840');
        $payload .= $this->tlv('54', $amountStr);
        $payload .= $this->tlv('58', 'KH');
        $payload .= $this->tlv('59', $name);
        $payload .= $this->tlv('60', $city);
        $payload .= $this->tlv('62', $this->tlv('01', substr($billNumber, 0, 25))); // Bill number
        $payload .= $this->tlv('99', $this->tlv('00', (string) $createdMs) . $this->tlv('01', (string) $expiresMs));
        $payload .= '6304';
        $payload .= $this->crc16($payload);

        return [
            'qr'         => $payload,
            'md5'        => md5($payload),
            'expires_at' => $expiresMs,
        ];
    }

    /**
     * Ask Bakong whether this transaction has been paid.
     *
     * @return array|null  transaction data if paid, null if not yet paid
     */
    public function checkByMd5(string $md5): ?array
    {
        $res = Http::withToken(config('services.bakong.token'))
            ->acceptJson()
            ->timeout(15)
            ->post(rtrim(config('services.bakong.base_url'), '/') . '/v1/check_transaction_by_md5', [
                'md5' => $md5,
            ]);

        if (! $res->ok()) {
            return null;
        }

        $json = $res->json();
        if (($json['responseCode'] ?? 1) === 0 && ! empty($json['data'])) {
            return $json['data'];
        }
        return null;
    }
}