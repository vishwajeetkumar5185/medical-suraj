<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'required|string',
        ]);

        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Pehle account login karein checkout karne ke liye.');
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect('/')->with('error', 'Cart is empty!');
        }

        // Get main shop or fallback to first available shop
        $shopId = $request->input('shop_id');
        $shop = $shopId ? Shop::find($shopId) : null;
        if (!$shop) {
            $shop = Shop::first();
        }
        if (!$shop) {
            // Create default Admin Central Pharmacy if database has no shop records
            $shop = Shop::create([
                'name' => 'Dawalo Central Pharmacy',
                'phone' => '9939717283',
                'address' => 'Central Hub, Muzaffarpur',
                'status' => 'approved',
                'is_online' => true
            ]);
        }

        $cartItems = \App\Models\Medicine::whereIn('id', array_keys($cart))->get();

        $items = [];
        $totalPrice = 0;

        foreach ($cartItems as $med) {
            $price = (float)$med->price;
            $qty = $cart[$med->id] ?? 1;

            $items[] = [
                'name' => $med->name,
                'price' => $price,
                'quantity' => $qty,
                'emoji' => $med->emoji
            ];
            $totalPrice += $price * $qty;
        }

        // Read Global Admin Settings
        $globalDeliveryCharge = (float) Setting::getVal('delivery_charge', '20');
        $minDeliveryOrder = (float) Setting::getVal('min_delivery_order', '150');
        $freeDeliveryMin = (float) Setting::getVal('free_delivery_min', '500');

        // Enforce Minimum Order Value for Home Delivery
        if ($request->mode === 'delivery' && $totalPrice < $minDeliveryOrder) {
            return redirect()->back()->with('error', "Minimum order bill ₹" . number_format($minDeliveryOrder, 2) . " required for Home Delivery. Please add more items to cart.");
        }

        $deliveryCharge = 0;
        $deliveryAddress = null;
        if ($request->mode === 'delivery') {
            $deliveryCharge = ($freeDeliveryMin > 0 && $totalPrice >= $freeDeliveryMin) ? 0 : $globalDeliveryCharge;
            $deliveryAddress = $request->address_name . ', ' 
                             . $request->address_line1 . ', ' 
                             . $request->address_line2 . ', ' 
                             . $request->address_city . ' - ' 
                             . $request->address_pincode;
        }

        $discountAmount = 0;
        $appliedCoupon = session('applied_coupon');
        if ($appliedCoupon && isset($appliedCoupon['discount'])) {
            $discountAmount = (float)$appliedCoupon['discount'];
        }

        $order = Order::create([
            'shop_id' => $shop->id,
            'status' => 'Pending',
            'mode' => $request->mode,
            'total_price' => max(0, $totalPrice - $discountAmount),
            'delivery_charge' => $deliveryCharge,
            'discount_amount' => $discountAmount,
            'delivery_address' => $deliveryAddress,
            'items' => $items,
            'user_id' => Auth::id()
        ]);

        // Update Wallet total sales & commission if commission is enabled
        $commOn = Setting::getVal('comm_on', 'true') === 'true';
        if ($commOn) {
            $commRate = (float) Setting::getVal('comm_rate', '2');
            $dueComm = ($totalPrice * $commRate) / 100;
            
            $wallet = Wallet::where('shop_id', $shop->id)->first();
            if ($wallet) {
                $wallet->total_sales += $totalPrice;
                $wallet->due_commission += $dueComm;
                if ($wallet->due_commission >= $wallet->credit_limit) {
                    $wallet->status = 'restricted';
                }
                $wallet->save();
            }
        }

        // Clear Cart & Coupon Session
        session()->forget('cart');
        session()->forget('applied_coupon');

        $customerName = Auth::user() ? Auth::user()->name : 'Guest';
        $customerPhone = Auth::user() ? Auth::user()->phone : 'N/A';

        // Send WhatsApp notification in background after response is sent to browser
        app()->terminating(function () use ($order, $shop, $items, $totalPrice, $discountAmount, $deliveryCharge, $customerName, $customerPhone) {
            try {
                $text = "🛍️ *NEW ORDER RECEIVED!*\n";
                $text .= "*Order ID:* #{$order->id}\n\n";
                $text .= "⏰ _Please accept within 2 minutes_\n";
                $text .= "──────────────────\n";
                $text .= "👤 *CUSTOMER DETAILS*\n";
                $text .= "*Name:* " . $customerName . "\n";
                $text .= "*Phone:* " . $customerPhone . "\n";
                if ($order->mode === 'delivery') {
                    $text .= "📍 *Delivery Address:*\n{$order->delivery_address}\n";
                }
                $text .= "──────────────────\n";
                $text .= "💊 *ITEMS ORDERED*\n";
                foreach ($items as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $text .= "• {$item['name']}  |  Qty: {$item['quantity']}  |  ₹{$subtotal}\n";
                }
                $text .= "──────────────────\n";
                $text .= "💰 *PAYMENT DETAILS*\n";
                $text .= "💵 *Items Total:* ₹" . number_format($totalPrice, 2) . "\n";
                if ($discountAmount > 0) {
                    $text .= "🏷️ *Discount:* -₹" . number_format($discountAmount, 2) . "\n";
                }
                if ($deliveryCharge > 0) {
                    $text .= "🛵 *Delivery Charge:* +₹" . number_format($deliveryCharge, 2) . "\n";
                }
                $grandTotal = $order->total_price + $order->delivery_charge;
                $text .= "💳 *Grand Total:* *₹" . number_format($grandTotal, 2) . "*\n\n";
                $text .= "🛵 *Delivery Mode:* " . ucfirst($order->mode) . "\n";

                // Generate secure clickable links for Accept/Reject
                $secureToken = md5($order->id . $order->created_at . 'dawalo_secure_whatsapp_token_salt');
                $acceptLink = url("/order/{$order->id}/action/accept?token={$secureToken}");
                $rejectLink = url("/order/{$order->id}/action/reject?token={$secureToken}");

                $text .= "──────────────────\n";
                $text .= "⚡ *QUICK ACTIONS*\n\n";
                $text .= "✅ *Accept Order:*\n{$acceptLink}\n\n";
                $text .= "❌ *Reject Order:*\n{$rejectLink}\n\n";
                if ($order->mode === 'delivery') {
                    $text .= "📍 *Delivery Mode:* Home Delivery\n";
                }
                $text .= "👁️ *VIEW ORDER:*\n" . url("/order/{$order->id}/success") . "\n";

                $shopPhone = preg_replace('/\D/', '', $shop->phone);
                if (str_starts_with($shopPhone, '0')) {
                    $shopPhone = substr($shopPhone, 1);
                }
                if (strlen($shopPhone) === 10) {
                    $shopPhone = '91' . $shopPhone;
                }

                $adminPhone = '919939717283'; // Admin WhatsApp destination

                // Resolve session ID for session name "dawalo"
                $sessionId = '294fbef7-01e6-436c-bffb-2f2d9e396c48'; // fallback default
                try {
                    $sessionsResponse = \Illuminate\Support\Facades\Http::timeout(2)
                        ->connectTimeout(1)
                        ->withHeaders([
                            'X-API-Key' => 'owa_k1_3c7a86634a5cf9e2a4bff465c75093742029199a635159b128284cda6fccf816'
                        ])->get('http://13.60.229.175/api/sessions');

                    if ($sessionsResponse->successful()) {
                        $sessionsList = $sessionsResponse->json();
                        foreach ($sessionsList as $s) {
                            if (($s['name'] ?? '') === 'dawalo') {
                                $sessionId = $s['id'];
                                break;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to resolve WhatsApp session ID: ' . $e->getMessage());
                }

                // Send WhatsApp to Admin and Shop Phone
                $recipients = array_unique([$adminPhone . '@c.us', $shopPhone . '@c.us']);
                foreach ($recipients as $chatId) {
                    try {
                        \Illuminate\Support\Facades\Http::timeout(4)
                            ->connectTimeout(2)
                            ->withHeaders([
                                'X-API-Key' => 'owa_k1_3c7a86634a5cf9e2a4bff465c75093742029199a635159b128284cda6fccf816',
                                'Content-Type' => 'application/json'
                            ])->post("http://13.60.229.175/api/sessions/{$sessionId}/messages/send-text", [
                                'chatId' => $chatId,
                                'text' => $text
                            ]);
                    } catch (\Exception $e) {
                        \Log::error("WhatsApp API failed to send to {$chatId}: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                \Log::error('WhatsApp notification failed due to error: ' . $e->getMessage());
            }

            // Send Web Push notification to Shop Owner AND Admin users
            try {
                $recipients = \App\Models\User::where('role', 'admin')
                    ->orWhere('id', $shop->user_id)
                    ->with('pushSubscriptions')
                    ->get();

                $auth = [
                    'VAPID' => [
                        'subject' => config('services.webpush.subject'),
                        'publicKey' => config('services.webpush.public_key'),
                        'privateKey' => config('services.webpush.private_key'),
                    ]
                ];

                $webPush = new \Minishlink\WebPush\WebPush($auth);
                $hasSubs = false;

                foreach ($recipients as $recipientUser) {
                    if ($recipientUser->pushSubscriptions->isNotEmpty()) {
                        foreach ($recipientUser->pushSubscriptions as $sub) {
                            $hasSubs = true;
                            $targetUrl = $recipientUser->role === 'admin' ? url('/admin/orders') : url('/shop/orders');
                            $webPushSubscription = \Minishlink\WebPush\Subscription::create([
                                'endpoint' => $sub->endpoint,
                                'publicKey' => $sub->public_key,
                                'authToken' => $sub->auth_token,
                            ]);

                            $payload = json_encode([
                                'title' => 'New Order Received! 📦',
                                'body' => "Order #{$order->id} from " . $customerName . " for ₹" . number_format($order->total_price, 2),
                                'icon' => asset('assets/icon-192.png'),
                                'badge' => asset('assets/favicon.png'),
                                'url' => $targetUrl,
                                'orderId' => $order->id,
                            ]);

                            $webPush->queueNotification($webPushSubscription, $payload);
                        }
                    }
                }

                if ($hasSubs) {
                    $results = $webPush->flush();
                    foreach ($results as $result) {
                        if (!$result->isSuccess()) {
                            try {
                                $expiredEndpoint = $result->getEndpoint();
                                \App\Models\PushSubscription::where('endpoint', $expiredEndpoint)->delete();
                            } catch (\Throwable $cleanupErr) {}
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Web Push notification failed: ' . $e->getMessage());
            }
        });

        return redirect('/order/' . $order->id . '/success')->with('success', 'Order placed successfully!');
    }

    public function success($id)
    {
        $order = Order::findOrFail($id);
        
        // Security check: Only the customer who placed the order (or shop owner, or admin) can view the success receipt
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Pehle account login karein.');
        }

        $user = Auth::user();
        if ($order->user_id !== $user->id && $order->shop_id !== $user->shop?->id && $user->role !== 'admin') {
            abort(403, 'Aapko is order ko dekhne ki permission nahi hai.');
        }

        $order->shop = Shop::findOrFail($order->shop_id);
        
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.order_success', compact('order', 'cartCount'));
    }

    public function getStatus($id)
    {
        $order = Order::findOrFail($id);
        
        // Security check: Only the customer who placed the order (or shop owner, or admin) can view status
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        $user = Auth::user();
        if ($order->user_id !== $user->id && $order->shop_id !== $user->shop?->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
        return response()->json([
            'success' => true,
            'status' => $order->status,
        ]);
    }

    public function whatsappAction($id, $action, Request $request)
    {
        $order = Order::findOrFail($id);

        // Security check: Validate the secure token against the order hash
        $expectedToken = md5($order->id . $order->created_at . 'dawalo_secure_whatsapp_token_salt');
        if ($request->query('token') !== $expectedToken) {
            abort(403, 'Unauthorized link or invalid token!');
        }

        // Action routing logic
        if ($action === 'accept') {
            $order->status = 'Accepted';
            $statusText = 'Accepted ✅';
            $color = '#10B981';
        } elseif ($action === 'reject') {
            $order->status = 'Rejected';
            $statusText = 'Rejected ❌';
            $color = '#EF4444';
        } else {
            abort(400, 'Invalid action!');
        }

        $order->save();

        // Render premium responsive HTML response matching app theme aesthetics
        $html = "
        <!DOCTYPE html>
        <html lang='hi'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Order {$statusText}</title>
            <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap' rel='stylesheet'>
            <style>
                body {
                    background-color: #F0F4FF;
                    font-family: 'Outfit', sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .card {
                    background: #ffffff;
                    padding: 40px 30px;
                    border-radius: 24px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
                    text-align: center;
                    max-width: 400px;
                    width: 90%;
                    border-top: 8px solid {$color};
                }
                .icon {
                    font-size: 64px;
                    margin-bottom: 20px;
                }
                h1 {
                    font-size: 24px;
                    font-weight: 900;
                    color: #1A1A1A;
                    margin: 0 0 10px;
                }
                p {
                    color: #666;
                    font-size: 15px;
                    margin: 0 0 24px;
                    line-height: 1.5;
                }
                .btn {
                    display: inline-block;
                    background: linear-gradient(135deg,#1A3C8F,#2563EB);
                    color: #ffffff;
                    text-decoration: none;
                    font-weight: 700;
                    font-size: 14.5px;
                    padding: 12px 24px;
                    border-radius: 12px;
                    box-shadow: 0 4px 14px rgba(37,99,235,0.25);
                    transition: transform 0.2s;
                }
                .btn:hover {
                    transform: scale(1.05);
                }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='icon'>" . ($action === 'accept' ? '🎉' : '⚠️') . "</div>
                <h1>Order {$statusText}</h1>
                <p>Order #{$order->id} status has been updated in the database. You can close this browser tab now.</p>
                <a href='" . url('/') . "' class='btn'>Dawalo Home Search</a>
            </div>
        </body>
        </html>
        ";
        return response($html);
    }

    public function whatsappWebhook(Request $request)
    {
        // 1. Log the full payload for debugging and verification
        \Log::info('WhatsApp Webhook received', ['payload' => $request->all()]);

        // If it's a GET test request, return success
        if ($request->isMethod('get')) {
            return response()->json(['status' => 'active', 'message' => 'Dawalo WhatsApp Webhook is active and reachable!']);
        }

        // 2. Parse incoming message body and sender from WeGrow event payload
        $body = $request->input('payload.body');
        $from = $request->input('payload.from'); // e.g. "919939717283@c.us"

        // Fallback structures
        if (empty($body)) {
            $body = $request->input('body');
        }
        if (empty($from)) {
            $from = $request->input('from');
        }

        if (empty($body) || empty($from)) {
            return response()->json(['status' => 'skipped', 'message' => 'Empty body or sender']);
        }

        $body = trim($body);

        // 3. Match patterns: "A<orderId>" or "R<orderId>" (case-insensitive)
        if (preg_match('/^([AR])(\d+)$/i', $body, $matches)) {
            $actionLetter = strtoupper($matches[1]);
            $orderId = intval($matches[2]);

            // 4. Find the order
            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => "Order #{$orderId} not found"]);
            }

            // 5. Verify the sender's phone matches the order's shop phone
            $shop = Shop::find($order->shop_id);
            if ($shop) {
                $shopPhoneClean = preg_replace('/\D/', '', $shop->phone);
                if (str_starts_with($shopPhoneClean, '0')) {
                    $shopPhoneClean = substr($shopPhoneClean, 1);
                }
                $senderPhoneClean = preg_replace('/\D/', '', $from);

                // Compare last 10 digits to be 100% safe
                if (substr($shopPhoneClean, -10) !== substr($senderPhoneClean, -10)) {
                    \Log::warning('WhatsApp Webhook sender mismatch', [
                        'orderId' => $orderId,
                        'expectedShopPhone' => $shopPhoneClean,
                        'actualSenderPhone' => $senderPhoneClean
                    ]);
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized sender for this order']);
                }
            }

            // 6. Apply status changes
            if ($actionLetter === 'A') {
                $order->status = 'Accepted';
                $replyMessage = "Order #{$orderId} has been accepted successfully! ✅";
            } else {
                $order->status = 'Rejected';
                $replyMessage = "Order #{$orderId} has been rejected! ❌";
            }
            $order->save();

            // 7. Send confirmation reply back to the shop owner via WhatsApp API
            $sessionId = '294fbef7-01e6-436c-bffb-2f2d9e396c48'; // default fallback
            try {
                $sessionsResponse = \Illuminate\Support\Facades\Http::timeout(3)
                    ->withHeaders([
                        'X-API-Key' => 'owa_k1_3c7a86634a5cf9e2a4bff465c75093742029199a635159b128284cda6fccf816'
                    ])->get('http://13.60.229.175/api/sessions');

                if ($sessionsResponse->successful()) {
                    foreach ($sessionsResponse->json() as $s) {
                        if (($s['name'] ?? '') === 'dawalo') {
                            $sessionId = $s['id'];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to resolve WhatsApp session ID in webhook: ' . $e->getMessage());
            }

            try {
                \Illuminate\Support\Facades\Http::timeout(10)
                    ->withHeaders([
                        'X-API-Key' => 'owa_k1_3c7a86634a5cf9e2a4bff465c75093742029199a635159b128284cda6fccf816',
                        'Content-Type' => 'application/json'
                    ])->post("http://13.60.229.175/api/sessions/{$sessionId}/messages/send-text", [
                        'chatId' => $from,
                        'text' => $replyMessage
                    ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send webhook reply to WhatsApp: ' . $e->getMessage());
            }

            return response()->json(['status' => 'success', 'message' => "Order #{$orderId} status updated"]);
        }

        return response()->json(['status' => 'ignored', 'message' => 'Not a reply code command']);
    }
}
