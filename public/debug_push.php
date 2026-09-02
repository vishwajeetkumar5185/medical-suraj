<?php
// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

header('Content-Type: text/plain');

echo "=== DAWALO WEB PUSH DIAGNOSTICS ===\n\n";

// 1. Check PHP Extensions
echo "1. Checking PHP Extensions:\n";
$extensions = ['openssl', 'curl', 'json', 'mbstring', 'zip'];
foreach ($extensions as $ext) {
    echo "   - {$ext}: " . (extension_loaded($ext) ? "LOADED ✅" : "MISSING ❌") . "\n";
}
echo "\n";

// 2. Check VAPID Configs
echo "2. Checking VAPID Credentials:\n";
$pub = config('services.webpush.public_key');
$priv = config('services.webpush.private_key');
$sub = config('services.webpush.subject');

echo "   - VAPID_PUBLIC_KEY: " . ($pub ? "SET (" . strlen($pub) . " chars) ✅" : "MISSING ❌") . "\n";
echo "   - VAPID_PRIVATE_KEY: " . ($priv ? "SET (" . strlen($priv) . " chars) ✅" : "MISSING ❌") . "\n";
echo "   - VAPID_SUBJECT: " . ($sub ? "SET ({$sub}) ✅" : "MISSING ❌") . "\n";
echo "\n";

if (!$pub || !$priv || !$sub) {
    echo "❌ ERROR: VAPID keys are not fully configured in your .env or services.php config!\n";
    exit;
}

// 3. Check Database Table
echo "3. Checking Database Table 'push_subscriptions':\n";
try {
    if (\Schema::hasTable('push_subscriptions')) {
        $count = PushSubscription::count();
        echo "   - Table exists: YES ✅\n";
        echo "   - Total subscriptions saved: {$count} device(s) ✅\n";
    } else {
        echo "   - Table exists: NO ❌ (Please check if you ran the SQL DDL script!)\n";
        exit;
    }
} catch (\Exception $e) {
    echo "   - Database check error: " . $e->getMessage() . " ❌\n";
    exit;
}
echo "\n";

// 4. Send Test Notification to All Subscriptions
echo "4. Attempting to dispatch test notifications:\n";
$subscriptions = PushSubscription::all();

if ($subscriptions->isEmpty()) {
    echo "   - ❌ NO SUBSCRIBERS FOUND! Please open the dashboard in your browser and enable notifications first.\n";
    exit;
}

try {
    $auth = [
        'VAPID' => [
            'subject' => $sub,
            'publicKey' => $pub,
            'privateKey' => $priv,
        ]
    ];

    $webPush = new WebPush($auth);
    echo "   - Initializing WebPush client... SUCCESS ✅\n";

    foreach ($subscriptions as $index => $subItem) {
        echo "   - Device #" . ($index + 1) . " (User ID: {$subItem->user_id}):\n";
        echo "     * Endpoint: " . substr($subItem->endpoint, 0, 60) . "...\n";

        $webPushSubscription = Subscription::create([
            'endpoint' => $subItem->endpoint,
            'publicKey' => $subItem->public_key,
            'authToken' => $subItem->auth_token,
        ]);

        $payload = json_encode([
            'title' => 'Diagnostics Test! ⚡',
            'body' => 'Checking Web Push pipeline from live server diagnostics script.',
            'icon' => asset('assets/icon-192.png'),
            'url' => url('/shop/orders'),
        ]);

        $webPush->queueNotification($webPushSubscription, $payload);
    }

    echo "   - Flushing queue to send notifications...\n";
    $results = $webPush->flush();

    $successCount = 0;
    $failCount = 0;

    foreach ($results as $index => $result) {
        if ($result->isSuccess()) {
            echo "     * Device #" . ($index + 1) . ": SENT SUCCESS ✅\n";
            $successCount++;
        } else {
            echo "     * Device #" . ($index + 1) . ": FAILED ❌\n";
            echo "       Reason: " . $result->getReason() . "\n";
            $failCount++;
        }
    }

    echo "\n=== SUMMARY ===\n";
    echo "Total Attempted: " . ($successCount + $failCount) . "\n";
    echo "Success: {$successCount} ✅\n";
    echo "Failed: {$failCount} ❌\n";

} catch (\Exception $e) {
    echo "   - ❌ FATAL ERROR DURING DISPATCH: " . $e->getMessage() . "\n";
}
