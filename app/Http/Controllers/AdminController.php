<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Medicine;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $shopsCount = Shop::count();
        $approvedShopsCount = Shop::where('status', 'approved')->count();
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();
        $blockedShopsCount = Shop::where('status', 'blocked')->count();
        
        $revenue = Order::sum('total_price');
        $totalOrdersCount = Order::count();
        $pendingOrdersCount = Order::where('status', 'Pending')->count();

        // Retrieve top 3 performing shops with order sums
        $topShops = Shop::withCount('orders')
            ->withSum('orders', 'total_price')
            ->orderBy('orders_count', 'desc')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'shopsCount', 'approvedShopsCount', 'pendingApprovalsCount', 'blockedShopsCount',
            'revenue', 'topShops', 'totalOrdersCount', 'pendingOrdersCount'
        ));
    }

    public function stores()
    {
        $stores = Shop::with('user')->withCount('orders')->withSum('orders', 'total_price')->get();
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();

        return view('admin.stores', compact('stores', 'pendingApprovalsCount'));
    }

    public function storesStatus(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $shop = Shop::findOrFail($request->shop_id);
        $shop->status = $request->status;
        $shop->save();

        return redirect()->back()->with('success', 'Shop status updated to ' . $request->status);
    }

    public function storesDelete($id)
    {
        $shop = Shop::findOrFail($id);

        // Delete inventory items linked to this shop
        \App\Models\Inventory::where('shop_id', $shop->id)->delete();

        // Delete orders linked to this shop
        \App\Models\Order::where('shop_id', $shop->id)->delete();

        // Delete shop image if stored locally
        if ($shop->image && file_exists(public_path($shop->image))) {
            @unlink(public_path($shop->image));
        }

        // Delete associated shop owner user account if exists and role is shop_owner
        if ($shop->user_id) {
            $user = \App\Models\User::find($shop->user_id);
            if ($user && $user->role === 'shop_owner') {
                $user->delete();
            }
        }

        $shopName = $shop->name;
        $shop->delete();

        return redirect()->back()->with('success', "Store '{$shopName}' and its owner user account have been deleted permanently!");
    }

    public function approvals()
    {
        $pendingApprovals = Shop::with('user')->where('status', 'pending')->get();
        $pendingApprovalsCount = $pendingApprovals->count();

        return view('admin.approvals', compact('pendingApprovals', 'pendingApprovalsCount'));
    }

    public function medicines()
    {
        $medicines = Medicine::paginate(50);
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();

        return view('admin.medicines', compact('medicines', 'pendingApprovalsCount'));
    }

    public function medicinesAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'mrp' => 'required|numeric',
            'price' => 'required|numeric',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $emojis = [
            'Fever' => '🌡️',
            'Antibiotic' => '💊',
            'Allergy' => '🤧',
            'Acidity' => '🔵',
            'Pain' => '🩹',
            'Diabetes' => '💉',
            'Heart' => '❤️',
            'Supplement' => '🍊',
            'Skin' => '🧴',
            'Eye' => '👁️',
            'Dental' => '🦷'
        ];

        $emoji = $emojis[$request->category] ?? '💊';

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/medicines'), $filename);
                $imagePaths[] = '/uploads/medicines/' . $filename;
            }
        }

        Medicine::create([
            'name' => $request->name,
            'category' => $request->category,
            'emoji' => $emoji,
            'mrp' => $request->mrp,
            'price' => $request->price,
            'images' => !empty($imagePaths) ? $imagePaths : null
        ]);

        return redirect('/admin/medicines')->with('success', 'Master medicine added successfully!');
    }

    public function medicinesDelete($id)
    {
        Medicine::destroy($id);

        return redirect('/admin/medicines')->with('success', 'Master medicine deleted.');
    }

    public function commission()
    {
        $commRate = (int) Setting::getVal('comm_rate', 2);
        $commOn = Setting::getVal('comm_on', 'true') === 'true';

        $wallets = Wallet::with('shop')->get();
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();

        return view('admin.commission', compact('commRate', 'commOn', 'wallets', 'pendingApprovalsCount'));
    }

    public function commissionUpdate(Request $request)
    {
        Setting::setVal('comm_on', $request->has('comm_on') ? 'true' : 'false');
        Setting::setVal('comm_rate', $request->input('comm_rate', 2));

        return redirect('/admin/commission')->with('success', 'Commission settings updated successfully!');
    }

    public function updateShopImage(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|integer',
            'shop_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $shop = Shop::findOrFail($request->shop_id);

        if ($request->hasFile('shop_image')) {
            $image = $request->file('shop_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shops'), $filename);
            $shop->image = '/uploads/shops/' . $filename;
            $shop->save();
        }

        return redirect()->back()->with('success', 'Shop image updated successfully!');
    }

    public function cleanInventoryDuplicates()
    {
        @set_time_limit(300);
        try {
            $normalizeKey = function($s) {
                $str = strtolower(trim((string)$s));
                $str = str_replace(['-', '_', '.', ',', '/'], ' ', $str);
                $str = preg_replace('/[^a-z0-9\s]/', '', $str);
                return trim(preg_replace('/\s+/', ' ', $str));
            };

            // 1. Fetch all shop inventories (super lightweight array of ~1000 items across all approved shops)
            $inventories = \App\Models\Inventory::with(['shop:id,name', 'medicine:id,name,image_urls'])->get();

            if ($inventories->isEmpty()) {
                return response('<div style="font-family:sans-serif; padding:40px; text-align:center; background:#f8fafc;"><h2>🎉 No Shop Inventory Records Found!</h2><a href="/admin/medicines">← Back to Admin</a></div>');
            }

            // 2. Extract unique medicine names from shop inventories
            $searchTerms = [];
            foreach ($inventories as $inv) {
                $name = $inv->medicine ? $inv->medicine->name : $inv->name;
                if (!empty($name)) {
                    $searchTerms[] = trim($name);
                    $searchTerms[] = str_replace('-', ' ', trim($name));
                }
            }
            $searchTerms = array_unique(array_filter($searchTerms));

            // 3. Target query ONLY relevant master medicines (RAM usage < 2 MB instead of 350MB!)
            $masterMedsList = Medicine::whereIn('name', $searchTerms)
                ->orWhere(function($q) use ($searchTerms) {
                    foreach (array_slice($searchTerms, 0, 100) as $st) {
                        if (strlen($st) >= 3) {
                            $q->orWhere('name', 'like', '%' . $st . '%');
                        }
                    }
                })->select('id', 'name', 'image_urls')->get();

            // Build normalized index of Master Medicines
            $masterMeds = [];
            foreach ($masterMedsList as $m) {
                $norm = $normalizeKey($m->name);
                if (empty($norm)) continue;

                if (!isset($masterMeds[$norm])) {
                    $masterMeds[$norm] = $m;
                } else {
                    $existing = $masterMeds[$norm];
                    $existHasImg = !empty($existing->image_urls) ? 1 : 0;
                    $newHasImg = !empty($m->image_urls) ? 1 : 0;

                    if ($newHasImg > $existHasImg) {
                        $masterMeds[$norm] = $m;
                    } elseif ($existHasImg === $newHasImg && str_contains((string)$existing->name, '-') && !str_contains((string)$m->name, '-')) {
                        $masterMeds[$norm] = $m;
                    }
                }
            }
            unset($masterMedsList);

            $logs = [];
            $remappedCount = 0;
            $mergedStockCount = 0;

            // 4. Re-map shop inventories to Master Catalog
            foreach ($inventories as $inv) {
                $currentName = $inv->medicine ? $inv->medicine->name : $inv->name;
                if (empty($currentName)) continue;

                $norm = $normalizeKey($currentName);
                $master = $masterMeds[$norm] ?? null;

                if ($master && $inv->medicine_id != $master->id) {
                    $oldName = $currentName;
                    $oldMedId = $inv->medicine_id;

                    $inv->medicine_id = $master->id;

                    if (empty($inv->images) && !empty($master->image_urls)) {
                        $inv->images = [$master->image_urls];
                    }

                    $inv->save();
                    $remappedCount++;

                    $logs[] = [
                        'shop' => $inv->shop ? $inv->shop->name : 'Shop #'.$inv->shop_id,
                        'old_name' => $oldName,
                        'old_id' => $oldMedId,
                        'master_name' => $master->name,
                        'master_id' => $master->id,
                        'status' => 'Re-mapped to Master Catalog Medicine'
                    ];
                }
            }

            // 5. Merge duplicate inventory rows within the same shop
            $shops = \App\Models\Shop::select('id', 'name')->get();
            foreach ($shops as $s) {
                $shopInvs = \App\Models\Inventory::where('shop_id', $s->id)->get();
                $groupedByMed = [];
                foreach ($shopInvs as $si) {
                    if (!$si->medicine_id) continue;
                    $groupedByMed[$si->medicine_id][] = $si;
                }

                foreach ($groupedByMed as $medId => $items) {
                    if (count($items) <= 1) continue;

                    $primaryInv = $items[0];
                    for ($k = 1; $k < count($items); $k++) {
                        $dupInv = $items[$k];
                        $primaryInv->quantity += $dupInv->quantity;
                        $dupInv->delete();
                        $mergedStockCount++;

                        $logs[] = [
                            'shop' => $s->name,
                            'old_name' => 'Duplicate Stock Row (Inv ID: '.$dupInv->id.')',
                            'old_id' => $medId,
                            'master_name' => 'Combined into Stock Row (Inv ID: '.$primaryInv->id.')',
                            'master_id' => $medId,
                            'status' => 'Merged Duplicate Shop Stock Quantity'
                        ];
                    }
                    $primaryInv->save();
                }
            }

            // Render detailed HTML Report
            $html = '<!DOCTYPE html><html><head><title>Inventory Cleanup Report</title>';
            $html .= '<style>body{font-family:sans-serif; padding:20px; background:#f4f6f9; color:#333;}';
            $html .= '.card{background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.08); margin-bottom:20px;}';
            $html .= 'table{width:100%; border-collapse:collapse; margin-top:15px;} th,td{padding:10px; border:1px solid #e5e7eb; text-align:left; font-size:13px;}';
            $html .= 'th{background:#1e3a8a; color:#fff;} tr:nth-child(even){background:#f8fafc;}';
            $html .= '.badge{background:#dcfce7; color:#166534; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;}</style></head><body>';

            $html .= '<div class="card"><h2>✨ Shop Inventory Cleanup & Remap Report</h2>';
            $html .= '<p><strong>Total Inventories Scanned:</strong> '.count($inventories).'</p>';
            $html .= '<p><strong>Re-mapped to Master Catalog:</strong> <span style="color:#059669; font-weight:bold;">'.$remappedCount.' items</span></p>';
            $html .= '<p><strong>Duplicate Shop Stock Rows Merged:</strong> <span style="color:#2563eb; font-weight:bold;">'.$mergedStockCount.' rows</span></p>';
            $html .= '<p><em>Note: Master Medicine Catalog entries were NOT deleted. Shop inventories were remapped to Master Medicines!</em></p>';
            $html .= '<a href="/admin/medicines" style="display:inline-block; margin-top:10px; background:#1e3a8a; color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none;">← Back to Admin Panel</a>';
            $html .= '</div>';

            if (empty($logs)) {
                $html .= '<div class="card"><h3 style="color:#059669;">🎉 Everything is already clean! No duplicate inventory items found.</h3></div>';
            } else {
                $html .= '<div class="card"><h3>📋 Detailed Changes Log ('.count($logs).' Records)</h3>';
                $html .= '<table><thead><tr><th>#</th><th>Shop Name</th><th>Previous Entry / Name</th><th>Old Med ID</th><th>Mapped Master Medicine</th><th>Master ID</th><th>Action Taken</th></tr></thead><tbody>';
                foreach ($logs as $idx => $log) {
                    $html .= '<tr>';
                    $html .= '<td>'.($idx + 1).'</td>';
                    $html .= '<td><strong>'.e($log['shop']).'</strong></td>';
                    $html .= '<td><code>'.e($log['old_name']).'</code></td>';
                    $html .= '<td>'.e($log['old_id']).'</td>';
                    $html .= '<td><strong>'.e($log['master_name']).'</strong></td>';
                    $html .= '<td>'.e($log['master_id']).'</td>';
                    $html .= '<td><span class="badge">'.e($log['status']).'</span></td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody></table></div>';
            }

            $html .= '</body></html>';
            return response($html);

        } catch (\Throwable $e) {
            return response('<div style="font-family:sans-serif; padding:30px; background:#fef2f2; border:2px solid #fca5a5; border-radius:12px; margin:20px; color:#991b1b;"><h2>⚠️ Diagnostic Error Trace</h2><p><strong>Error:</strong> '.e($e->getMessage()).'</p><p><strong>File:</strong> '.e($e->getFile()).' on Line '.e($e->getLine()).'</p><pre style="background:#fff; padding:15px; border-radius:8px; font-size:11px; overflow-x:auto;">'.e($e->getTraceAsString()).'</pre></div>', 500);
        }
    }

    public function orders(Request $request)
    {
        $status = $request->input('status', 'all');
        $query = Order::with(['shop', 'user'])->latest();

        if ($status !== 'all') {
            $query->where('status', ucfirst($status));
        }

        $orders = $query->paginate(25);

        $pendingCount = Order::where('status', 'Pending')->count();
        $acceptedCount = Order::where('status', 'Accepted')->count();
        $outForDeliveryCount = Order::where('status', 'Out for Delivery')->count();
        $deliveredCount = Order::where('status', 'Delivered')->count();
        $cancelledCount = Order::where('status', 'Cancelled')->count();
        $totalOrdersCount = Order::count();
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();

        $allShops = Shop::where('status', 'approved')->select('id', 'name')->get();

        return view('admin.orders', compact(
            'orders', 'status', 'pendingCount', 'acceptedCount', 'outForDeliveryCount', 
            'deliveredCount', 'cancelledCount', 'totalOrdersCount', 'pendingApprovalsCount', 'allShops'
        ));
    }

    public function ordersStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->id} status updated to '{$request->status}'!");
    }

    public function ordersAssignShop(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'shop_id' => 'required|integer',
        ]);

        $order = Order::findOrFail($request->order_id);
        $shop = Shop::findOrFail($request->shop_id);

        $order->shop_id = $shop->id;
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->id} successfully re-assigned to '{$shop->name}'!");
    }

    public function ordersUpdateCharges(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'delivery_charge' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->delivery_charge = (float)$request->delivery_charge;
        $order->discount_amount = (float)$request->discount_amount;
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->id} charges & discounts updated successfully!");
    }

    public function coupons()
    {
        \App\Models\Coupon::checkTable();
        $coupons = \App\Models\Coupon::latest()->get();
        $pendingApprovalsCount = Shop::where('status', 'pending')->count();

        $deliveryCharge = (float) Setting::getVal('delivery_charge', '20');
        $minDeliveryOrder = (float) Setting::getVal('min_delivery_order', '150');
        $freeDeliveryMin = (float) Setting::getVal('free_delivery_min', '500');

        return view('admin.coupons', compact('coupons', 'pendingApprovalsCount', 'deliveryCharge', 'minDeliveryOrder', 'freeDeliveryMin'));
    }

    public function updateDeliverySettings(Request $request)
    {
        $request->validate([
            'delivery_charge' => 'required|numeric|min:0',
            'min_delivery_order' => 'required|numeric|min:0',
            'free_delivery_min' => 'nullable|numeric|min:0',
        ]);

        Setting::setVal('delivery_charge', (string)$request->delivery_charge);
        Setting::setVal('min_delivery_order', (string)$request->min_delivery_order);
        Setting::setVal('free_delivery_min', (string)($request->free_delivery_min ?? 0));

        return redirect()->back()->with('success', 'Global Delivery Charges & Minimum Order Rules saved successfully!');
    }

    public function couponsAdd(Request $request)
    {
        \App\Models\Coupon::checkTable();
        $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:flat,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        \App\Models\Coupon::updateOrCreate(
            ['code' => $code],
            [
                'type' => $request->type,
                'value' => $request->value,
                'min_order_amount' => $request->min_order_amount ?? 0,
                'is_active' => true,
            ]
        );

        return redirect()->back()->with('success', "Coupon '{$code}' saved successfully!");
    }

    public function couponsDelete($id)
    {
        \App\Models\Coupon::checkTable();
        $coupon = \App\Models\Coupon::findOrFail($id);
        $code = $coupon->code;
        $coupon->delete();

        return redirect()->back()->with('success', "Coupon '{$code}' deleted successfully!");
    }
}
