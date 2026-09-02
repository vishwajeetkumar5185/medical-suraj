<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Medicine;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pills = ['Paracetamol', 'Azithromycin', 'Cetirizine', 'Omeprazole', 'Ibuprofen', 'Dolo 650', 'Metformin', 'Amoxicillin'];
        
        $categories = [
            ['icon' => '🤒', 'label' => 'Bukhar', 'color' => '#FEF3C7'],
            ['icon' => '💊', 'label' => 'Antibiotic', 'color' => '#DBEAFE'],
            ['icon' => '❤️', 'label' => 'Heart', 'color' => '#FEE2E2'],
            ['icon' => '🧴', 'label' => 'Skin', 'color' => '#F3E8FF'],
            ['icon' => '🦷', 'label' => 'Dental', 'color' => '#E0F2FE'],
            ['icon' => '👁️', 'label' => 'Eye', 'color' => '#DCFCE7']
        ];

        // Get popular medicines from database
        $popularMedicines = Medicine::whereIn('category', ['Fever', 'Pain', 'Allergy'])
            ->orderBy('name', 'asc')
            ->limit(6)
            ->get();

        $city = session('user_location', 'Muzaffarpur');
        
        // Extract the city word token from the selected address string
        $cityToken = trim(last(explode(',', $city)));
        if (empty($cityToken)) {
            $cityToken = $city;
        }
        
        // Get user's location from session
        $userLat = session('user_lat');
        $userLng = session('user_lng');
        
        // Debug log
        \Log::info('Homepage location check', [
            'user_lat' => $userLat,
            'user_lng' => $userLng,
            'location_name' => $city
        ]);
        
        // Global approved shops (all locations)
        $approvedShops = Shop::where('status', 'approved')->get();
        
        // Sort shops by distance if user location is available
        if ($userLat && $userLng) {
            \Log::info('Calculating distances for ' . $approvedShops->count() . ' shops');
            
            $shops = $approvedShops->map(function($shop) use ($userLat, $userLng) {
                // Calculate distance using Haversine formula
                $shop->distance = $this->calculateDistance(
                    $userLat, 
                    $userLng, 
                    $shop->latitude, 
                    $shop->longitude
                );
                
                \Log::info('Shop distance calculated', [
                    'shop' => $shop->name,
                    'distance' => $shop->distance
                ]);
                
                return $shop;
            })->sortBy('distance')->values();
        } else {
            \Log::warning('User location not available, showing shops without distance sorting');
            $shops = $approvedShops->values();
        }
        
        $shopsCount = $approvedShops->count();
        $onlineShopsCount = $approvedShops->where('is_online', true)->count();

        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.home', compact('pills', 'categories', 'shops', 'shopsCount', 'onlineShopsCount', 'cart', 'cartCount', 'popularMedicines'));
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat2 || !$lon2) {
            return 9999; // Return large number if shop location not set
        }
        
        $earthRadius = 6371; // Radius of Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $shopId = $request->input('shop_id');
        $selectedCategories = $request->input('categories', []);
        $selectedShop = null;

        $medQuery = Medicine::query();
        
        if ($shopId) {
            $selectedShop = Shop::findOrFail($shopId);
            // Joint query with inventories table to fetch only mapping medicines of this shop. No heavy plucking array.
            $medQuery->join('inventories', 'medicines.id', '=', 'inventories.medicine_id')
                     ->where('inventories.shop_id', $shopId)
                     ->select('medicines.*', 'inventories.price as shop_price', 'inventories.quantity as shop_qty');
        }

        if ($query) {
            $words = array_filter(explode(' ', trim($query)));
            $medQuery->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->where('medicines.name', 'like', '%' . $word . '%');
                }
            });
        }

        if (!empty($selectedCategories)) {
            $medQuery->whereIn('medicines.category', $selectedCategories);
        }

        // Order by: Exact/Prefix match first, then medicines having images, then alphabetical
        $cleanQ = addslashes(trim($query));
        $medQuery->orderByRaw("CASE 
            WHEN medicines.name LIKE '{$cleanQ}%' THEN 0 
            WHEN medicines.name LIKE '% {$cleanQ}%' THEN 1 
            WHEN medicines.images IS NOT NULL AND medicines.images != '[]' AND medicines.images != '' THEN 2 
            ELSE 3 
        END ASC")
        ->orderBy('medicines.name', 'asc');

        // Paginate in chunks (40 items per page) so server does not run out of memory (prevents 503)
        $medicines = $medQuery->paginate(40);
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        // Get all unique categories for checkbox sidebar filter
        $allCategories = ['Fever', 'Antibiotic', 'Allergy', 'Acidity', 'Pain', 'Diabetes', 'Heart', 'Supplement', 'Skin', 'Eye', 'Dental'];

        if ($request->ajax()) {
            return response(view('customer.search_results_inner', compact('medicines', 'cart', 'cartCount', 'query', 'selectedShop', 'allCategories', 'selectedCategories'))->render())
                ->header('Vary', 'X-Requested-With')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        return view('customer.search', compact('medicines', 'cart', 'cartCount', 'query', 'selectedShop', 'allCategories', 'selectedCategories'));
    }

    public function medicineDetails($id, Request $request)
    {
        $medicine = Medicine::findOrFail($id);
        $shopId = $request->input('shop_id');
        $selectedShop = null;
        $price = $medicine->price;

        if ($shopId) {
            $selectedShop = Shop::find($shopId);
            if ($selectedShop) {
                $inventory = $selectedShop->inventories()->where('medicine_id', $id)->first();
                if ($inventory) {
                    $price = $inventory->price;
                }
            }
        }

        $relatedMedicines = Medicine::where('category', $medicine->category)
            ->where('id', '!=', $id)
            ->limit(6)
            ->get();

        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.medicine_details', compact('medicine', 'relatedMedicines', 'price', 'selectedShop', 'cart', 'cartCount'));
    }

    public function profile()
    {
        $registeredShop = null;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $registeredShop = \Illuminate\Support\Facades\Auth::user()->shop;
        }
        
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.profile', compact('registeredShop', 'cartCount'));
    }

    public function orders()
    {
        $orders = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->get();
        $prescriptions = \App\Models\Prescription::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->get();
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.profile_orders', compact('orders', 'prescriptions', 'cartCount'));
    }

    public function addresses()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $orders = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereNotNull('delivery_address')
            ->distinct()
            ->pluck('delivery_address');

        return view('customer.profile_addresses', compact('orders', 'cartCount'));
    }

    public function favourites()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $shops = Shop::where('status', 'approved')->where('rating', '>=', 4.5)->limit(3)->get();

        return view('customer.profile_favourites', compact('shops', 'cartCount'));
    }

    public function notifications()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $notifications = [
            ['title' => 'Order Status Update', 'text' => 'Aapka Dolo 650 ka order Sharma Medical ne accept kar liya hai! ⚡', 'time' => '10 mins ago'],
            ['title' => 'Cashback Added 💰', 'text' => 'Dawalo wallet check karein, new promo coupon applied.', 'time' => '2 hours ago']
        ];

        return view('customer.profile_notifications', compact('notifications', 'cartCount'));
    }

    public function settings()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $user = \Illuminate\Support\Facades\Auth::user();

        return view('customer.profile_settings', compact('user', 'cartCount'));
    }

    public function help()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.profile_help', compact('cartCount'));
    }

    public function setLocation(\Illuminate\Http\Request $request)
    {
        $city = $request->input('city', 'Muzaffarpur');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        
        session(['user_location' => $city]);
        if ($lat && $lng) {
            session([
                'user_lat' => (float)$lat,
                'user_lng' => (float)$lng
            ]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'city' => $city, 'lat' => $lat, 'lng' => $lng]);
        }
        return redirect()->back()->with('success', 'Location updated to ' . $city);
    }

    public function medicineSearchSuggestions(\Illuminate\Http\Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }
        $words = array_filter(explode(' ', $q));
        $medsQuery = \App\Models\Medicine::query();
        foreach ($words as $word) {
            $medsQuery->where('name', 'like', '%' . $word . '%');
        }
        $meds = $medsQuery->limit(15)->get();
        return response()->json($meds);
    }
    
    /**
     * View all popular medicines
     */
    public function popularMedicines()
    {
        $medicines = Medicine::whereIn('category', ['Fever', 'Pain', 'Allergy', 'Antibiotic'])
            ->orderBy('name', 'asc')
            ->paginate(40);

        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.popular_medicines', compact('medicines', 'cart', 'cartCount'));
    }
    
    /**
     * View all nearby pharmacies
     */
    public function nearbyPharmacies()
    {
        // Get user's location from session
        $userLat = session('user_lat');
        $userLng = session('user_lng');
        
        // Get all approved shops
        $approvedShops = Shop::where('status', 'approved')->get();
        
        // Sort shops by distance if user location is available
        if ($userLat && $userLng) {
            $shops = $approvedShops->map(function($shop) use ($userLat, $userLng) {
                $shop->distance = $this->calculateDistance(
                    $userLat, 
                    $userLng, 
                    $shop->latitude, 
                    $shop->longitude
                );
                return $shop;
            })->sortBy('distance')->values();
        } else {
            $shops = $approvedShops->values();
        }

        $cart = session('cart', []);
        $cartCount = array_sum($cart);

        return view('customer.nearby_pharmacies', compact('shops', 'cart', 'cartCount', 'userLat', 'userLng'));
    }
}
