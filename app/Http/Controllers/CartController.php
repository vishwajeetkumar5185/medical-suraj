<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Shop;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        $page = (int) $request->input('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $catalogQuery = Medicine::query();
        if ($query) {
            $catalogQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            });
        }
        
        // Fetch items having image_urls first directly in SQL
        $catalog = $catalogQuery->orderByRaw('CASE WHEN image_urls IS NOT NULL AND image_urls != "" THEN 0 ELSE 1 END')
            ->orderBy('name', 'asc')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($med) {
                $disc = $med->mrp > 0 ? round((($med->mrp - $med->price) / $med->mrp) * 100) : 0;
                return (object) [
                    'id' => $med->id,
                    'name' => $med->name,
                    'category' => $med->category,
                    'emoji' => $med->emoji,
                    'price' => (float)$med->price,
                    'mrp' => (float)$med->mrp,
                    'disc' => $disc,
                    'images' => $med->images
                ];
            });

        if ($request->ajax()) {
            $html = view('customer.smartcart_items_inner', compact('catalog', 'cart'))->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $catalog->count() === $perPage
            ])
            ->header('Vary', 'X-Requested-With')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        }

        return view('customer.smartcart', compact('catalog', 'cart', 'cartCount', 'query'));
    }

    public function add(Request $request)
    {
        $request->validate(['medicine_id' => 'required|integer']);
        $medId = $request->medicine_id;
        
        $cart = session('cart', []);
        $cart[$medId] = ($cart[$medId] ?? 0) + 1;
        session(['cart' => $cart]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cartCount' => array_sum($cart),
                'qty' => $cart[$medId]
            ]);
        }
        return redirect()->back()->with('success', 'Medicine added to cart!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|integer',
            'qty' => 'required|integer|min:0'
        ]);
        $medId = $request->medicine_id;
        $qty = $request->qty;

        $cart = session('cart', []);
        if ($qty == 0) {
            unset($cart[$medId]);
        } else {
            $cart[$medId] = $qty;
        }
        session(['cart' => $cart]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cartCount' => array_sum($cart),
                'qty' => $qty
            ]);
        }
        return redirect()->back();
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));

        \App\Models\Coupon::checkTable();
        $coupon = \App\Models\Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code!']);
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty!']);
        }

        $cartItems = Medicine::whereIn('id', array_keys($cart))->get();
        $cartTotal = 0;
        foreach ($cartItems as $med) {
            $cartTotal += (float)$med->price * ($cart[$med->id] ?? 1);
        }

        if ($coupon->min_order_amount > 0 && $cartTotal < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum bill amount ₹" . number_format($coupon->min_order_amount, 2) . " required for code {$code}!"
            ]);
        }

        $discount = 0;
        if ($coupon->type === 'flat') {
            $discount = (float)$coupon->value;
        } else {
            $discount = round(($cartTotal * (float)$coupon->value) / 100, 2);
        }

        $discount = min($discount, $cartTotal);

        session(['applied_coupon' => [
            'code' => $coupon->code,
            'discount' => $discount
        ]]);

        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'message' => "Coupon '{$coupon->code}' applied! Saved ₹" . number_format($discount, 2)
        ]);
    }

    public function results()
    {
        $cart = session('cart', []);
        $cartItems = Medicine::whereIn('id', array_keys($cart))->get();
        if ($cartItems->isEmpty()) {
            return redirect('/smartcart')->with('error', 'Cart is empty!');
        }

        $allShops = Shop::where('status', 'approved')->where('is_online', true)->get();
        $matches = [];

        foreach ($allShops as $shop) {
            $available = [];
            $missing = [];
            $totalPrice = 0;

            foreach ($cartItems as $med) {
                // Generate inv key matches
                $invKey = preg_replace('/(\s+\d+mg|\s+\d+g|\s+mg)/i', '', $med->name);
                
                // Look up in inventories relation
                $inv = $shop->inventories()
                            ->where(function($q) use ($med, $invKey) {
                                $q->where('medicine_id', $med->id)
                                  ->orWhere('name', 'like', "%{$invKey}%");
                            })->first();

                if ($inv) {
                    $mrp = $med->mrp > 0 ? (float)$med->mrp : (float)$inv->price;
                    $itemDiscountPct = ($mrp > $inv->price && $mrp > 0) ? round((($mrp - $inv->price) / $mrp) * 100) : 0;
                    $available[] = [
                        'id' => $med->id,
                        'name' => $med->name,
                        'emoji' => $med->emoji,
                        'mrp' => $mrp,
                        'shopPrice' => (float)$inv->price,
                        'itemDiscountPct' => $itemDiscountPct
                    ];
                    $totalPrice += $inv->price * $cart[$med->id];
                } else {
                    $missing[] = $med;
                }
            }

            // Compute real-time distance using geocoded session coordinates rather than static database fallbacks
            // Compute real-time distance using geocoded session coordinates or city defaults
            $uLat = session('user_lat');
            $uLng = session('user_lng');

            if (!$uLat || !$uLng) {
                $userLoc = session('user_location', '');
                if (stripos($userLoc, 'Jaipur') !== false) {
                    $uLat = 26.9124;
                    $uLng = 75.7873;
                } else if (stripos($userLoc, 'Patna') !== false) {
                    $uLat = 25.5941;
                    $uLng = 85.1376;
                } else {
                    $uLat = 26.1209;
                    $uLng = 85.3647;
                }
            }

            $realDistance = (float)($shop->distance_km ?? 99.0);
            if ($uLat && $uLng && $shop->latitude && $shop->longitude) {
                $theta = $uLng - $shop->longitude;
                $dist = sin(deg2rad($uLat)) * sin(deg2rad($shop->latitude)) + cos(deg2rad($uLat)) * cos(deg2rad($shop->latitude)) * cos(deg2rad($theta));
                $dist = acos(min(1.0, max(-1.0, $dist)));
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $realDistance = round($miles * 1.609344, 1);
            }
            $shop->distance_km = $realDistance;

            // Calculate delivery charges according to shop settings
            $deliveryCharge = 0;
            if ($shop->delivery_charge_type === 'fixed') {
                $deliveryCharge = (float)($shop->delivery_charge_fixed ?? 20);
            } else {
                $deliveryCharge = round($realDistance * ($shop->delivery_charge_per_km ?? 8));
            }

            // Calculate active offers and bill payment discount (applies if offer_min_bill is 0 or if totalPrice >= offer_min_bill)
            $discount = 0;
            if ($shop->offer_discount_pct > 0 && ($shop->offer_min_bill <= 0 || $totalPrice >= $shop->offer_min_bill)) {
                $discount = round(($totalPrice * $shop->offer_discount_pct) / 100, 2);
            }

            // Check delivery radius constraint
            $isOutOfRadius = $realDistance > ($shop->delivery_radius_km ?? 10.0);

            // ONLY push matches if the shop has at least 1 matching medicine!
            if (count($available) > 0) {
                $matches[] = [
                    'shop' => $shop,
                    'available' => $available,
                    'missing' => $missing,
                    'matchCount' => count($available),
                    'totalPrice' => $totalPrice,
                    'deliveryCharge' => $deliveryCharge,
                    'discount' => $discount,
                    'isOutOfRadius' => $isOutOfRadius,
                    'totalWithDelivery' => $totalPrice - $discount + ($shop->delivery_enabled && !$isOutOfRadius ? $deliveryCharge : 0)
                ];
            }
        }

        if (empty($matches)) {
            foreach ($allShops as $shop) {
                $available = [];
                $missing = [];
                $totalPrice = 0;

                foreach ($cartItems as $med) {
                    $missing[] = $med;
                    $mrp = $med->mrp > 0 ? (float)$med->mrp : 50;
                    $totalPrice += $mrp * $cart[$med->id];
                }

                $uLat = session('user_lat');
                $uLng = session('user_lng');
                if (!$uLat || !$uLng) {
                    $userLoc = session('user_location', '');
                    if (stripos($userLoc, 'Jaipur') !== false) {
                        $uLat = 26.9124;
                        $uLng = 75.7873;
                    } else if (stripos($userLoc, 'Patna') !== false) {
                        $uLat = 25.5941;
                        $uLng = 85.1376;
                    } else {
                        $uLat = 26.1209;
                        $uLng = 85.3647;
                    }
                }

                $realDistance = (float)($shop->distance_km ?? 99.0);
                if ($uLat && $uLng && $shop->latitude && $shop->longitude) {
                    $theta = $uLng - $shop->longitude;
                    $dist = sin(deg2rad($uLat)) * sin(deg2rad($shop->latitude)) + cos(deg2rad($uLat)) * cos(deg2rad($shop->latitude)) * cos(deg2rad($theta));
                    $dist = acos(min(1.0, max(-1.0, $dist)));
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $realDistance = round($miles * 1.609344, 1);
                }
                $shop->distance_km = $realDistance;

                $deliveryCharge = 0;
                if ($shop->delivery_enabled) {
                    if ($shop->delivery_charge_type === 'fixed') {
                        $deliveryCharge = (float)($shop->delivery_charge_fixed ?? 20);
                    } else {
                        $deliveryCharge = round($realDistance * ($shop->delivery_charge_per_km ?? 8));
                    }
                }

                $discount = 0;
                if ($shop->offer_discount_pct > 0 && ($shop->offer_min_bill <= 0 || $totalPrice >= $shop->offer_min_bill)) {
                    $discount = round(($totalPrice * $shop->offer_discount_pct) / 100, 2);
                }

                $isOutOfRadius = $realDistance > ($shop->delivery_radius_km ?? 10.0);

                $matches[] = [
                    'shop' => $shop,
                    'available' => $available,
                    'missing' => $missing,
                    'matchCount' => 0,
                    'totalPrice' => $totalPrice,
                    'deliveryCharge' => $deliveryCharge,
                    'discount' => $discount,
                    'isOutOfRadius' => $isOutOfRadius,
                    'totalWithDelivery' => $totalPrice - $discount + ($shop->delivery_enabled && !$isOutOfRadius ? $deliveryCharge : 0)
                ];
            }
        }

        // Sort: highest match count first (maximum medicines available), then closest shop distance, then price
        usort($matches, function ($a, $b) {
            if ($b['matchCount'] !== $a['matchCount']) {
                return $b['matchCount'] - $a['matchCount'];
            }
            $distA = (float)$a['shop']->distance_km;
            $distB = (float)$b['shop']->distance_km;
            if (abs($distA - $distB) > 0.001) {
                return $distA <=> $distB;
            }
            return $a['totalWithDelivery'] <=> $b['totalWithDelivery'];
        });

        // If user bought from a specific shop store URL (e.g. search?shop_id=18), prioritize it!
        $reqShopId = request('shop_id');
        $preferDelivery = request('prefer_delivery');
        $bestMatch = null;

        if ($reqShopId) {
            foreach ($matches as $idx => $m) {
                if ((int)$m['shop']->id === (int)$reqShopId) {
                    $bestMatch = $m;
                    // Remove from original list and prepend to array
                    unset($matches[$idx]);
                    array_unshift($matches, $bestMatch);
                    break;
                }
            }
        }

        // If user clicked "Find Stores Who Can Deliver", pick best shop with delivery + medicines
        if ($preferDelivery && !$bestMatch) {
            foreach ($matches as $idx => $m) {
                if ($m['shop']->delivery_enabled && !$m['isOutOfRadius'] && $m['matchCount'] > 0) {
                    $bestMatch = $m;
                    unset($matches[$idx]);
                    array_unshift($matches, $bestMatch);
                    break;
                }
            }
        }
        
        if (!$bestMatch) {
            $bestMatch = $matches[0] ?? null;
        }
        
        if (!$bestMatch) {
            return redirect('/smartcart')->with('error', 'No approved online pharmacies found near you!');
        }

        // Limit matches to maximum 5 nearest shops
        $matches = array_slice($matches, 0, 5);

        $cartCount = array_sum($cart);

        return view('customer.cart_results', compact('bestMatch', 'matches', 'cart', 'cartItems', 'cartCount'));
    }
}
