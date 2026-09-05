<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Shop;
use App\Models\Setting;
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

        $itemsTotal = 0;
        foreach ($cartItems as $med) {
            $qty = $cart[$med->id] ?? 1;
            $itemsTotal += (float)$med->price * $qty;
        }

        // Global Delivery Charges & Minimum Order Rules from Admin Settings
        $globalDeliveryCharge = (float) Setting::getVal('delivery_charge', '20');
        $minDeliveryOrder = (float) Setting::getVal('min_delivery_order', '150');
        $freeDeliveryMin = (float) Setting::getVal('free_delivery_min', '500');

        $deliveryCharge = ($freeDeliveryMin > 0 && $itemsTotal >= $freeDeliveryMin) ? 0 : $globalDeliveryCharge;

        $discountAmount = 0;
        $appliedCoupon = session('applied_coupon');
        if ($appliedCoupon && isset($appliedCoupon['discount'])) {
            $discountAmount = (float)$appliedCoupon['discount'];
        }

        $defaultShop = Shop::first();
        if (!$defaultShop) {
            $defaultShop = Shop::create([
                'name' => 'Dawalo Central Hub',
                'phone' => '9939717283',
                'address' => 'Central Hub, Muzaffarpur',
                'status' => 'approved',
                'is_online' => true
            ]);
        }

        $cartCount = array_sum($cart);

        return view('customer.cart_results', compact(
            'cart', 'cartItems', 'cartCount', 'itemsTotal', 
            'deliveryCharge', 'discountAmount', 'defaultShop',
            'globalDeliveryCharge', 'minDeliveryOrder', 'freeDeliveryMin'
        ));
    }
}
