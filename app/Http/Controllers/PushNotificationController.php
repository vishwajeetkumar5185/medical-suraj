<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationController extends Controller
{
    /**
     * Store or update user's Web Push Subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $userId = Auth::id(); // Link to current user/shop owner if logged in

        // Clean old subscriptions with same endpoint
        PushSubscription::where('endpoint', $request->endpoint)->delete();

        // Save new subscription
        $subscription = PushSubscription::create([
            'user_id' => $userId,
            'endpoint' => $request->endpoint,
            'public_key' => $request->input('keys.p256dh'),
            'auth_token' => $request->input('keys.auth'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription saved successfully.',
            'data' => $subscription
        ]);
    }

    /**
     * Send a test push notification to the logged-in user.
     */
    public function sendTestNotification()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $subscriptions = $user->pushSubscriptions;

        if ($subscriptions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No push subscriptions found for this user.'], 404);
        }

        // Load VAPID credentials
        $auth = [
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ]
        ];

        $webPush = new WebPush($auth);
        $notificationsSent = 0;

        foreach ($subscriptions as $sub) {
            $webPushSubscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]);

            $payload = json_encode([
                'title' => 'Test Notification! 🔔',
                'body' => 'Web push notifications are working perfectly on Dawalo.',
                'icon' => asset('assets/icon-192.png'),
                'badge' => asset('assets/favicon.png'),
                'url' => url('/profile'),
                'orderId' => 999,
            ]);

            $webPush->queueNotification($webPushSubscription, $payload);
            $notificationsSent++;
        }

        // Flush and process queue
        $results = $webPush->flush();

        // Handle expired subscriptions
        foreach ($results as $result) {
            if (!$result->isSuccess()) {
                // Delete invalid subscriptions from DB
                $expiredEndpoint = $result->getSubscription()->getEndpoint();
                PushSubscription::where('endpoint', $expiredEndpoint)->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Test push sent to {$notificationsSent} device(s)."
        ]);
    }
}
