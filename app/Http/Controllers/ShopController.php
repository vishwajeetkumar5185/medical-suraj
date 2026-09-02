<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Inventory;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    private function getActiveShop()
    {
        if (Auth::check()) {
            return Auth::user()->shop;
        }
        return null;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'owner' => 'required|string',
            'phone' => 'required|string',
            'area' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'opens_at' => 'required|string',
            'closes_at' => 'required|string',
            'delivery_enabled' => 'nullable',
            'delivery_charge_type' => 'required|string|in:fixed,dynamic',
            'delivery_charge_rate' => 'nullable|numeric|min:0',
            'offer_min_bill' => 'nullable|numeric|min:0',
            'offer_discount_pct' => 'nullable|numeric|min:0|max:100'
        ]);

        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Pehle account login karein.');
        }

        $deliveryEnabled = $request->has('delivery_enabled');
        $chargeType = $request->delivery_charge_type;
        $rate = (float)($request->delivery_charge_rate ?? 0.00);

        $fixedCharge = ($chargeType === 'fixed') ? $rate : 0.00;
        $perKmCharge = ($chargeType === 'dynamic') ? $rate : 0.00;

        $shop = Shop::create([
            'name' => $request->name,
            'owner_name' => $request->owner,
            'phone' => $request->phone,
            'area' => $request->area,
            'address' => $request->address,
            'rating' => 5.0,
            'reviews' => 0,
            'distance_km' => round(rand(5, 45) / 10, 1),
            'is_top' => false,
            'delivery_enabled' => $deliveryEnabled,
            'is_online' => true,
            'status' => 'approved', // Automatically approve for demo purposes
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'opens_at' => $request->opens_at,
            'closes_at' => $request->closes_at,
            'delivery_charge_type' => $chargeType,
            'delivery_charge_fixed' => $fixedCharge,
            'delivery_charge_per_km' => $perKmCharge,
            'offer_min_bill' => (float)($request->offer_min_bill ?? 0.00),
            'offer_discount_pct' => (float)($request->offer_discount_pct ?? 0.00),
        ]);

        // Create wallet
        Wallet::create([
            'shop_id' => $shop->id,
            'total_sales' => 0,
            'due_commission' => 0,
            'credit_limit' => 100,
            'status' => 'active'
        ]);

        // Automatically update role to shop_owner if they were customer
        $user = Auth::user();
        if ($user->role === 'customer') {
            $user->role = 'shop_owner';
            $user->save();
        }

        return redirect('/shop/dashboard')->with('success', 'Store registered successfully!');
    }

    public function dashboard()
    {
        $shop = $this->getActiveShop();
        if (!$shop) {
            return redirect('/profile')->with('error', 'Pehle store register karein!');
        }

        $orders = Order::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();
        $ordersCount = $orders->count();
        $revenue = $orders->sum('total_price');
        
        $inventoryCount = Inventory::where('shop_id', $shop->id)->count();

        $wallet = Wallet::where('shop_id', $shop->id)->first();

        $prescriptions = \App\Models\Prescription::where('shop_id', $shop->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('shop.dashboard', compact('shop', 'ordersCount', 'revenue', 'inventoryCount', 'wallet', 'prescriptions'));
    }

    public function toggleOnline(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $shop->is_online = !$shop->is_online;
        $shop->save();

        return redirect()->back()->with('success', 'Online status updated!');
    }

    public function toggleDelivery(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $shop->delivery_enabled = !$shop->delivery_enabled;
        $shop->save();

        return redirect()->back()->with('success', 'Delivery status updated!');
    }

    public function quickSetupIndex(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $category = $request->input('category', 'All');
        $search = $request->input('q', '');
        $company = $request->input('company', 'All');

        $medQuery = Medicine::query();
        if ($category !== 'All') {
            if ($category === 'Tablet') {
                $medQuery->where(function($q) {
                    $q->where('product_form', 'like', '%tablet%')
                      ->orWhere('product_form', 'like', '%capsule%');
                });
            } elseif ($category === 'Liquid') {
                $medQuery->where(function($q) {
                    $q->where('product_form', 'like', '%liquid%')
                      ->orWhere('product_form', 'like', '%suspension%')
                      ->orWhere('product_form', 'like', '%syrup%')
                      ->orWhere('product_form', 'like', '%solution%')
                      ->orWhere('product_form', 'like', '%drop%');
                });
            } elseif ($category === 'Powder') {
                $medQuery->where(function($q) {
                    $q->where('product_form', 'like', '%powder%')
                      ->orWhere('product_form', 'like', '%sachet%')
                      ->orWhere('product_form', 'like', '%granule%');
                });
            } elseif ($category === 'Injection') {
                $medQuery->where(function($q) {
                    $q->where('product_form', 'like', '%injection%')
                      ->orWhere('product_form', 'like', '%vial%')
                      ->orWhere('product_form', 'like', '%ampoule%')
                      ->orWhere('product_form', 'like', '%prefilled pen%');
                });
            } elseif ($category === 'Ointment/Cream') {
                $medQuery->where(function($q) {
                    $q->where('product_form', 'like', '%ointment%')
                      ->orWhere('product_form', 'like', '%cream%')
                      ->orWhere('product_form', 'like', '%gel%')
                      ->orWhere('product_form', 'like', '%lotion%');
                });
            } else {
                $medQuery->where('product_form', 'like', "%{$category}%");
            }
        }
        if ($search) {
            $words = array_filter(explode(' ', $search));
            $medQuery->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function($sub) use ($word) {
                        $sub->where('name', 'like', "%{$word}%")
                            ->orWhere('product_form', 'like', "%{$word}%")
                            ->orWhere('category', 'like', "%{$word}%");
                    });
                }
            });
        }
        
        $company = $request->input('company', 'All');
        $allCompanies = ['Cipla Ltd', 'Abbott India', 'Sun Pharma', 'Alkem Laboratories', 'Mankind Pharma', 'Lupin Ltd'];

        // Fetch only the IDs of medicines that this shop HAS in its inventory to prioritize unadded ones
        $allShopInvMeds = \App\Models\Inventory::where('shop_id', $shop->id)->pluck('medicine_id')->toArray();

        // Instead of CASE WHEN IN order-by-raw (which ruins SQL index optimization),
        // we fetch unadded medicines first using whereNotIn, and if empty, fetch normal medicines.
        $qsRawOrder = "CASE 
          WHEN (image_urls IS NOT NULL AND image_urls != '' OR images IS NOT NULL AND images != '') 
               AND (prescription_required = 'Yes' OR prescription_required = '1' OR category IN ('Antibiotic', 'Heart', 'Anti Neoplastics', 'Diabetes', 'Psychiatry')) 
          THEN 1
          WHEN (image_urls IS NOT NULL AND image_urls != '' OR images IS NOT NULL AND images != '') 
          THEN 2
          ELSE 3
        END ASC, name ASC";

        if (count($allShopInvMeds) > 0) {
            // Get unadded medicines count first
            $unaddedQuery = clone $medQuery;
            $unaddedQuery->whereNotIn('id', $allShopInvMeds);
            $unaddedCount = $unaddedQuery->count();

            $page = (int) $request->input('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            if ($offset < $unaddedCount) {
                // We are still loading unadded medicines
                $masterMedicines = $unaddedQuery->orderByRaw($qsRawOrder)->paginate(10)->withQueryString();
            } else {
                // We need to load already added medicines
                $addedQuery = clone $medQuery;
                $addedQuery->whereIn('id', $allShopInvMeds);
                
                // Adjust pagination offset logic manually to seamlessly transition
                $addedPage = $page - ceil($unaddedCount / $perPage);
                if ($addedPage <= 0) $addedPage = 1;
                
                // Set page custom to load next chunks of added medicines
                $masterMedicines = $addedQuery->orderByRaw($qsRawOrder)
                    ->paginate(10, ['*'], 'page', $addedPage)
                    ->withQueryString();
            }
        } else {
            // If shop has 0 inventory items, retrieve medicines sorted by priority order
            $masterMedicines = $medQuery->orderByRaw($qsRawOrder)->paginate(10)->withQueryString();
        }

        // 2. Fetch inventory records matching the paginated subset IDs for the active shop
        $medicineIds = $masterMedicines->pluck('id')->toArray();
        $shopInventories = \App\Models\Inventory::where('shop_id', $shop->id)
            ->whereIn('medicine_id', $medicineIds)
            ->get()
            ->keyBy('medicine_id');

        // 3. Bind temporary attributes to Eloquent models manually
        foreach ($masterMedicines as $med) {
            $inv = $shopInventories->get($med->id);
            $med->shop_price = $inv ? $inv->price : null;
            $med->shop_quantity = $inv ? $inv->quantity : null;
        }

        // Pluck only necessary IDs of the shop's inventory to keep memory footprint close to zero
        $shopInventoryIds = \App\Models\Inventory::where('shop_id', $shop->id)
            ->whereIn('medicine_id', $medicineIds)
            ->pluck('medicine_id')
            ->toArray();
        
        // Pass a minimal representation or lookup helper to view instead of loading 288k eloquent objects
        if ($request->ajax()) {
            return view('shop.quicksetup_inner', compact('shop', 'masterMedicines', 'shopInventoryIds', 'category', 'search', 'company', 'allCompanies'));
        }

        return view('shop.quicksetup', compact('shop', 'masterMedicines', 'shopInventoryIds', 'category', 'search', 'company', 'allCompanies'));
    }

    public function quickSetupSave(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $qsSel = $request->input('qs_sel', []);
        $addedCount = 0;

        foreach ($qsSel as $medIdStr => $data) {
            $medId = (int) str_replace('m', '', $medIdStr);
            $has = isset($data['has']) && $data['has'] === 'true';
            $price = (float) ($data['price'] ?? 0);
            $qty = (int) ($data['qty'] ?? 50);

            if ($has) {
                Inventory::updateOrCreate(
                    ['shop_id' => $shop->id, 'medicine_id' => $medId],
                    ['price' => $price, 'quantity' => $qty]
                );
                $addedCount++;
            } else {
                Inventory::where('shop_id', $shop->id)->where('medicine_id', $medId)->delete();
            }
        }

        return redirect()->back()->with('success', 'Catalogue medicines updated successfully!');
    }

    public function inventoryIndex()
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $inventory = Inventory::where('shop_id', $shop->id)
            ->with(['medicine' => function($query) {
                $query->select('id', 'name', 'category', 'emoji', 'product_form', 'image_urls');
            }])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('shop.inventory', compact('shop', 'inventory'));
    }

    public function medicineSearchSuggestions(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $meds = Medicine::where('name', 'like', '%' . $q . '%')->limit(15)->get();
        return response()->json($meds);
    }

    public function inventoryAdd(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $medId = $request->input('medicine_id');
        $master = null;
        if ($medId) {
            $master = Medicine::find($medId);
        } else {
            $master = Medicine::where('name', 'like', '%' . $request->name . '%')->first();
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/medicines'), $filename);
                $imagePaths[] = '/uploads/medicines/' . $filename;
            }
        }

        // Process pipe separated link URLs
        if ($request->filled('image_links')) {
            $links = explode('|', $request->image_links);
            foreach ($links as $link) {
                $trimmed = trim($link);
                if (!empty($trimmed)) {
                    $imagePaths[] = $trimmed;
                }
            }
        }

        Inventory::create([
            'shop_id' => $shop->id,
            'medicine_id' => $master ? $master->id : null,
            'name' => $master ? null : $request->name,
            'price' => $request->price,
            'quantity' => $request->qty,
            'images' => !empty($imagePaths) ? $imagePaths : null
        ]);

        return redirect('/shop/inventory')->with('success', 'Medicine stock added manually!');
    }

    public function inventoryDelete(Request $request, $id)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        // Confirm inventory item belongs to this shop for protection
        Inventory::where('id', $id)->where('shop_id', $shop->id)->delete();

        return redirect('/shop/inventory')->with('success', 'Medicine stock removed!');
    }

    public function uploadInventoryExtraImage(Request $request, $id)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'extra_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $inv = Inventory::where('id', $id)->where('shop_id', $shop->id)->firstOrFail();
        $imagePaths = is_array($inv->images) ? $inv->images : [];

        if ($request->hasFile('extra_image')) {
            $image = $request->file('extra_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/medicines'), $filename);
            $newPath = '/uploads/medicines/' . $filename;

            // Add new camera photo to the front of images array
            array_unshift($imagePaths, $newPath);
            $inv->images = array_values(array_unique($imagePaths));
            $inv->save();

            // ALSO sync photo to master Medicine model so customer search renders the photo!
            if ($inv->medicine) {
                $masterImgs = !empty($inv->medicine->image_urls) ? explode('|', $inv->medicine->image_urls) : [];
                if (!in_array($newPath, $masterImgs)) {
                    array_unshift($masterImgs, $newPath);
                    $inv->medicine->image_urls = implode('|', array_unique(array_filter($masterImgs)));
                    $inv->medicine->save();
                }
            }
        }

        return redirect('/shop/inventory')->with('success', '📷 Photo added successfully to medicine!');
    }

    public function downloadSampleInventoryTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_inventory_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Selling Price', 'Image', 'Company', 'Composition', 'Quantity']);
            fputcsv($file, ['Paracetamol 500mg Tablet', '35.00', 'https://example.com/images/paracetamol.jpg', 'Cipla Ltd', 'Paracetamol (500mg)', '100']);
            fputcsv($file, ['Ascoril Plus Expectorant', '157.90', 'https://example.com/images/ascoril.jpg', 'Glenmark Pharmaceuticals', 'Bromhexine (2mg) + Guaifenesin (50mg) + Terbutaline (1.25mg)', '50']);
            fputcsv($file, ['Cetirizine 10mg Tablet', '20.00', 'https://example.com/images/cetirizine.jpg', 'Abbott India', 'Cetirizine Dihydrochloride (10mg)', '150']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseXlsxFast($filePath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== TRUE) {
            return false;
        }

        $sharedStrings = [];
        if (($fp = $zip->getStream('xl/sharedStrings.xml')) !== FALSE) {
            $xml = new \XMLReader();
            $xml->XML(stream_get_contents($fp));
            fclose($fp);

            while ($xml->read()) {
                if ($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 'si') {
                    $node = new \SimpleXMLElement($xml->readOuterXML());
                    $text = '';
                    if (isset($node->t)) {
                        $text = (string)$node->t;
                    } else if (isset($node->r)) {
                        foreach ($node->r as $r) {
                            $text .= (string)$r->t;
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
            $xml->close();
        }

        $rows = [];
        if (($fp = $zip->getStream('xl/worksheets/sheet1.xml')) !== FALSE) {
            $xml = new \XMLReader();
            $xml->XML(stream_get_contents($fp));
            fclose($fp);

            while ($xml->read()) {
                if ($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 'row') {
                    $rowXml = new \SimpleXMLElement($xml->readOuterXML());
                    $rowCells = [];
                    foreach ($rowXml->c as $c) {
                        $cellRef = (string)$c['r'];
                        preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                        $colStr = $matches[1] ?? 'A';
                        
                        $colIdx = 0;
                        $len = strlen($colStr);
                        for ($i = 0; $i < $len; $i++) {
                            $colIdx = $colIdx * 26 + (ord($colStr[$i]) - ord('A') + 1);
                        }
                        $colIdx--;

                        $t = (string)$c['t'];
                        $v = (string)$c->v;

                        if ($t === 's' && isset($sharedStrings[(int)$v])) {
                            $val = $sharedStrings[(int)$v];
                        } else {
                            $val = $v;
                        }

                        $rowCells[$colIdx] = $val;
                    }
                    if (!empty($rowCells)) {
                        $maxIdx = max(array_keys($rowCells));
                        $fullRow = [];
                        for ($k = 0; $k <= $maxIdx; $k++) {
                            $fullRow[$k] = $rowCells[$k] ?? '';
                        }
                        $rows[] = $fullRow;
                    }
                }
            }
            $xml->close();
        }

        $zip->close();
        unset($sharedStrings);
        return $rows;
    }

    public function inventoryBulkUpload(Request $request)
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');

        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:102400'
        ]);

        $file = $request->file('excel_file');
        $rows = [];

        try {
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['xlsx', 'xls'])) {
                $rows = $this->parseXlsxFast($file->getRealPath());
                if ($rows === false || empty($rows)) {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($file->getRealPath());
                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray();
                    unset($spreadsheet);
                }
            } else {
                if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                        $rows[] = $data;
                    }
                    fclose($handle);
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading spreadsheet file: ' . $e->getMessage());
        }

        if (empty($rows) || count($rows) < 2) {
            return redirect()->back()->with('error', 'Uploaded file is empty or does not contain data rows!');
        }

        // Parse header row to locate column indexes dynamically
        $header = array_map(function($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$h)));
        }, $rows[0]);

        $nameIdx = -1;
        $priceIdx = -1;
        $imageIdx = -1;
        $companyIdx = -1;
        $quantityIdx = -1;
        $compositionIdx = -1;

        foreach ($header as $idx => $col) {
            if ($nameIdx === -1 && (str_contains($col, 'name') || str_contains($col, 'item') || str_contains($col, 'medicine') || str_contains($col, 'product') || str_contains($col, 'dawai'))) {
                $nameIdx = $idx;
            } elseif ($priceIdx === -1 && (str_contains($col, 'price') || str_contains($col, 'rate') || str_contains($col, 'mrp') || str_contains($col, 'seling') || str_contains($col, 'cost') || str_contains($col, 'amount'))) {
                $priceIdx = $idx;
            } elseif ($imageIdx === -1 && (str_contains($col, 'image') || str_contains($col, 'img') || str_contains($col, 'photo') || str_contains($col, 'pic') || str_contains($col, 'picture'))) {
                $imageIdx = $idx;
            } elseif ($companyIdx === -1 && (str_contains($col, 'company') || str_contains($col, 'brand') || str_contains($col, 'marketer') || str_contains($col, 'manufacturer'))) {
                $companyIdx = $idx;
            } elseif ($quantityIdx === -1 && (str_contains($col, 'quantity') || str_contains($col, 'qty') || str_contains($col, 'stock') || str_contains($col, 'count'))) {
                $quantityIdx = $idx;
            } elseif ($compositionIdx === -1 && (str_contains($col, 'composition') || str_contains($col, 'generic') || str_contains($col, 'salt') || str_contains($col, 'formula') || str_contains($col, 'ingredient') || str_contains($col, 'content'))) {
                $compositionIdx = $idx;
            }
        }

        if ($nameIdx === -1) $nameIdx = 0;
        if ($priceIdx === -1) $priceIdx = 1;
        if ($imageIdx === -1) $imageIdx = 2;
        if ($companyIdx === -1) $companyIdx = 3;

        // Process data rows in 500-item chunks using DB transaction batches for 30x performance
        $dataRows = array_slice($rows, 1);
        unset($rows); // Free raw array from memory

        $updatedCount = 0;
        $addedCount = 0;
        $chunks = array_chunk($dataRows, 500);

        foreach ($chunks as $chunk) {
            $namesInChunk = [];
            $parsedItems = [];

            foreach ($chunk as $row) {
                if (empty($row) || !isset($row[$nameIdx])) continue;
                $name = trim((string)$row[$nameIdx]);
                if (empty($name)) continue;

                $cleanName = trim(preg_replace('/\s+/', ' ', $name));
                $rawPrice = isset($row[$priceIdx]) ? (string)$row[$priceIdx] : '0';
                $sellingPrice = (float)preg_replace('/[^0-9.]/', '', $rawPrice);
                $image = isset($row[$imageIdx]) ? trim((string)$row[$imageIdx]) : '';
                $company = isset($row[$companyIdx]) ? trim((string)$row[$companyIdx]) : '';
                $composition = ($compositionIdx !== -1 && isset($row[$compositionIdx])) ? trim((string)$row[$compositionIdx]) : '';

                $rawQty = ($quantityIdx !== -1 && isset($row[$quantityIdx])) ? (string)$row[$quantityIdx] : '';
                $qtyInput = (int)preg_replace('/[^0-9]/', '', $rawQty);
                if ($qtyInput <= 0) $qtyInput = 100;

                $namesInChunk[] = $cleanName;
                $parsedItems[] = [
                    'name' => $cleanName,
                    'price' => $sellingPrice,
                    'image' => $image,
                    'company' => $company,
                    'composition' => $composition,
                    'qty' => $qtyInput
                ];
            }

            if (empty($parsedItems)) continue;

            // Normalized helper for flexible case/hyphen-insensitive matching
            $normalizeKey = function($s) {
                $str = strtolower(trim((string)$s));
                $str = str_replace(['-', '_', '.', ',', '/'], ' ', $str);
                $str = preg_replace('/[^a-z0-9\s]/', '', $str);
                return trim(preg_replace('/\s+/', ' ', $str));
            };

            // Build search variations for batch lookup (original, space-replaced, hyphen-replaced)
            $searchTerms = [];
            foreach ($namesInChunk as $n) {
                $searchTerms[] = $n;
                $searchTerms[] = str_replace('-', ' ', $n);
                $searchTerms[] = str_replace(' ', '-', $n);
            }
            $searchTerms = array_unique(array_filter($searchTerms));

            // Fetch candidate medicines from DB
            $candidateMeds = Medicine::whereIn('name', $searchTerms)->get();
            
            $existingMeds = [];
            $normalizedMeds = [];
            foreach ($candidateMeds as $m) {
                $exactKey = strtolower(trim($m->name));
                $normKey = $normalizeKey($m->name);
                if (!isset($existingMeds[$exactKey])) {
                    $existingMeds[$exactKey] = $m;
                }
                if (!isset($normalizedMeds[$normKey])) {
                    $normalizedMeds[$normKey] = $m;
                }
            }

            // Batch lookup existing shop inventories by candidate medicine IDs
            $existingMedIds = $candidateMeds->pluck('id')->toArray();
            $existingInvs = Inventory::where('shop_id', $shop->id)
                ->whereIn('medicine_id', $existingMedIds)
                ->get()
                ->keyBy('medicine_id');

            \DB::transaction(function() use ($parsedItems, &$existingMeds, &$normalizedMeds, &$existingInvs, $shop, $normalizeKey, &$updatedCount, &$addedCount) {
                foreach ($parsedItems as $item) {
                    $exactKey = strtolower(trim($item['name']));
                    $normKey = $normalizeKey($item['name']);

                    // 1. Try exact lowercase match
                    $medicine = $existingMeds[$exactKey] ?? null;

                    // 2. Try normalized key match (handles betnecip-0.5mg-t... vs Betnecip 0.5mg Tablet)
                    if (!$medicine && !empty($normKey)) {
                        $medicine = $normalizedMeds[$normKey] ?? null;
                    }

                    // 3. Try database fuzzy match fallback
                    if (!$medicine) {
                        $dehyphenated = str_replace('-', ' ', $item['name']);
                        $medicine = Medicine::where('name', 'like', '%' . $dehyphenated . '%')->first();
                        if ($medicine) {
                            $existingMeds[$exactKey] = $medicine;
                            $normalizedMeds[$normKey] = $medicine;
                        }
                    }

                    if (!$medicine) {
                        // Format clean title case for new medicines
                        $formattedName = ucwords(str_replace(['-', '_'], ' ', $item['name']));
                        $medicine = Medicine::create([
                            'name' => $formattedName,
                            'price' => $item['price'] > 0 ? $item['price'] : 50,
                            'mrp' => $item['price'] > 0 ? $item['price'] : 50,
                            'marketer' => !empty($item['company']) ? $item['company'] : 'General',
                            'composition' => !empty($item['composition']) ? $item['composition'] : null,
                            'category' => 'General',
                            'emoji' => '💊',
                            'image_urls' => !empty($item['image']) ? $item['image'] : null
                        ]);
                        $existingMeds[$exactKey] = $medicine;
                        $normalizedMeds[$normKey] = $medicine;
                    } else {
                        if (!empty($item['company']) && (empty($medicine->marketer) || $medicine->marketer === 'General')) {
                            $medicine->marketer = $item['company'];
                        }
                        if (!empty($item['composition']) && (empty($medicine->composition) || $medicine->composition === 'General')) {
                            $medicine->composition = $item['composition'];
                        }
                        if (!empty($item['image']) && empty($medicine->image_urls)) {
                            $medicine->image_urls = $item['image'];
                        }
                        if ($medicine->isDirty()) {
                            $medicine->save();
                        }
                    }

                    // Smart Image Fallback:
                    // If Excel has image -> use Excel image
                    // Else if Master DB has image -> use Master DB image
                    // Else -> null (view renders 💊 medicine icon fallback automatically)
                    $imageToUse = !empty($item['image']) ? $item['image'] : (!empty($medicine->image_urls) ? $medicine->image_urls : null);

                    $inv = $existingInvs->get($medicine->id);
                    if ($inv) {
                        if ($item['price'] > 0) $inv->price = $item['price'];
                        if ($item['qty'] > 0) $inv->quantity = $item['qty'];
                        if (!empty($imageToUse)) {
                            $currentImgs = is_array($inv->images) ? $inv->images : [];
                            if (!in_array($imageToUse, $currentImgs)) {
                                array_unshift($currentImgs, $imageToUse);
                                $inv->images = $currentImgs;
                            }
                        }
                        $inv->save();
                        $updatedCount++;
                    } else {
                        $newInv = Inventory::create([
                            'shop_id' => $shop->id,
                            'medicine_id' => $medicine->id,
                            'price' => $item['price'] > 0 ? $item['price'] : ($medicine->price ?: 50),
                            'quantity' => $item['qty'],
                            'images' => !empty($imageToUse) ? [$imageToUse] : null
                        ]);
                        $existingInvs->put($medicine->id, $newInv);
                        $addedCount++;
                    }
                }
            });
        }

        $totalProcessed = $updatedCount + $addedCount;
        return redirect('/shop/inventory')->with('success', "🎉 Bulk Upload Successful! Processed {$totalProcessed} medicines ({$updatedCount} updated, {$addedCount} newly created).");
    }

    public function ordersIndex()
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $orders = Order::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

        return view('shop.orders', compact('shop', 'orders'));
    }

    public function ordersUpdate(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string'
        ]);

        // Secure checking that order belongs to active shop owner
        $order = Order::where('id', $request->order_id)->where('shop_id', $shop->id)->firstOrFail();
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated to ' . $request->status);
    }

    public function updateTimings(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'opens_at' => 'required|string',
            'closes_at' => 'required|string',
        ]);

        $shop->opens_at = $request->opens_at;
        $shop->closes_at = $request->closes_at;
        $shop->save();

        return redirect()->back()->with('success', 'Store timings updated successfully!');
    }

    public function updateDeliverySettings(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'delivery_radius_km' => 'required|numeric|min:0.1',
            'delivery_charge_type' => 'required|string|in:fixed,dynamic',
            'delivery_charge_fixed' => 'nullable|numeric|min:0',
            'delivery_charge_per_km' => 'nullable|numeric|min:0',
            'offer_min_bill' => 'required|numeric|min:0',
            'offer_discount_pct' => 'required|numeric|min:0|max:100',
        ]);

        $shop->delivery_radius_km = $request->delivery_radius_km;
        $shop->delivery_charge_type = $request->delivery_charge_type;
        $shop->delivery_charge_fixed = $request->delivery_charge_fixed ?? 0.00;
        $shop->delivery_charge_per_km = $request->delivery_charge_per_km ?? 0.00;
        $shop->offer_min_bill = $request->offer_min_bill;
        $shop->offer_discount_pct = $request->offer_discount_pct;
        $shop->save();

        return redirect()->back()->with('success', 'Delivery and Offer settings updated successfully!');
    }

    public function settingsIndex()
    {
        $shop = $this->getActiveShop();
        if (!$shop) {
            return redirect('/profile')->with('error', 'Pehle store register karein!');
        }

        $wallet = Wallet::where('shop_id', $shop->id)->first();

        return view('shop.settings', compact('shop', 'wallet'));
    }

    public function updateShopImage(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'shop_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('shop_image')) {
            $image = $request->file('shop_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shops'), $filename);
            $shop->image = '/uploads/shops/' . $filename;
            $shop->save();
        }

        return redirect()->back()->with('success', 'Shop image updated successfully!');
    }

    public function updateLocation(Request $request)
    {
        $shop = $this->getActiveShop();
        if (!$shop) return redirect('/profile');

        $request->validate([
            'area' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $shop->area = $request->area;
        $shop->address = $request->address;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->save();

        return redirect()->back()->with('success', 'Store location and map coordinates updated successfully!');
    }

    public function statusBackground(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string|in:Accepted,Cancelled'
        ]);

        $shop = $this->getActiveShop();
        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated or no active shop.'], 401);
        }

        // Secure check that order belongs to active shop owner
        $order = Order::where('id', $request->order_id)->where('shop_id', $shop->id)->first();
        if (!$order) {
            \Log::warning('statusBackground: Order not found or not belonging to shop', ['order_id' => $request->order_id, 'shop_id' => $shop->id]);
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        \Log::info('statusBackground: Updating order status', ['order_id' => $order->id, 'new_status' => $request->status, 'old_status' => $order->status]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.'
        ]);
    }
}
