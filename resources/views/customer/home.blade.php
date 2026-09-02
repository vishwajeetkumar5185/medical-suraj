@extends('layouts.app')

@section('seo_title', 'Dawalo 💊 - Online Medicine Aggregator & Home Delivery in Bihar')
@section('seo_description', 'Search medicines, verify stock at nearby local pharmacies, and get home deliveries in 45 minutes with Bihar\'s most reliable local medicine aggregator.')
@section('seo_keywords', 'dawalo, online medicine store, online pharmacy Bihar, check medicine stock, medicine delivery Patna, medicine delivery Muzaffarpur, buy generic medicine')

@section('content')
<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
  .screen { overflow: visible !important; height: auto !important; min-height: 100vh !important; }
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
      <div id="home-search-autocomplete" style="display:none; position:absolute; left:16px; right:16px; background:#fff; border-radius:12px; margin-top:8px; box-shadow:0 8px 24px rgba(0,0,0,0.12); max-height:300px; overflow-y:auto; z-index:9999;"></div>
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
            <div style="background:#FEF3C7; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">🤧</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Cold & Cough</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Fever') }}" style="text-decoration:none;">
            <div style="background:#DBEAFE; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">🌡️</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Fever & Pain</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Pain') }}" style="text-decoration:none;">
            <div style="background:#FECACA; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">💊</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Pain Relief</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Heart') }}" style="text-decoration:none;">
            <div style="background:#FED7D7; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">❤️</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Heart Care</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Diabetes') }}" style="text-decoration:none;">
            <div style="background:#FED7E2; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">🩸</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Diabetic</div>
            </div>
          </a>
          <a href="{{ url('/search?q=Blood Pressure') }}" style="text-decoration:none;">
            <div style="background:#E0E7FF; border-radius:16px; padding:20px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:110px;">
              <div style="font-size:40px; margin-bottom:8px;">🩺</div>
              <div style="font-size:12px; font-weight:700; color:#1A1A1A; text-align:center;">Blood Pressure</div>
            </div>
          </a>
        </div>
      </div>

      <!-- Popular Dawaiyan -->
      <div style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <h3 style="font-size:17px; font-weight:800; color:#1A1A1A; margin:0;">Popular Dawaiyan</h3>
          <a href="{{ url('/popular-medicines') }}" style="color:#3B82F6; font-size:13px; font-weight:700; text-decoration:none;">View All ›</a>
        </div>
        
        <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:8px;">
          @foreach($popularMedicines as $index => $medicine)
          <div style="min-width:140px; background:#fff; border-radius:14px; padding:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); position:relative;">
            @if($index == 0)
              <div style="position:absolute; top:8px; left:8px; background:#10B981; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px;">10% OFF</div>
            @endif
            <a href="{{ url('/medicine/'.$medicine->id) }}" style="text-decoration:none; display:block;">
              <div style="width:100%; height:80px; background:#F3F4F6; border-radius:10px; margin-bottom:10px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                @if($medicine->images)
                  @php
                    $images = is_array($medicine->images) ? $medicine->images : json_decode($medicine->images, true);
                    $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
                  @endphp
                  @if($firstImage)
                    <img src="{{ asset($firstImage) }}" style="width:100%; height:100%; object-fit:contain;" alt="{{ $medicine->name }}">
                  @else
                    <span style="font-size:48px;">{{ $medicine->emoji ?? '💊' }}</span>
                  @endif
                @else
                  <span style="font-size:48px;">{{ $medicine->emoji ?? '💊' }}</span>
                @endif
              </div>
              <div style="font-size:13px; font-weight:800; color:#1A1A1A; margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $medicine->name }}</div>
              <div style="display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                @if($medicine->mrp && $medicine->price < $medicine->mrp)
                  <span style="font-size:14px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
                  <span style="font-size:11px; color:#94A3B8; text-decoration:line-through;">₹{{ number_format($medicine->mrp, 0) }}</span>
                @else
                  <span style="font-size:14px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
                @endif
              </div>
            </a>
            <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0;">
              @csrf
              <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" style="width:100%; background:#3B82F6; color:#fff; border:none; border-radius:8px; padding:8px; font-size:12px; font-weight:700; cursor:pointer;">+ Add</button>
            </form>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Nearby Open Pharmacies Section -->
      <div style="margin-bottom:80px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <div style="width:10px; height:10px; background:#22C55E; border-radius:50%; box-shadow:0 0 0 3px rgba(34,197,94,0.3);"></div>
            <h3 style="font-size:17px; font-weight:800; color:#1A1A1A; margin:0;">Nearby Pharmacies</h3>
          </div>
          <a href="{{ url('/nearby-pharmacies') }}" style="color:#3B82F6; font-size:13px; font-weight:700; text-decoration:none;">View All ›</a>
        </div>
        
        <!-- Pharmacy Cards -->
        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($shops->take(5) as $shop)
          <a href="{{ url('/search?shop_id='.$shop->id) }}" style="text-decoration:none;">
            <div style="background:#fff; border-radius:16px; padding:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); display:flex; gap:12px; align-items:center;">
              <div style="width:56px; height:56px; background:#EEF2FF; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0;">
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
                  @if(isset($shop->distance) && $shop->distance < 9999)
                    <span style="color:#3B82F6; font-weight:700;"> • {{ number_format($shop->distance, 1) }} km</span>
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
    <a href="{{ url('/profile/orders') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">📋</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Order</span>
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

    searchTimeout = setTimeout(() => {
      fetch(`{{ url('/medicines/search') }}?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          dropdown.innerHTML = '';
          if (data.length === 0) {
            dropdown.style.display = 'none';
            return;
          }
          dropdown.style.display = 'block';
          data.forEach(item => {
            const row = document.createElement('div');
            row.style.cssText = 'padding:14px 16px; cursor:pointer; border-bottom:1px solid #F3F4F6; display:flex; align-items:center; gap:10px;';
            row.innerHTML = `<span style="font-size:20px;">${item.emoji || '💊'}</span> <div><div style="font-size:14px; font-weight:700; color:#1A1A1A;">${item.name}</div><div style="font-size:11px; color:#64748B;">${item.category}</div></div>`;
            row.addEventListener('click', () => {
              document.getElementById('home-search-input').value = item.name;
              dropdown.style.display = 'none';
              triggerHomeSearch();
            });
            dropdown.appendChild(row);
          });
        })
        .catch(err => console.error(err));
    }, 300);
  }

  function triggerHomeSearch() {
    const q = document.getElementById('home-search-input').value.trim();
    if (q) {
      window.location.href = "{{ url('/search') }}?q=" + encodeURIComponent(q);
    }
  }

  // Get user's location on page load
  window.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
      // Try with high accuracy first
      navigator.geolocation.getCurrentPosition(
        function(position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          console.log('Location detected:', lat, lng);
          
          // Save location first (works even without reverse geocoding)
          fetch("{{ url('/set-location') }}?city=Your Location&lat=" + lat + "&lng=" + lng)
            .then(() => {
              // Reload page to show updated distances
              window.location.reload();
            })
            .catch(err => console.log('Location save failed:', err));
        }, 
        function(error) {
          console.log('GPS blocked or failed, using default location');
          // Use default location if GPS fails
          const locationElements = document.querySelectorAll('[data-location-display]');
          locationElements.forEach(el => {
            if (el.textContent === 'Detecting...') {
              el.textContent = '{{ session("user_location", "Muzaffarpur") }}';
            }
          });
        },
        {
          enableHighAccuracy: false,
          timeout: 5000,
          maximumAge: 300000
        }
      );
    }
  });
</script>

