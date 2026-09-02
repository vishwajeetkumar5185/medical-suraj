<?php
/**
 * DAWALO Push Notification Full Diagnostics v3
 * Upload to: public/debug_push2.php
 * DELETE after debugging!
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

header('Content-Type: text/plain; charset=utf-8');
echo "=== DAWALO PUSH DIAGNOSTICS v3 ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// 1. VAPID Config
echo "--- 1. VAPID Config ---\n";
$pub  = config('services.webpush.public_key');
$priv = config('services.webpush.private_key');
$subj = config('services.webpush.subject');
echo "Public Key  : " . ($pub  ? substr($pub, 0, 30) . "... (" . strlen($pub) . " chars) ✅" : "MISSING ❌") . "\n";
echo "Private Key : " . ($priv ? substr($priv, 0, 20) . "... (" . strlen($priv) . " chars) ✅" : "MISSING ❌") . "\n";
echo "Subject     : " . ($subj ? "$subj ✅" : "MISSING ❌") . "\n\n";

// 2. Subscriptions
echo "--- 2. Push Subscriptions in Database ---\n";
$subs = PushSubscription::with('user')->get();
echo "Total subscriptions: " . $subs->count() . "\n";

if ($subs->isEmpty()) {
    echo "❌ NO SUBSCRIPTIONS FOUND!\n\n";
} else {
    foreach ($subs as $i => $sub) {
        echo "  #" . ($i+1) . " user_id=" . $sub->user_id . " (" . ($sub->user ? $sub->user->name : 'NULL') . ") endpoint=" . substr($sub->endpoint, 0, 50) . "...\n";
    }
    echo "\n";
}

// 3. Send test
echo "--- 3. Sending Test Push ---\n";
if ($subs->isEmpty() || !$pub || !$priv) {
    echo "SKIPPED\n";
    exit;
}

$auth = [
    'VAPID' => [
        'subject'    => $subj,
        'publicKey'  => $pub,
        'privateKey' => $priv,
    ]
];

try {
    $webPush = new WebPush($auth);
    echo "WebPush client OK ✅\n";

    $payload = json_encode([
        'title'   => 'Dawalo Test 🔔 v3',
        'body'    => 'Test notification at ' . date('H:i:s') . ' - If you see this, push is working!',
        'icon'    => 'https://medical.techomission.com/assets/icon-192.png',
        'badge'   => 'https://medical.techomission.com/assets/favicon.png',
        'url'     => 'https://medical.techomission.com/shop/orders',
        'orderId' => 999,
    ]);

    foreach ($subs as $sub) {
        $wSub = Subscription::create([
            'endpoint'  => $sub->endpoint,
            'publicKey' => $sub->public_key,
            'authToken' => $sub->auth_token,
        ]);
        $webPush->queueNotification($wSub, $payload);
        echo "Queued: user_id=" . $sub->user_id . "\n";
    }

    echo "\nFlushing...\n";
    $results = $webPush->flush();

    $sent = 0;
    $failed = 0;
    $failedEndpoints = [];

    foreach ($results as $result) {
        if ($result->isSuccess()) {
            $sent++;
            echo "✅ SENT OK\n";
        } else {
            $failed++;
            $reason = 'Unknown';
            // Try different API methods for getting failure info
            try { $reason = $result->getReason(); } catch (\Throwable $e) {}
            try {
                $resp = $result->getResponse();
                if ($resp) $reason .= ' | HTTP ' . $resp->getStatusCode();
            } catch (\Throwable $e) {}
            echo "❌ FAILED: $reason\n";

            // Try to get endpoint for cleanup
            try { 
                $ep = $result->getEndpoint(); 
                $failedEndpoints[] = $ep;
                echo "   Endpoint: " . substr($ep, 0, 60) . "...\n";
            } catch (\Throwable $e) {
                try {
                    $req = $result->getRequest();
                    if ($req) {
                        $ep = (string) $req->getUri();
                        $failedEndpoints[] = $ep;
                        echo "   URI: " . substr($ep, 0, 60) . "...\n";
                    }
                } catch (\Throwable $e2) {}
            }
        }
    }

    echo "\n--- SUMMARY ---\n";
    echo "Sent: $sent | Failed: $failed\n";

    if ($failed > 0 && !empty($failedEndpoints)) {
        echo "\n⚠️  Failed endpoints are likely EXPIRED subscriptions.\n";
        echo "These devices need to re-enable push notifications.\n";
    }

} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== END ===\n";
