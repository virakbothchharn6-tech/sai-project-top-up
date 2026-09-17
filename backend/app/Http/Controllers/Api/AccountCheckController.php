<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccountCheckController extends Controller
{
    public function check(Request $request)
    {
        $playerId = $request->input('player_id', $request->input('user_id'));
        $serverId = $request->input('server_id', $request->input('zone_id'));

        if (!$playerId || !$serverId) {
            return response()->json([
                'success' => false,
                'message' => 'សូមបញ្ចូល Player ID និង Zone ID ឲ្យបានគ្រប់គ្រាន់!'
            ], 422);
        }

        try {
            // ហៅទៅកាន់ Endpoint API ពិតសម្រាប់ Mobile Legends
            $response = Http::timeout(8)->get("https://api.isan.eu.org/nickname/ml", [
                'id'   => $playerId,
                'zone' => $serverId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // បើស្វែងរកឃើញ Username ពិតប្រាកដ
                if (isset($data['name']) && !empty($data['name'])) {
                    return response()->json([
                        'success'   => true,
                        'username'  => $data['name'],
                        'player_id' => $playerId,
                        'server_id' => $serverId
                    ]);
                }
            }

            // សាកល្បង Gateway ទី២ បើ Gateway ទី១ រវល់
            $resBackup = Http::timeout(8)->asForm()->post("https://order-sg.codashop.com/initPayment.action", [
                'voucherTypeName' => 'MOBILE_LEGENDS',
                'voucherPricePoint.id' => '4615',
                'user.userId' => $playerId,
                'user.zoneId' => $serverId
            ]);

            if ($resBackup->successful()) {
                $backupData = $resBackup->json();
                if (!empty($backupData['confirmationFields']['username'])) {
                    return response()->json([
                        'success'   => true,
                        'username'  => urldecode($backupData['confirmationFields']['username']),
                        'player_id' => $playerId,
                        'server_id' => $serverId
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញគណនីនេះទេ! សូមពិនិត្យមើល Player ID និង Server ID ឡើងវិញ។'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ប្រព័ន្ធផ្ទៀងផ្ទាត់កំពុងរវល់ សូមព្យាយាមម្តងទៀតបន្តិចក្រោយ!'
            ], 500);
        }
    }
}