<?php
/**
 * WhatsApp API Diagnostic Script
 * Upload to: public/debug_whatsapp.php
 * DELETE after debugging!
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Http;

header('Content-Type: text/plain; charset=utf-8');
echo "=== DAWALO WHATSAPP API DIAGNOSTICS ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$apiKey = 'owa_k1_3c7a86634a5cf9e2a4bff465c75093742029199a635159b128284cda6fccf816';
$apiBase = 'http://13.60.229.175';

// 1. Check API Server
echo "--- 1. API Server Connectivity ---\n";
try {
    $ping = Http::timeout(5)->connectTimeout(3)
        ->withHeaders(['X-API-Key' => $apiKey])
        ->get("$apiBase/api/sessions");

    echo "HTTP Status: " . $ping->status() . "\n";

    if ($ping->successful()) {
        $sessions = $ping->json();
        echo "Sessions found: " . count($sessions) . "\n";

        $dawaloSession = null;
        foreach ($sessions as $s) {
            $name = $s['name'] ?? 'unnamed';
            $id   = $s['id'] ?? '?';
            $status = $s['status'] ?? '?';
            echo "  - Name: $name | ID: $id | Status: $status\n";
            if ($name === 'dawalo') {
                $dawaloSession = $s;
            }
        }

        if (!$dawaloSession) {
            echo "\n❌ 'dawalo' session NOT FOUND!\n";
            echo "=== END ===\n";
            exit;
        }

        $sessionId = $dawaloSession['id'];
        $sessionStatus = $dawaloSession['status'] ?? '?';
        echo "\n✅ Dawalo session found: ID=$sessionId, Status=$sessionStatus\n";

        if ($sessionStatus !== 'WORKING' && $sessionStatus !== 'CONNECTED') {
            echo "⚠️  Session is NOT in WORKING/CONNECTED state. WhatsApp might be disconnected!\n";
        }

    } else {
        echo "❌ API returned error: " . $ping->body() . "\n";
        echo "=== END ===\n";
        exit;
    }
} catch (\Exception $e) {
    echo "❌ Connection FAILED: " . $e->getMessage() . "\n";
    echo "   The WhatsApp API server at $apiBase may be DOWN.\n";
    echo "=== END ===\n";
    exit;
}

// 2. Check shop owner phone
echo "\n--- 2. Shop Owner Phone Check ---\n";
$shops = \App\Models\Shop::where('status', 'approved')->with('user')->get();
foreach ($shops as $shop) {
    $phone = $shop->phone;
    $cleanPhone = preg_replace('/\D/', '', $phone);
    if (str_starts_with($cleanPhone, '0')) {
        $cleanPhone = substr($cleanPhone, 1);
    }
    if (strlen($cleanPhone) === 10) {
        $cleanPhone = '91' . $cleanPhone;
    }
    $chatId = $cleanPhone . '@c.us';
    echo "  Shop: {$shop->name} | Raw Phone: $phone | ChatId: $chatId | Owner: " . ($shop->user ? $shop->user->name : 'NULL') . "\n";
}

// 3. Send test message to first shop
echo "\n--- 3. Sending Test Message ---\n";
$firstShop = $shops->first();
if (!$firstShop) {
    echo "❌ No approved shops found!\n";
    echo "=== END ===\n";
    exit;
}

$testPhone = '919549224496';
$testChatId = $testPhone . '@c.us';

$testText = "🔔 *DAWALO TEST MESSAGE*\n\nTime: " . date('d M Y, h:i:s A') . "\n\nYeh ek test message hai to check WhatsApp API connectivity.\n\nAgar aapko yeh message mila hai, toh WhatsApp notifications working hain! ✅";

echo "Sending to: $testChatId (Shop: {$firstShop->name})\n";
echo "Session ID: $sessionId\n";
echo "API URL: $apiBase/api/sessions/$sessionId/messages/send-text\n\n";

try {
    $response = Http::timeout(10)
        ->connectTimeout(5)
        ->withHeaders([
            'X-API-Key' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post("$apiBase/api/sessions/$sessionId/messages/send-text", [
            'chatId' => $testChatId,
            'text' => $testText
        ]);

    echo "HTTP Status: " . $response->status() . "\n";
    echo "Response Body: " . $response->body() . "\n\n";

    if ($response->successful()) {
        echo "✅ TEST MESSAGE SENT SUCCESSFULLY!\n";
        echo "Check WhatsApp on phone: $testPhone\n";
    } else {
        echo "❌ SEND FAILED!\n";
        echo "Status: " . $response->status() . "\n";
        echo "Error: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== END ===\n";
