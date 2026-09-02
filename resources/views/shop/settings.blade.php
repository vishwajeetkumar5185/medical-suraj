@extends('layouts.app')

@section('content')
<div class="screen">
  <!-- Shop Dashboard Header -->
  <div class="hdr-gradient" style="padding:24px 20px 24px; position:relative; overflow:hidden; flex-shrink:0; border-radius: 20px; margin-bottom:20px;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; position:relative; z-index:1;">
      <a href="{{ url('/profile') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <div style="flex:1;">
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">{{ $shop->name }}</h2>
        <p style="color:rgba(255,255,255,0.75); font-size:12px; margin:0;">📍 {{ $shop->area }} • Store Settings</p>
      </div>
    </div>

    <!-- Toggles (Online Status, Delivery) -->
    <div style="display:flex; gap:10px; position:relative; z-index:1;">
      <!-- Online/Offline Switch -->
      <form action="{{ url('/shop/toggle-online') }}" method="POST" style="flex:1;">
        @csrf
        <button type="submit" style="width:100%; border:none; text-align:left; background:none; padding:0; cursor:pointer; font-family:inherit;">
          @php
            $online = (bool) $shop->is_online;
            $bg = $online ? 'rgba(34,197,94,0.25)' : 'rgba(255,255,255,0.12)';
            $border = $online ? '1.5px solid rgba(34,197,94,0.6)' : '1.5px solid rgba(255,255,255,0.2)';
          @endphp
          <div style="border-radius:16px; padding:12px 14px; background:{{ $bg }}; border:{{ $border }}; display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:10px; background:{{ $online ? '#22C55E' : 'rgba(255,255,255,0.2)' }}; display:flex; align-items:center; justify-content:center; font-size:18px;">
              {{ $online ? '🟢' : '🔴' }}
            </div>
            <div>
              <div style="color:#fff; font-weight:900; font-size:13px;">{{ $online ? 'Shop Online' : 'Shop Offline' }}</div>
              <div style="color:rgba(255,255,255,0.7); font-size:10px; margin-top:1px;">{{ $online ? 'Orders aa rahe hain' : 'Orders band hain' }}</div>
            </div>
          </div>
        </button>
      </form>

      <!-- Delivery Switch -->
      <form action="{{ url('/shop/toggle-delivery') }}" method="POST" style="flex:1;">
        @csrf
        <button type="submit" style="width:100%; border:none; text-align:left; background:none; padding:0; cursor:pointer; font-family:inherit;">
          @php
            $deliv = (bool) $shop->delivery_enabled;
            $bg = $deliv ? 'rgba(37,99,235,0.3)' : 'rgba(255,255,255,0.12)';
            $border = $deliv ? '1.5px solid rgba(96,165,250,0.6)' : '1.5px solid rgba(255,255,255,0.2)';
          @endphp
          <div style="border-radius:16px; padding:12px 14px; background:{{ $bg }}; border:{{ $border }}; display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:10px; background:{{ $deliv ? '#2563EB' : 'rgba(255,255,255,0.2)' }}; display:flex; align-items:center; justify-content:center; font-size:18px;">
              🛵
            </div>
            <div>
              <div style="color:#fff; font-weight:900; font-size:13px;">Delivery {{ $deliv ? 'ON' : 'OFF' }}</div>
              <div style="color:rgba(255,255,255,0.7); font-size:10px; margin-top:1px;">{{ $deliv ? 'Home delivery active' : 'Sirf pickup' }}</div>
            </div>
          </div>
        </button>
      </form>
    </div>
  </div>

  <!-- Dashboard Navigation Menu Bar -->
  <div style="display:flex; background:#fff; padding:10px 10px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:20px;">
    <a href="{{ url('/shop/dashboard') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📊</span>Overview
    </a>
    <a href="{{ url('/shop/quicksetup') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">⚡</span>Quick Setup
    </a>
    <a href="{{ url('/shop/inventory') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📦</span>Inventory
    </a>
    <a href="{{ url('/shop/orders') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📋</span>Orders
    </a>
    <a href="{{ url('/shop/settings') }}" class="dash-tab active" style="background:#1A3C8F; color:#fff; flex:1; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
      <span style="font-size:16px;">⚙️</span>Settings
    </a>
  </div>

  <!-- Content scroll pane -->
  <div class="scroll" style="flex:1; padding-bottom:20px;">
    <div class="responsive-grid">

      <!-- Store Image Upload Card -->
      <div style="background:#fff; border-radius:20px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #F3F4F6; margin:0; grid-column: 1 / -1;">
        <h4 style="font-weight:900; font-size:14px; color:#1A3C8F; margin-top:0; margin-bottom:12px; display:flex; align-items:center; gap:6px;">📸 Store Photo</h4>
        
        <div style="display:flex; gap:16px; align-items:center; margin-bottom:16px; background:#F8FAFF; padding:12px; border-radius:14px; border:1px dashed #E0E7FF;">
          <div style="width:70px; height:70px; border-radius:12px; background:#fff; flex-shrink:0; overflow:hidden; border:1px solid #E5E7EB; display:flex; align-items:center; justify-content:center;">
            @if($shop->image)
              <img src="{{ asset($shop->image) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
              <span style="font-size:32px;">🏪</span>
            @endif
          </div>
          <div>
            <div style="font-weight:850; font-size:13px; color:#1A1A1A;">Current Pharmacy Photo</div>
            <div style="font-size:11px; color:#666; margin-top:2px;">Dukan ki image customer ko home page par dikhegi.</div>
          </div>
        </div>

        <form action="{{ url('/shop/update-image') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div style="margin-bottom:12px;">
            <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Choose New Photo:</label>
            <input type="file" name="shop_image" class="form-input" accept="image/*" style="padding:8px 10px;" required>
          </div>
          <button type="submit" class="btn-blue" style="width:100%; border:none; padding:12px; border-radius:10px; font-weight:800; font-size:13px; color:#fff; cursor:pointer;">
            Upload & Update Store Photo
          </button>
        </form>
      </div>
      
      <!-- Store Timings Settings Card -->
      <div style="background:#fff; border-radius:20px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #F3F4F6; margin:0; grid-column: 1 / -1;">
        <h4 style="font-weight:900; font-size:14px; color:#1A3C8F; margin-top:0; margin-bottom:12px; display:flex; align-items:center; gap:6px;">🕰️ Store Timings</h4>
        <form action="{{ url('/shop/update-timings') }}" method="POST">
          @csrf
          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Opening Time</label>
              <input type="time" name="opens_at" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->opens_at ?? '09:00' }}" required>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Closing Time</label>
              <input type="time" name="closes_at" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->closes_at ?? '21:00' }}" required>
            </div>
          </div>
          <button type="submit" class="btn-blue" style="width:100%; border:none; padding:12px; border-radius:10px; font-weight:800; font-size:13px; color:#fff; cursor:pointer;">
            Save Store Timings
          </button>
        </form>
      </div>

      <!-- Delivery & Offer Settings Card -->
      <div style="background:#fff; border-radius:20px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #F3F4F6; margin:0; grid-column: 1 / -1;">
        <h4 style="font-weight:900; font-size:14px; color:#1A3C8F; margin-top:0; margin-bottom:12px; display:flex; align-items:center; gap:6px;">⚙️ Delivery & Offer Settings</h4>
        <form action="{{ url('/shop/update-delivery-settings') }}" method="POST">
          @csrf
          
          <!-- Delivery Settings row -->
          <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px; border-bottom:1px solid #F3F4F6; padding-bottom:16px;">
            <div style="font-weight:800; font-size:12.5px; color:#374151;">🛵 Delivery Configuration</div>
            
            <div style="display:flex; gap:10px;">
              <div style="flex:1;">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Delivery Radius (KM)</label>
                <input type="number" step="0.1" name="delivery_radius_km" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->delivery_radius_km ?? '10' }}" required>
              </div>
              <div style="flex:1;">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Delivery Charge Type</label>
                <select name="delivery_charge_type" id="delivery_charge_type" class="form-input" style="padding:10px; font-size:13px; height:40px;" onchange="toggleDeliveryChargeInputs()" required>
                  <option value="fixed" {{ ($shop->delivery_charge_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Flat Rate</option>
                  <option value="dynamic" {{ ($shop->delivery_charge_type ?? 'dynamic') === 'dynamic' ? 'selected' : '' }}>Dynamic (Per KM)</option>
                </select>
              </div>
            </div>

            <div style="display:flex; gap:10px;">
              <div style="flex:1;" id="fixed-charge-wrapper">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Fixed Delivery Charge (₹)</label>
                <input type="number" step="0.5" name="delivery_charge_fixed" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->delivery_charge_fixed ?? '20' }}">
              </div>
              <div style="flex:1;" id="per-km-charge-wrapper">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Charge per KM (₹)</label>
                <input type="number" step="0.5" name="delivery_charge_per_km" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->delivery_charge_per_km ?? '8' }}">
              </div>
            </div>
          </div>

          <!-- Billing Offers row -->
          <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
            <div style="font-weight:800; font-size:12.5px; color:#374151;">🎁 Discount Offers (Customer Savings)</div>
            
            <div style="display:flex; gap:10px;">
              <div style="flex:1;">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Min Bill Amount (₹)</label>
                <input type="number" step="1" name="offer_min_bill" class="form-input" style="padding:10px; font-size:13px;" value="{{ $shop->offer_min_bill ?? '0' }}" required>
              </div>
              <div style="flex:1;">
                <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Discount Percentage (%)</label>
                <input type="number" step="0.5" name="offer_discount_pct" class="form-input" style="padding:10px; font-size:13px;" min="0" max="100" value="{{ $shop->offer_discount_pct ?? '0' }}" required>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-blue" style="width:100%; border:none; padding:12px; border-radius:10px; font-weight:800; font-size:13px; color:#fff; cursor:pointer;">
            Save Settings
          </button>
        </form>
      </div>

      <!-- Store Location & Map Coordinates settings card -->
      <div style="background:#fff; border-radius:20px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #F3F4F6; margin:0; grid-column: 1 / -1;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <h4 style="font-weight:900; font-size:14px; color:#1A3C8F; margin:0; display:flex; align-items:center; gap:6px;">📍 Store Location & Map</h4>
          <div style="display:flex; gap:6px;">
            <button type="button" onclick="toggleSettingsSatelliteView()" id="btn-settings-satellite" style="background:#4A5568; border:none; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; cursor:pointer;">🛰️ Satellite</button>
            <button type="button" onclick="toggleSettingsFullScreen()" id="btn-settings-fullscreen" style="background:#1A3C8F; border:none; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; cursor:pointer;">🗖 Full Screen</button>
          </div>
        </div>

        <form action="{{ url('/shop/update-location') }}" method="POST">
          @csrf
          <div style="margin-bottom:12px;">
            <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">City / Region Area Coverage</label>
            <select name="area" id="area-select" class="form-input" required>
              <option value="Jaipur" {{ $shop->area === 'Jaipur' ? 'selected' : '' }}>Jaipur</option>
              <option value="Muzaffarpur" {{ $shop->area === 'Muzaffarpur' ? 'selected' : '' }}>Muzaffarpur</option>
              <option value="Patna" {{ $shop->area === 'Patna' ? 'selected' : '' }}>Patna</option>
              <option value="Darbhanga" {{ $shop->area === 'Darbhanga' ? 'selected' : '' }}>Darbhanga</option>
            </select>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label" style="font-size:11px; margin-bottom:4px; display:block; font-weight:700; color:#555;">Complete Shop Address</label>
            <textarea name="address" id="address-textarea" class="form-input" style="height:56px; resize:none; font-family:inherit;" required>{{ $shop->address }}</textarea>
          </div>

          <div id="settings-leaflet-map" style="height:220px; border-radius:12px; border:1px solid #CBD5E0; margin-bottom:12px; z-index:9; transition: height 0.3s ease;"></div>

          <div style="font-size:11.5px; color:#4A5568; margin-bottom:12px; text-align:left; font-weight:600; line-height:1.4;">
            📍 Detected Map Address: <span id="settings-address-preview" style="color:#1A3C8F; font-weight:800;">{{ $shop->address }}</span>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:14px;">
            <div style="flex:1;">
              <label class="form-label" style="font-size:10.5px; font-weight:700; color:#666; margin-bottom:3px;">Latitude</label>
              <input type="text" name="latitude" id="settings-lat" class="form-input" style="background:#F3F4F6;" value="{{ $shop->latitude }}" readonly required>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-size:10.5px; font-weight:700; color:#666; margin-bottom:3px;">Longitude</label>
              <input type="text" name="longitude" id="settings-lng" class="form-input" style="background:#F3F4F6;" value="{{ $shop->longitude }}" readonly required>
            </div>
          </div>

          <button type="submit" class="btn-blue" style="width:100%; border:none; padding:12px; border-radius:10px; font-weight:800; font-size:13px; color:#fff; cursor:pointer;">
            Save Location & Coordinates
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
  function toggleDeliveryChargeInputs() {
    const typeSelect = document.getElementById('delivery_charge_type');
    const fixedWrapper = document.getElementById('fixed-charge-wrapper');
    const perKmWrapper = document.getElementById('per-km-charge-wrapper');
    
    if (typeSelect.value === 'fixed') {
      fixedWrapper.style.display = 'block';
      perKmWrapper.style.display = 'none';
    } else {
      fixedWrapper.style.display = 'none';
      perKmWrapper.style.display = 'block';
    }
  }

  // Trigger on load
  document.addEventListener('DOMContentLoaded', () => {
    toggleDeliveryChargeInputs();
    initSettingsMap();
  });

  let settingsMap;
  let settingsMarker;
  let settingsStreetLayer;
  let settingsSatelliteLayer;
  let settingsActiveLayer = 'street';
  let isSettingsMapFullScreen = false;

  function initSettingsMap() {
    // Current coordinates from DB model shop properties
    const startLat = parseFloat("{{ $shop->latitude ?? 26.9124 }}");
    const startLng = parseFloat("{{ $shop->longitude ?? 75.7873 }}");

    settingsMap = L.map('settings-leaflet-map', { zoomControl: true }).setView([startLat, startLng], 15);
    
    settingsStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    });

    settingsSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles &copy; Esri'
    });

    // Add default street layer
    settingsStreetLayer.addTo(settingsMap);

    // Add draggable marker
    settingsMarker = L.marker([startLat, startLng], { draggable: true }).addTo(settingsMap);

    // Marker drag end handler
    settingsMarker.on('dragend', function(e) {
      const pos = settingsMarker.getLatLng();
      updateSettingsCoordsFields(pos.lat, pos.lng);
    });

    // Map click handler
    settingsMap.on('click', function(e) {
      settingsMarker.setLatLng(e.latlng);
      updateSettingsCoordsFields(e.latlng.lat, e.latlng.lng);
    });

    // Get live location of the shop owner on page load to snap the map marker
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(position => {
        const liveLat = position.coords.latitude;
        const liveLng = position.coords.longitude;
        settingsMap.setView([liveLat, liveLng], 15);
        settingsMarker.setLatLng([liveLat, liveLng]);
        updateSettingsCoordsFields(liveLat, liveLng);
      }, err => {
        console.warn("Live location lookup failed, using current DB coordinate values.");
      });
    }
  }

  function updateSettingsCoordsFields(lat, lng) {
    document.getElementById('settings-lat').value = lat.toFixed(6);
    document.getElementById('settings-lng').value = lng.toFixed(6);
    document.getElementById('settings-address-preview').innerText = "Fetching address details...";

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
      headers: { 'Accept-Language': 'en' }
    })
      .then(res => res.json())
      .then(data => {
        if (data && data.address) {
          const addr = data.address;
          
          let cityClean = '';
          if (addr.district) {
              cityClean = addr.district;
          } else if (addr.city_district) {
              cityClean = addr.city_district;
          } else if (addr.state_district) {
              cityClean = addr.state_district;
          } else if (addr.county) {
              cityClean = addr.county;
          } else if (addr.city) {
              cityClean = addr.city;
          } else if (addr.town) {
              cityClean = addr.town;
          } else if (addr.village) {
              cityClean = addr.village;
          } else {
              cityClean = 'Jaipur';
          }
          cityClean = cityClean.replace(/\s+(District|Division|City|Tehsil)/i, '').trim();

          let displayLoc = '';
          if (data.display_name) {
              let parts = data.display_name.split(',').map(p => p.trim());
              let filteredParts = parts.filter(p => {
                  let lower = p.toLowerCase();
                  return !lower.includes('india') && 
                         !lower.includes('rajasthan') && 
                         !lower.match(/^\d{6}$/) && 
                         lower !== cityClean.toLowerCase();
              });
              if (filteredParts.length > 0) {
                  displayLoc = filteredParts.slice(0, 2).join(', ') + ', ' + cityClean;
              }
          }

          if (!displayLoc) {
              let landmark = data.name || addr.amenity || addr.shop || addr.building || addr.commercial || addr.office || '';
              let road = addr.road || addr.suburb || addr.neighbourhood || '';
              if (landmark) displayLoc += landmark.trim();
              if (road) displayLoc += (displayLoc ? ', ' : '') + road.trim();
              if (!displayLoc) {
                  displayLoc = cityClean;
              } else {
                  displayLoc += ', ' + cityClean;
              }
          }

          // Fill settings location address textareas and labels
          document.getElementById('address-textarea').value = displayLoc;
          document.getElementById('settings-address-preview').innerText = displayLoc;

          // Auto update area selectors
          const areaSelect = document.getElementById('area-select');
          if (areaSelect) {
            let hasOption = false;
            for (let i = 0; i < areaSelect.options.length; i++) {
              if (areaSelect.options[i].value.toLowerCase() === cityClean.toLowerCase()) {
                areaSelect.selectedIndex = i;
                hasOption = true;
                break;
              }
            }
            if (!hasOption && cityClean) {
              const newOpt = new Option(cityClean, cityClean, true, true);
              areaSelect.add(newOpt);
            }
          }
        }
      })
      .catch(err => {
        console.error(err);
        document.getElementById('settings-address-preview').innerText = "Reverse lookup failed.";
      });
  }

  function toggleSettingsSatelliteView() {
    if (!settingsMap) return;
    const btn = document.getElementById('btn-settings-satellite');
    if (settingsActiveLayer === 'street') {
      settingsMap.removeLayer(settingsStreetLayer);
      settingsSatelliteLayer.addTo(settingsMap);
      settingsActiveLayer = 'satellite';
      btn.innerHTML = '🗺️ Street View';
      btn.style.background = '#3182CE';
    } else {
      settingsMap.removeLayer(settingsSatelliteLayer);
      settingsStreetLayer.addTo(settingsMap);
      settingsActiveLayer = 'street';
      btn.innerHTML = '🛰️ Satellite';
      btn.style.background = '#4A5568';
    }
  }

  function toggleSettingsFullScreen() {
    const mapDiv = document.getElementById('settings-leaflet-map');
    const btn = document.getElementById('btn-settings-fullscreen');
    if (!isSettingsMapFullScreen) {
      mapDiv.style.height = '480px';
      btn.innerHTML = '🗗 Minimize';
      btn.style.background = '#E53E3E';
      isSettingsMapFullScreen = true;
    } else {
      mapDiv.style.height = '220px';
      btn.innerHTML = '🗖 Full Screen';
      btn.style.background = '#1A3C8F';
      isSettingsMapFullScreen = false;
    }
    setTimeout(() => {
      if (settingsMap) settingsMap.invalidateSize();
    }, 300);
  }
</script>
@endsection
