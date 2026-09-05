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
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        // Only fetch medicines that are in the cart
        if (empty($cart)) {
            $medicines = collect([]);
        } else {
            $medicines = Medicine::whereIn('id', array_keys($cart))
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($med) {
                    $disc = $med->mrp > 0 ? round((($med->mrp - $med->price) / $med->mrp) * 100) : 0;
                    return (object) [
                        'id' => $med->id,
                        'name' => $med->name,
                        'category' => $med->category,
                        'composition' => $med->composition ?? 'Strip of tablets',
                        'emoji' => $med->emoji,
                        'price' => (float)$med->price,
                        'mrp' => (float)$med->mrp,
                        'disc' => $disc,
                        'images' => $med->images
                    ];
                });
        }

        return view('customer.smartcart', [
            'medicines' => $medicines,
            'cart' => $cart,
            'cartCount' => $cartCount
        ]);
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
