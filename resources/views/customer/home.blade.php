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
  
  /* PWA Install Banner Animations */
  @keyframes slideDown {
    from {
      transform: translateY(-100%);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  @keyframes slideUp {
    from {
      transform: translateY(0);
      opacity: 1;
    }
    to {
      transform: translateY(-100%);
      opacity: 0;
    }
  }
  
  /* Install Button Effects */
  #install-app-btn:hover {
    background: #f0f9ff !important;
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
  }
  
  #dismiss-install-btn:hover {
    background: rgba(255,255,255,0.25) !important;
    transform: scale(1.1);
  }
  
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
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:12px 16px 20px; border-radius:0 0 20px 20px; position:relative; z-index:100;">
    
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

    <!-- Real-time Live Search Input -->
    <div style="margin-bottom:14px; position:relative; z-index:101;" id="home-search-wrapper">
      <form action="{{ url('/search') }}" method="GET" style="margin:0;">
        <div style="background:#fff; border-radius:12px; padding:8px 12px; display:flex; align-items:center; gap:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:1px solid rgba(255,255,255,0.4);">
          <span style="font-size:18px; color:#0EA5E9;">🔍</span>
          <input 
            type="text" 
            id="home-search-input" 
            name="q"
            placeholder="Medicine ya lab test search karein..." 
            autocomplete="off"
            style="flex:1; border:none; outline:none; font-size:14px; color:#1A1A1A; font-weight:500; background:transparent; padding:6px 0;"
          >
          <button type="submit" style="background:#0EA5E9; color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:13px; font-weight:700; cursor:pointer; flex-shrink:0;">Search</button>
        </div>
      </form>

      <!-- Instant Live Suggestions Dropdown Container -->
      <div id="home-search-suggestions" style="display:none; position:absolute; top:calc(100% + 6px); left:0; right:0; background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,0.18); z-index:99999; max-height:380px; overflow-y:auto; border:1px solid #E2E8F0; padding:6px 0;">
      </div>
    </div>

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
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766580/Blue_and_Black_Modern_E-Sport_Gaming_Initial_E_Abstract_Mark_Logo.png" style="width:100%; height:100%; object-fit:cover;" alt="Cold & Cough">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Cold & Cough</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Fever') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766534/Blue_and_Black_Modern_E-Sport_Gaming_Initial_E_Abstract_Mark_Logo_1.png" style="width:100%; height:100%; object-fit:cover;" alt="Fever & Pain">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Fever & Pain</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Pain') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766534/Gemini_Generated_Image_wb0kqjwb0kqjwb0k.png" style="width:100%; height:100%; object-fit:cover;" alt="Pain Relief">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Pain Relief</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Heart') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766535/Gemini_Generated_Image_ivjhanivjhanivjh.png" style="width:100%; height:100%; object-fit:cover;" alt="Heart Care">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Heart Care</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Diabetes') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766534/Gemini_Generated_Image_cte40pcte40pcte4.png" style="width:100%; height:100%; object-fit:cover;" alt="Diabetic">
              </div>
              <div style="font-size:11px; font-weight:700; color:#1A1A1A; text-align:center; line-height:1.2;">Diabetic</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Blood Pressure') }}" style="text-decoration:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px;">
                <img src="https://res.cloudinary.com/js6syrfv/image/upload/v1788766534/Gemini_Generated_Image_szu5onszu5onszu5.png" style="width:100%; height:100%; object-fit:cover;" alt="Blood Pressure">
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
              <button type="submit" style="width:100%; background:#3B82F6; color:#fff; border:none; border-radius:10px; padding:10px; font-size:13px; font-weight:800; cursor:pointer; transition:all 0.2s ease;" onmouseover="this.style.background='#2563EB'" onmouseout="this.style.background='#3B82F6'">+ Add</button>
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
        <span class="bottom-nav-cart-badge" style="position:absolute; top:-4px; right:4px; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px; min-width:18px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);">{{ $cartCount }}</span>
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
// PWA Control System - Scoped to avoid conflicts
(function() {
  'use strict';
  
  let pwaPrompt = null; // Renamed to avoid conflicts
  const PWA_DISMISS_KEY = 'dawalo_pwa_dismissed';
  const PWA_INSTALL_KEY = 'dawalo_pwa_installed';
  
  // PWA Event Listeners
  window.addEventListener('beforeinstallprompt', (e) => {
    console.log('Browser PWA prompt intercepted');
    e.preventDefault();
    e.stopImmediatePropagation();
    
    const dismissed = localStorage.getItem(PWA_DISMISS_KEY);
    const installed = localStorage.getItem(PWA_INSTALL_KEY);
    
    if (dismissed || installed) {
      console.log('PWA banner suppressed - user choice exists');
      return false;
    }
    
    pwaPrompt = e;
    setTimeout(() => showPWABanner(), 2000);
    return false;
  });
  
  window.addEventListener('appinstalled', () => {
    console.log('App installed successfully');
    localStorage.setItem(PWA_INSTALL_KEY, Date.now().toString());
    hidePWABanner();
  });
  
  // Show PWA Banner
  function showPWABanner() {
    // Check user preferences
    const dismissed = localStorage.getItem(PWA_DISMISS_KEY);
    const installed = localStorage.getItem(PWA_INSTALL_KEY);
    
    if (dismissed || installed) {
      console.log('Banner creation cancelled - user choice exists');
      return;
    }
    
    // Remove existing banner
    const existingBanner = document.getElementById('pwa-banner');
    if (existingBanner) existingBanner.remove();
    
    // Create banner
    const banner = document.createElement('div');
    banner.id = 'pwa-banner';
    banner.style.cssText = `
      position: fixed; top: 0; left: 0; right: 0; z-index: 999999;
      background: linear-gradient(135deg, #3B82F6, #1D4ED8);
      color: white; padding: 12px 16px;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      animation: slideDown 0.4s ease-out;
    `;
    
    banner.innerHTML = `
      <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
        <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.25); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px;">💊</div>
        <div>
          <div style="font-weight: 800; font-size: 16px;">Dawalo Install Karein</div>
          <div style="font-size: 13px; opacity: 0.9;">Check pharmacy medicine inventory live</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px; align-items: center;">
        <button id="pwa-install-btn" style="background: #fff; color: #3B82F6; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 800; font-size: 14px; cursor: pointer;">Install</button>
        <button id="pwa-dismiss-btn" style="background: rgba(255,255,255,0.15); color: #fff; border: none; padding: 8px 10px; font-size: 20px; cursor: pointer; border-radius: 6px;">×</button>
      </div>
    `;
    
    document.body.insertBefore(banner, document.body.firstChild);
    document.body.style.paddingTop = '80px';
    
    // Event listeners
    document.getElementById('pwa-install-btn').onclick = installPWA;
    document.getElementById('pwa-dismiss-btn').onclick = dismissPWA;
  }
  
  // Install PWA function
  function installPWA() {
    if (pwaPrompt) {
      pwaPrompt.prompt();
      pwaPrompt.userChoice.then((result) => {
        if (result.outcome === 'accepted') {
          localStorage.setItem(PWA_INSTALL_KEY, Date.now().toString());
        } else {
          localStorage.setItem(PWA_DISMISS_KEY, Date.now().toString());
        }
        hidePWABanner();
        pwaPrompt = null;
      });
    } else {
      localStorage.setItem(PWA_DISMISS_KEY, Date.now().toString());
      hidePWABanner();
    }
  }
  
  // Dismiss PWA function
  function dismissPWA() {
    localStorage.setItem(PWA_DISMISS_KEY, Date.now().toString());
    hidePWABanner();
    pwaPrompt = null;
  }
  
  // Hide banner function
  function hidePWABanner() {
    const banner = document.getElementById('pwa-banner');
    if (banner) {
      banner.style.animation = 'slideUp 0.3s ease-in';
      setTimeout(() => {
        banner.remove();
        document.body.style.paddingTop = '0';
      }, 300);
    }
  }

})(); // End PWA module
// Global PWA utilities
window.resetPWAStatus = function() {
  localStorage.removeItem('dawalo_pwa_dismissed');
  localStorage.removeItem('dawalo_pwa_installed');
  const banner = document.getElementById('pwa-banner');
  if (banner) banner.remove();
  document.body.style.paddingTop = '0';
  console.log('✅ PWA status reset');
};

window.checkPWAStatus = function() {
  const dismissed = localStorage.getItem('dawalo_pwa_dismissed');
  const installed = localStorage.getItem('dawalo_pwa_installed');
  console.log('📱 PWA Status:', {
    dismissed: dismissed ? new Date(parseInt(dismissed)).toLocaleString() : '❌ No',
    installed: installed ? new Date(parseInt(installed)).toLocaleString() : '❌ No',
    will_show: (!dismissed && !installed) ? '✅ Yes' : '❌ No'
  });
};

// Search functionality
  // Search functionality - Simple redirect to search page
function clickPillSearch(term) {
  window.location.href = "{{ url('/search') }}?q=" + encodeURIComponent(term);
}

function redirectToSearchPage() {
  window.location.href = "{{ url('/search') }}";
}

  // Cart functionality
  function addToCart(medicineId) {
    // This function is kept for compatibility but not used on homepage anymore
    console.log('Redirecting to search page for medicine:', medicineId);
    window.location.href = "{{ url('/search') }}";
  }

  // Cart functionality

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

  // Handle cart forms on homepage with smart notifications
  document.querySelectorAll('.cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = this.querySelector('button');
      const originalText = btn.textContent;
      const medicineId = this.querySelector('input[name="medicine_id"]').value;
      
      // Check if recently added
      const storageKey = `cart_added_${medicineId}`;
      const lastAdded = localStorage.getItem(storageKey);
      const now = Date.now();
      
      btn.textContent = 'Adding...';
      btn.disabled = true;
      btn.style.background = '#9CA3AF';

      fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          btn.textContent = '✓ Added';
          btn.style.background = '#10B981';
          
          // Update cart count in bottom navigation always
          const cartBadge = document.querySelector('.bottom-nav-cart-badge');
          if (cartBadge) {
            cartBadge.textContent = data.cartCount;
            cartBadge.style.display = data.cartCount > 0 ? 'block' : 'none';
          }
          
          // Store timestamp
          localStorage.setItem(storageKey, now.toString());
          
          // If recently added, just log to console
          if (lastAdded && (now - parseInt(lastAdded)) < 5000) {
            console.log(`Medicine ${medicineId} quantity updated in cart. Current count: ${data.cartCount}`);
          }
          
          // Reset button after 1.5 seconds
          setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '#3B82F6';
            btn.disabled = false;
          }, 1500);
        } else {
          alert(data.message || 'Failed to add to cart');
          btn.textContent = originalText;
          btn.style.background = '#3B82F6';
          btn.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error adding to cart');
        btn.textContent = originalText;
        btn.style.background = '#3B82F6';
        btn.disabled = false;
      });
    });
  });

  // Live Instant Medicine Search Autocomplete
  (function() {
    const searchInput = document.getElementById('home-search-input');
    const suggestionsBox = document.getElementById('home-search-suggestions');
    const searchWrapper = document.getElementById('home-search-wrapper');
    let debounceTimer = null;

    if (!searchInput || !suggestionsBox) return;

    function fetchSuggestions() {
      const query = searchInput.value.trim();
      clearTimeout(debounceTimer);

      if (query.length < 1) {
        suggestionsBox.style.display = 'none';
        suggestionsBox.innerHTML = '';
        return;
      }

      debounceTimer = setTimeout(() => {
        fetch("{{ url('/medicines/search') }}?q=" + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            if (!data || !Array.isArray(data) || data.length === 0) {
              suggestionsBox.innerHTML = `
                <div style="padding:14px 16px; text-align:center; color:#64748B; font-size:13px;">
                  <span>🔍</span> Koi medicine nahi mili "<strong>${escapeHtml(query)}</strong>" ke naam se.
                </div>`;
            } else {
              let html = '';
              data.forEach(med => {
                try {
                  let imgHtml = '<div style="width:38px; height:38px; background:#F1F5F9; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">💊</div>';
                  
                  let firstImg = null;
                  if (med.images) {
                    if (Array.isArray(med.images) && med.images.length > 0) {
                      firstImg = med.images[0];
                    } else if (typeof med.images === 'string') {
                      firstImg = med.images;
                    }
                  }

                  if (typeof firstImg === 'string' && firstImg.trim() !== '') {
                    const firstImgStr = firstImg.trim();
                    const imgUrl = (firstImgStr.startsWith('http://') || firstImgStr.startsWith('https://')) ? firstImgStr : '{{ asset("") }}' + firstImgStr;
                    imgHtml = `<img src="${imgUrl}" style="width:38px; height:38px; object-fit:contain; border-radius:8px; border:1px solid #E2E8F0; flex-shrink:0;" onerror="this.onerror=null; this.outerHTML='<div style=\\'width:38px; height:38px; background:#F1F5F9; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;\\'>💊</div>';">`;
                  }

                  const price = parseFloat(med.price || 0).toFixed(2);
                  const mrp = parseFloat(med.mrp || 0).toFixed(2);
                  const category = med.category || 'General';

                  html += `
                    <a href="{{ url('/medicine') }}/${med.id}" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:12px; padding:10px 14px; border-bottom:1px solid #F1F5F9; transition:background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                      ${imgHtml}
                      <div style="flex:1; min-width:0;">
                        <div style="font-size:14px; font-weight:700; color:#1E293B; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                          ${highlightMatch(med.name, query)}
                        </div>
                        <div style="font-size:11px; color:#64748B; margin-top:1px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                          ${med.composition ? escapeHtml(med.composition) + ' • ' : ''}<span style="color:#0EA5E9; font-weight:600;">${escapeHtml(category)}</span>
                        </div>
                      </div>
                      <div style="text-align:right; flex-shrink:0;">
                        <div style="font-size:13px; font-weight:800; color:#0EA5E9;">₹${price}</div>
                        ${parseFloat(mrp) > parseFloat(price) ? `<div style="font-size:10px; color:#94A3B8; text-decoration:line-through;">₹${mrp}</div>` : ''}
                      </div>
                    </a>`;
                } catch(e) {
                  console.error('Error rendering suggestion item:', e);
                }
              });

              html += `
                <a href="{{ url('/search') }}?q=${encodeURIComponent(query)}" style="display:block; text-align:center; padding:10px 14px; background:#F0F9FF; color:#0EA5E9; font-size:13px; font-weight:700; text-decoration:none; border-radius:0 0 14px 14px;">
                  Sabhi "${escapeHtml(query)}" results dekhein →
                </a>`;

              suggestionsBox.innerHTML = html;
            }
            suggestionsBox.style.display = 'block';
          })
          .catch(err => {
            console.error('Fetch error:', err);
          });
      }, 150);
    }

    searchInput.addEventListener('input', fetchSuggestions);
    searchInput.addEventListener('keyup', fetchSuggestions);

    // Close suggestions on outside click
    document.addEventListener('click', function(e) {
      if (searchWrapper && !searchWrapper.contains(e.target)) {
        suggestionsBox.style.display = 'none';
      }
    });

    searchInput.addEventListener('focus', function() {
      if (this.value.trim().length >= 1) {
        fetchSuggestions();
      }
    });

    function escapeHtml(str) {
      if (!str) return '';
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function highlightMatch(text, q) {
      if (!text || !q) return escapeHtml(text);
      const escaped = escapeHtml(text);
      const regex = new RegExp('(' + q.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + ')', 'gi');
      return escaped.replace(regex, '<mark style="background:#FEF08A; color:#000; padding:0 2px; border-radius:3px;">$1</mark>');
    }
  })();
</script>

