@extends('layouts.app')

@section('seo_title', 'Dawalo 💊 - Online Medicine Aggregator & Home Delivery in Bihar | Powered by TechoMission')
@section('seo_description', 'Search medicines, verify stock at nearby local pharmacies, and get home deliveries in 45 minutes with Bihar\'s most reliable local medicine aggregator. Powered by TechoMission - Bihar Best IT Company.')
@section('seo_keywords', 'dawalo, online medicine store, online pharmacy Bihar, check medicine stock, medicine delivery Patna, medicine delivery Muzaffarpur, buy generic medicine, TechoMission, Bihar best IT company, techomission.com')

@section('content')
<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
  .screen { overflow: visible !important; height: auto !important; min-height: 100vh !important; }
  
  /* Hide scrollbar for Popular Dawaiyan section */
  div[style*="overflow-x:auto"]::-webkit-scrollbar {
    display: none;
  }
  
  div[style*="overflow-x:auto"] {
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  
  /* Add hover effect for medicine cards */
  .medicine-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
  }
  
  .medicine-card {
    transition: all 0.2s ease;
  }
  
  .medicine-card button:hover {
    background: #2563EB !important;
  }
</style>
<div class="screen" style="background:#F5F7FA; min-height:100vh; display:block !important;">
  <!-- === HEADER === -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:12px 16px 20px; border-radius:0 0 20px 20px; position:relative;">
    
    <!-- Location Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:4px; margin-bottom:2px;">
          <span style="width:8px; height:8px; background:#EF4444; border-radius:50%;"></span>
          <span style="color:#fff; font-size:11px; font-weight:600; opacity:0.9;">Delivering to</span>
        </div>
        <div style="color:#fff; font-size:16px; font-weight:800; display:flex; align-items:center; gap:4px;" data-location-display>
          {{ session('user_location', 'Detecting...') }} <span style="font-size:10px;">›</span>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">💬</span>
        </div>
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">🔔</span>
        </div>
        <a href="{{ url('/profile') }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">👤</span>
        </a>
      </div>
    </div>

    <!-- Greeting -->
    <div style="margin-bottom:16px;">
      <h2 style="color:#fff; font-size:17px; font-weight:700; margin:0; line-height:1.3;">
        Namaste 👋 aaj kaisi tabiyat hai?
      </h2>
    </div>

    <!-- Search Box -->
    <form action="{{ url('/search') }}" method="GET" style="margin-bottom:14px;" onsubmit="event.preventDefault(); triggerHomeSearch();">
      <div style="background:#fff; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        <span style="font-size:20px; color:#94A3B8;">🔍</span>
        <input 
          type="text" 
          name="q"
          id="home-search-input"
          placeholder="Medicine ya lab test search karein..." 
          style="flex:1; border:none; outline:none; font-size:14px; color:#94A3B8; font-weight:500; background:transparent;"
          autocomplete="off"
          oninput="debouncedHomeSearchSuggestions(this.value)"
        >
      </div>
      <div id="home-search-autocomplete" style="display:none; position:absolute; left:16px; right:16px; background:#fff; border-radius:12px; margin-top:8px; box-shadow:0 8px 24px rgba(0,0,0,0.12); max-height:500px; overflow-y:auto; z-index:9999;"></div>
    </form>

    <!-- Category Pills -->
    <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:2px;">
      <a href="#" onclick="clickPillSearch('Bukhar'); return false;" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🤒</span> Bukhar
      </a>
      <a href="#" onclick="clickPillSearch('Diabetes'); return false;" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🩸</span> Diabetes
      </a>
      <a href="#" onclick="clickPillSearch('Skin Care'); return false;" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💧</span> Skin Care
      </a>
      <a href="#" onclick="clickPillSearch('Pain'); return false;" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💊</span> Pain
      </a>
    </div>
  </div>

  <!-- === MAIN CONTENT === -->
  <div style="padding:16px; padding-bottom:100px; overflow-y:auto;">
    
    <!-- Dynamic Search Results -->
    <div id="home-search-results" style="display:none;"></div>

    <div id="home-default-content" style="display:block !important; visibility:visible !important;">
      
      <!-- Free Delivery Banner -->
      <div style="background:linear-gradient(135deg, #FB923C 0%, #F97316 100%); border-radius:16px; padding:18px 20px; margin-bottom:16px; position:relative; overflow:hidden; box-shadow:0 4px 12px rgba(249,115,22,0.25);">
        <div style="position:absolute; top:-10px; right:-10px; font-size:80px; opacity:0.2;">🎁</div>
        <div style="position:relative; z-index:1;">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <span style="font-size:20px;">🚚</span>
            <span style="color:#fff; font-size:19px; font-weight:800;">₹399 se upar Free Delivery!</span>
          </div>
          <div style="color:rgba(255,255,255,0.95); font-size:13px; font-weight:600;">Sabhi users ke liye offer</div>
        </div>
        <div style="position:absolute; bottom:10px; right:20px; font-size:60px; opacity:0.5;">👨‍👩‍👧‍👦</div>
      </div>

      <!-- Three Features -->
      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:16px;">
        <div style="background:#fff; border-radius:14px; padding:16px 12px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
          <div style="width:50px; height:50px; background:#E8F5E9; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:26px;">
            ✅
          </div>
          <div style="font-size:13px; font-weight:800; color:#1A1A1A; margin-bottom:3px;">100% Asli</div>
          <div style="font-size:10px; color:#64748B; font-weight:600;">Genuine Medicine</div>
        </div>
        <div style="background:#fff; border-radius:14px; padding:16px 12px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
          <div style="width:50px; height:50px; background:#FFF3E0; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:26px;">
            🚚
          </div>
          <div style="font-size:13px; font-weight:800; color:#1A1A1A; margin-bottom:3px;">Same Day</div>
          <div style="font-size:10px; color:#64748B; font-weight:600;">Delivery</div>
        </div>
        <div style="background:#fff; border-radius:14px; padding:16px 12px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
          <div style="width:50px; height:50px; background:#FCE7F3; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:26px;">
            🏷️
          </div>
          <div style="font-size:13px; font-weight:800; color:#1A1A1A; margin-bottom:3px;">10% Off</div>
          <div style="font-size:10px; color:#64748B; font-weight:600;">Discount</div>
        </div>
      </div>

      <!-- Prescription Upload Card -->
      <a href="{{ url('/prescription/upload') }}" style="text-decoration:none; display:block; margin-bottom:20px;">
        <div style="background:linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); border-radius:16px; padding:18px 20px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 4px 12px rgba(37,99,235,0.25);">
          <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:58px; height:58px; background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:30px;">
              📋
            </div>
            <div>
              <div style="color:#fff; font-size:15px; font-weight:800; margin-bottom:3px;">Prescription /</div>
              <div style="color:#fff; font-size:15px; font-weight:800; margin-bottom:4px;">Medicine Photo</div>
              <div style="color:rgba(255,255,255,0.85); font-size:12px; font-weight:600;">Order Now</div>
            </div>
          </div>
          <div style="background:#fff; color:#3B82F6; font-size:13px; font-weight:800; padding:11px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            Upload ↑
          </div>
        </div>
      </a>

      <!-- Shop by Category -->
      <div style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <h3 style="font-size:17px; font-weight:800; color:#1A1A1A; margin:0;">Shop by Category</h3>
          <a href="{{ url('/search') }}" style="color:#3B82F6; font-size:13px; font-weight:700; text-decoration:none;">View All ›</a>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
          <a href="{{ url('/search?q=Cold') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS003256_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Cold & Cough">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Cold & Cough</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Fever') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS352322_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Fever & Pain">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Fever & Pain</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Pain') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS008085_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Pain Relief">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Pain Relief</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Heart') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS030209_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Heart Care">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Heart Care</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Diabetes') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS073799_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Diabetic">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Diabetic</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Blood Pressure') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://medicinedata.in/drg/DRS094782_1.jpg" style="width:100%; height:100%; object-fit:cover;" alt="Blood Pressure">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Blood Pressure</div>
            </div>
          </a>
        </div>
      </div>

      <!-- Popular Dawaiyan -->
      <div style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; position:relative; z-index:10;">
          <h3 style="font-size:17px; font-weight:800; color:#1A1A1A; margin:0;">Popular Dawaiyan</h3>
          <a href="{{ url('/popular-medicines') }}" 
             onclick="console.log('View All clicked'); return true;" 
             style="color:#3B82F6; font-size:13px; font-weight:700; text-decoration:none; cursor:pointer; padding:4px 8px; border-radius:6px; transition:background 0.2s ease; position:relative; z-index:100;" 
             onmouseover="this.style.background='#EFF6FF'" 
             onmouseout="this.style.background='transparent'">View All ›</a>
        </div>
        
        <div id="popular-medicines-carousel" style="display:flex; gap:12px; overflow-x:auto; padding-bottom:8px; -webkit-overflow-scrolling:touch; scrollbar-width:none; -ms-overflow-style:none;">
          @foreach($popularMedicines as $index => $medicine)
          <div class="medicine-card" style="min-width:150px; max-width:150px; background:#fff; border-radius:16px; padding:14px; box-shadow:0 2px 12px rgba(0,0,0,0.08); position:relative; flex-shrink:0;">
            @if($index == 0)
              <div style="position:absolute; top:10px; left:10px; background:#10B981; color:#fff; font-size:10px; font-weight:800; padding:5px 10px; border-radius:8px; z-index:1;">10% OFF</div>
            @endif
            <a href="{{ url('/medicine/'.$medicine->id) }}" style="text-decoration:none; display:block;">
              <div style="width:100%; height:100px; background:#F8FAFC; border-radius:12px; margin-bottom:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:8px;">
                @if(!empty($medicine->images))
                  @php
                    $images = is_array($medicine->images) ? $medicine->images : json_decode($medicine->images, true);
                    $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
                    $firstImgUrl = $firstImage ? ((strpos($firstImage, 'http://') === 0 || strpos($firstImage, 'https://') === 0) ? $firstImage : asset($firstImage)) : null;
                  @endphp
                  @if($firstImgUrl)
                    <img src="{{ $firstImgUrl }}" referrerpolicy="no-referrer" style="max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain;" alt="{{ $medicine->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-block';">
                    <span style="font-size:48px; display:none;">{{ $medicine->emoji ?? '💊' }}</span>
                  @else
                    <span style="font-size:48px;">{{ $medicine->emoji ?? '💊' }}</span>
                  @endif
                @else
                  <span style="font-size:48px;">{{ $medicine->emoji ?? '💊' }}</span>
                @endif
              </div>
              <div style="font-size:13px; font-weight:800; color:#1A1A1A; margin-bottom:6px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.3; min-height:34px;">{{ $medicine->name }}</div>
              <div style="display:flex; align-items:center; gap:6px; margin-bottom:10px; flex-wrap:wrap;">
                @if($medicine->mrp && $medicine->price < $medicine->mrp)
                  <span style="font-size:15px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
                  <span style="font-size:11px; color:#94A3B8; text-decoration:line-through;">₹{{ number_format($medicine->mrp, 0) }}</span>
                @else
                  <span style="font-size:15px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
                @endif
              </div>
            </a>
            <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0;">
              @csrf
              <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" style="width:100%; background:#3B82F6; color:#fff; border:none; border-radius:10px; padding:10px; font-size:13px; font-weight:800; cursor:pointer; transition:background 0.2s ease;">+ Add</button>
            </form>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Nearby Open Pharmacies Section -->
      <div style="margin-bottom:80px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <div style="width:10px; height:10px; background:#22C55E; border-radius:50%; box-shadow:0 0 0 3px rgba(34,197,94,0.3);"></div>
            <h3 style="font-size:17px; font-weight:800; color:#1A1A1A; margin:0;">Pharmacy Stores</h3>
          </div>
          <a href="{{ url('/nearby-pharmacies') }}" style="color:#3B82F6; font-size:13px; font-weight:700; text-decoration:none;">View All ›</a>
        </div>

        <!-- Filter Pill Tabs: Nearby (≤5km), My City, All -->
        <div style="display:flex; gap:8px; margin-bottom:16px; overflow-x:auto; padding-bottom:4px; -webkit-overflow-scrolling:touch;">
          <button type="button" onclick="filterPharmacies('nearby')" id="tab-nearby" class="pharmacy-filter-btn active-tab" style="padding:8px 16px; border-radius:24px; border:1px solid #3B82F6; background:#3B82F6; color:#fff; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap; transition:all 0.2s ease;">
            📍 Nearby (< 5 km)
          </button>
          <button type="button" onclick="filterPharmacies('city')" id="tab-city" class="pharmacy-filter-btn" style="padding:8px 16px; border-radius:24px; border:1px solid #E2E8F0; background:#F8FAFC; color:#64748B; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap; transition:all 0.2s ease;">
            🏙️ My City ({{ $cityToken ?? 'Muzaffarpur' }})
          </button>
          <button type="button" onclick="filterPharmacies('all')" id="tab-all" class="pharmacy-filter-btn" style="padding:8px 16px; border-radius:24px; border:1px solid #E2E8F0; background:#F8FAFC; color:#64748B; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap; transition:all 0.2s ease;">
            🌐 All Stores
          </button>
        </div>
        
        <!-- Pharmacy Cards List -->
        <div style="display:flex; flex-direction:column; gap:12px;" id="pharmacy-list-container">
          @foreach($shops as $shop)
          @php
            $shopDist = isset($shop->distance) ? (float)$shop->distance : (float)($shop->distance_km ?? 9999);
            $fullAddrStr = strtolower(($shop->area ?? '').' '.($shop->address ?? '').' '.($shop->name ?? ''));
          @endphp
          <a href="{{ url('/search?shop_id='.$shop->id) }}" 
             class="shop-card-item" 
             data-distance="{{ $shopDist }}" 
             data-address="{{ $fullAddrStr }}"
             style="text-decoration:none;">
            <div style="background:#fff; border-radius:16px; padding:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); display:flex; gap:12px; align-items:center;">
              <div style="width:56px; height:56px; background:#EEF2FF; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0; overflow:hidden;">
                @if($shop->image)
                  <img src="{{ asset($shop->image) }}" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                @else
                  🏥
                @endif
              </div>
              <div style="flex:1;">
                <div style="font-size:14px; font-weight:800; color:#1A1A1A; margin-bottom:3px;">{{ $shop->name }}</div>
                <div style="font-size:12px; color:#64748B; margin-bottom:4px;">
                  📍 {{ $shop->area }}
                  @if($shopDist < 9999)
                    <span style="color:#3B82F6; font-weight:700;"> • {{ number_format($shopDist, 1) }} km</span>
                  @endif
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-size:11px; color:#F59E0B; font-weight:700;">★ {{ number_format($shop->rating ?? 4.5, 1) }}</span>
                  @php
                    $isOpen = $shop->isOpen();
                  @endphp
                  @if($isOpen)
                    <span style="font-size:11px; color:#10B981; font-weight:700;">🟢 Open Now</span>
                  @else
                    <span style="font-size:11px; color:#EF4444; font-weight:700;">🔴 Closed</span>
                  @endif
                  @if($shop->delivery_enabled)
                    <span style="font-size:11px; color:#3B82F6; font-weight:600;">🛵 Delivery</span>
                  @endif
                </div>
              </div>
              <div style="color:#3B82F6; font-size:20px;">›</div>
            </div>
          </a>
          @endforeach

          <!-- Empty State Message when no shops match active filter -->
          <div id="no-shops-message" style="display:none; text-align:center; padding:28px 16px; background:#fff; border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <div style="font-size:36px; margin-bottom:8px;">🏥</div>
            <div style="font-size:14px; font-weight:700; color:#1A1A1A;" id="no-shops-title">No pharmacies found within 5 km</div>
            <div style="font-size:12px; color:#64748B; margin-top:4px;">Try switching to <b>My City</b> or <b>All Stores</b> tab above.</div>
          </div>
        </div>
      </div>

    </div>

  </div>

  <!-- Bottom Navigation -->
  <div style="position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:600px; background:#fff; border-top:1px solid #E5E7EB; padding:8px 20px 12px; display:flex; justify-content:space-around; align-items:center; z-index:1000;">
    <a href="{{ url('/') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; background:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:4px; box-shadow:0 2px 8px rgba(59,130,246,0.3);">
        <span style="font-size:22px;">🏠</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#3B82F6;">Home</span>
    </a>
    <a href="{{ url('/smartcart') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none; position:relative;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🛒</span>
      </div>
      @if($cartCount > 0)
        <span style="position:absolute; top:-4px; right:4px; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px; min-width:18px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);">{{ $cartCount }}</span>
      @endif
      <span style="font-size:11px; font-weight:700; color:#64748B;">Cart</span>
    </a>
    <a href="{{ url('/profile') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Profile</span>
    </a>
  </div>

</div>

<script>
  // Search functionality
  let searchTimeout;

  function clickPillSearch(term) {
    document.getElementById('home-search-input').value = term;
    triggerHomeSearch();
  }

  function debouncedHomeSearchSuggestions(query) {
    clearTimeout(searchTimeout);
    const dropdown = document.getElementById('home-search-autocomplete');
    const q = query.trim();

    if (q.length === 0) {
      dropdown.style.display = 'none';
      return;
    }

    // Show loading state
    dropdown.style.display = 'block';
    dropdown.innerHTML = '<div style="padding:16px; text-align:center; color:#64748B; font-size:13px;">🔍 Searching...</div>';

    searchTimeout = setTimeout(() => {
      fetch(`{{ url('/medicines/search') }}?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          dropdown.innerHTML = '';
          if (data.length === 0) {
            dropdown.innerHTML = '<div style="padding:20px; text-align:center;"><div style="font-size:40px; margin-bottom:8px;">😔</div><div style="color:#64748B; font-size:14px; font-weight:600;">No medicines found</div><div style="color:#94A3B8; font-size:12px; margin-top:4px;">Try a different name</div></div>';
            return;
          }
          
          // Add header with count
          const header = document.createElement('div');
          header.style.cssText = 'padding:12px 16px; background:#f8fafc; border-bottom:1px solid #e5e7eb; font-size:14px; font-weight:700; color:#1f2937;';
          header.textContent = 'Medicines';
          dropdown.appendChild(header);
          
          // Add medicines list
          data.forEach((item, index) => {
            const row = document.createElement('div');
            row.style.cssText = 'padding:12px 16px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:12px; background:#fff;';
            
            // Get image
            let imgSrc = null;
            if (item.images) {
              const imgs = Array.isArray(item.images) ? item.images : JSON.parse(item.images || '[]');
              imgSrc = imgs[0] || null;
            }
            
            let imgHtml = `<span style="font-size:24px;">${item.emoji || '💊'}</span>`;
            if (imgSrc) {
              const fullSrc = (imgSrc.startsWith('http://') || imgSrc.startsWith('https://')) ? imgSrc : `${imgSrc}`;
              imgHtml = `<img src="${fullSrc}" style="width:100%; height:100%; object-fit:contain;" onerror="this.outerHTML='<span style=\\'font-size:24px;\\'>${item.emoji || '💊'}</span>'">`;
            }

            row.innerHTML = `
              <div style="width:48px; height:48px; background:#f8fafc; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
                ${imgHtml}
              </div>
              <div style="flex:1; min-width:0;">
                <div style="font-size:14px; font-weight:700; color:#1f2937; margin-bottom:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.name}</div>
                <div style="font-size:12px; color:#0284c7; font-weight:600; margin-bottom:2px;">${item.composition || 'Strip of tablets'}</div>
                <div style="font-size:11px; color:#6b7280;">${item.marketer || 'Manufacturer'}</div>
              </div>
              <div style="flex-shrink:0;">
                <div style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px;">₹${item.price || '0.00'}</div>
                <button onclick="addToCart(${item.id}); event.stopPropagation();" style="background:#0ea5e9; color:#fff; border:none; border-radius:6px; padding:6px 16px; font-size:12px; font-weight:700; cursor:pointer;">ADD</button>
              </div>
            `;
            
            row.addEventListener('click', () => {
              window.location.href = `{{ url('/medicine') }}/${item.id}`;
            });
            
            dropdown.appendChild(row);
          });
          
          // Add "View all medicines" button if more than 10
          if (data.length >= 10) {
            const viewAllBtn = document.createElement('div');
            viewAllBtn.style.cssText = 'padding:16px; text-align:center; background:#f0f9ff; cursor:pointer;';
            viewAllBtn.innerHTML = '<span style="color:#0ea5e9; font-size:14px; font-weight:700;">View all medicines →</span>';
            viewAllBtn.addEventListener('click', () => {
              triggerHomeSearch();
            });
            dropdown.appendChild(viewAllBtn);
          }
        })
        .catch(err => {
          console.error(err);
          dropdown.innerHTML = '<div style="padding:20px; text-align:center;"><div style="font-size:40px; margin-bottom:8px;">⚠️</div><div style="color:#EF4444; font-size:14px; font-weight:600;">Error loading medicines</div></div>';
        });
    }, 300);
  }

  // Add to cart function
  function addToCart(medicineId) {
    fetch('{{ url("/cart/add") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        medicine_id: medicineId,
        quantity: 1
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('✓ Added to cart!');
      } else {
        alert(data.message || 'Failed to add to cart');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error adding to cart');
    });
  }

  function triggerHomeSearch() {
    const q = document.getElementById('home-search-input').value.trim();
    if (q) {
      window.location.href = "{{ url('/search') }}?q=" + encodeURIComponent(q);
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('home-search-autocomplete');
    const searchInput = document.getElementById('home-search-input');
    if (dropdown && searchInput && !dropdown.contains(e.target) && e.target !== searchInput) {
      dropdown.style.display = 'none';
    }
  });

  // Focus on search shows dropdown if there's content
  document.getElementById('home-search-input')?.addEventListener('focus', function() {
    const dropdown = document.getElementById('home-search-autocomplete');
    if (this.value.trim().length > 0 && dropdown.innerHTML !== '') {
      dropdown.style.display = 'block';
    }
  });

  function filterPharmacies(type) {
    const tabs = document.querySelectorAll('.pharmacy-filter-btn');
    tabs.forEach(tab => {
      tab.style.background = '#F8FAFC';
      tab.style.color = '#64748B';
      tab.style.borderColor = '#E2E8F0';
    });

    const activeTab = document.getElementById('tab-' + type);
    if (activeTab) {
      activeTab.style.background = '#3B82F6';
      activeTab.style.color = '#FFFFFF';
      activeTab.style.borderColor = '#3B82F6';
    }

    const cards = document.querySelectorAll('.shop-card-item');
    const currentCity = "{{ strtolower($cityToken ?? 'muzaffarpur') }}";
    let visibleCount = 0;

    cards.forEach(card => {
      const distance = parseFloat(card.dataset.distance || 9999);
      const address = (card.dataset.address || '').toLowerCase();

      let show = false;
      if (type === 'nearby') {
        // ONLY shops within 5 km
        show = (distance <= 5.0);
      } else if (type === 'city') {
        // Shops matching user's city or area
        show = address.includes(currentCity) || currentCity.includes(address) || (distance <= 15.0);
      } else if (type === 'all') {
        // All shops
        show = true;
      }

      if (show) {
        card.style.display = 'block';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const noShopsMsg = document.getElementById('no-shops-message');
    const noShopsTitle = document.getElementById('no-shops-title');
    if (noShopsMsg) {
      if (visibleCount === 0) {
        noShopsMsg.style.display = 'block';
        if (noShopsTitle) {
          if (type === 'nearby') {
            noShopsTitle.textContent = 'No pharmacies found within 5 km';
          } else if (type === 'city') {
            noShopsTitle.textContent = 'No pharmacies found in {{ $cityToken ?? "your city" }}';
          } else {
            noShopsTitle.textContent = 'No pharmacies found';
          }
        }
      } else {
        noShopsMsg.style.display = 'none';
      }
    }
  }

  // Initial filter: Nearby <= 5km by default
  document.addEventListener('DOMContentLoaded', function() {
    filterPharmacies('nearby');
    
    // Auto-scroll for Popular Dawaiyan section
    const popularContainer = document.getElementById('popular-medicines-carousel');
    if (popularContainer) {
      let scrollInterval;
      let isUserScrolling = false;
      let scrollTimeout;
      
      // Auto scroll function
      function autoScroll() {
        if (!isUserScrolling && popularContainer) {
          const cardWidth = 162; // 150px card + 12px gap
          const maxScroll = popularContainer.scrollWidth - popularContainer.clientWidth;
          
          // Smooth scroll to next card
          if (popularContainer.scrollLeft >= maxScroll - 10) {
            // Reset to beginning with smooth animation
            popularContainer.scrollTo({ left: 0, behavior: 'smooth' });
          } else {
            popularContainer.scrollBy({ left: cardWidth, behavior: 'smooth' });
          }
        }
      }
      
      // Start auto-scrolling every 3 seconds
      scrollInterval = setInterval(autoScroll, 3000);
      
      // Pause auto-scroll when user manually scrolls
      popularContainer.addEventListener('scroll', function() {
        isUserScrolling = true;
        clearInterval(scrollInterval);
        
        // Resume auto-scroll after 5 seconds of no user interaction
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
          isUserScrolling = false;
          scrollInterval = setInterval(autoScroll, 3000);
        }, 5000);
      });
      
      // Pause on touch/mouse interaction
      popularContainer.addEventListener('touchstart', function() {
        isUserScrolling = true;
        clearInterval(scrollInterval);
      });
      
      popularContainer.addEventListener('mouseenter', function() {
        isUserScrolling = true;
        clearInterval(scrollInterval);
      });
      
      popularContainer.addEventListener('mouseleave', function() {
        isUserScrolling = false;
        scrollInterval = setInterval(autoScroll, 3000);
      });
    }
  });
</script>

