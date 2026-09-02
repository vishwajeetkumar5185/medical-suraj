@extends('layouts.app')

@section('content')
<div class="screen" style="align-items:center; justify-content:center; padding:30px 20px;">
  <div style="background:#fff; border-radius:20px; padding:28px; max-width:650px; width:100%; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #E5E7EB;">
    <div style="text-align:center; margin-bottom:24px;">
      <div style="font-size:36px; margin-bottom:10px;">🏪</div>
      <h2 style="font-weight:900; font-size:22px; color:#1A1A1A;">Pharmacy Partner Registration</h2>
      <p style="font-size:13px; color:#888; margin-top:4px;">Register your pharmacy store on Dawalo platform to start selling</p>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:10px 12px; font-size:12px; color:#DC2626; margin-bottom:16px;">
        @foreach($errors->all() as $error)
          <div>⚠️ {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ url('/register/shop') }}" method="POST" enctype="multipart/form-data">
      @csrf


      
      <!-- Layout columns grid using flexbox/grid styled inline -->
      <div style="display:flex; flex-direction:column; gap:24px;">
        <!-- Column 1: Owner (Account) details -->
        <div style="flex:1;">
          <h3 style="font-weight:800; font-size:14px; color:#1A3C8F; border-bottom:1px solid #E5E7EB; padding-bottom:6px; margin-bottom:12px;">👤 Owner Details</h3>
          
          <div style="margin-bottom:12px;">
            <label class="form-label">Owner Full Name</label>
            <input type="text" name="name" class="form-input" placeholder="e.g. Rajesh Sharma" value="{{ old('name') }}" required>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-input" placeholder="name@example.com" value="{{ old('email') }}" required>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-input" placeholder="e.g. 9876543210" value="{{ old('phone') }}" required>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="Min. 6 characters" required>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Retype password" required>
          </div>
        </div>

        <!-- Column 2: Shop details -->
        <div style="flex:1;">
          <h3 style="font-weight:800; font-size:14px; color:#1A3C8F; border-bottom:1px solid #E5E7EB; padding-bottom:6px; margin-bottom:12px;">🏪 Pharmacy Store Details</h3>
          
          <!-- Leaflet Interactive Registration Map block -->
          <div style="margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="form-label" style="margin-bottom:0;">📍 Pharmacy Map Location</label>
              <div style="display:flex; gap:6px;">
                <button type="button" onclick="toggleRegSatelliteView()" id="btn-reg-satellite" style="background:#4A5568; border:none; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; cursor:pointer;">🛰️ Satellite</button>
                <button type="button" onclick="toggleRegFullScreen()" id="btn-reg-fullscreen" style="background:#1A3C8F; border:none; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; cursor:pointer;">🗖 Full Screen</button>
              </div>
            </div>
            
            <div id="reg-leaflet-map" style="width:100%; height:220px; border-radius:12px; border:1px solid #CBD5E0; position:relative; z-index:9; transition: height 0.3s ease;"></div>
            <div style="font-size:11.5px; color:#4A5568; margin-top:6px; text-align:left; font-weight:600; line-height:1.4;">
              📍 Detected Address: <span id="reg-address-preview" style="color:#1A3C8F; font-weight:800;">Fetching location details...</span>
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Pharmacy Name</label>
            <input type="text" name="shop_name" class="form-input" placeholder="e.g. Sharma Medical Store" value="{{ old('shop_name') }}" required>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">City / Region Area Coverage</label>
            <select name="area" id="area-select" class="form-input" required>
              <option value="Muzaffarpur" {{ old('area') === 'Muzaffarpur' ? 'selected' : '' }}>Muzaffarpur</option>
              <option value="Patna" {{ old('area') === 'Patna' ? 'selected' : '' }}>Patna</option>
              <option value="Jaipur" {{ old('area') === 'Jaipur' ? 'selected' : '' }}>Jaipur</option>
              <option value="Darbhanga" {{ old('area') === 'Darbhanga' ? 'selected' : '' }}>Darbhanga</option>
            </select>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label class="form-label">State</label>
              <input type="text" name="state" id="state-input" class="form-input" placeholder="e.g. Bihar" value="{{ old('state') }}">
            </div>
            <div style="flex:1;">
              <label class="form-label">City</label>
              <input type="text" name="city" id="city-input" class="form-input" placeholder="e.g. Muzaffarpur" value="{{ old('city') }}">
            </div>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label class="form-label">Area / Locality</label>
              <input type="text" name="area_name" id="area-name-input" class="form-input" placeholder="e.g. Mithanpura" value="{{ old('area_name') }}">
            </div>
            <div style="flex:1;">
              <label class="form-label">PIN Code</label>
              <input type="text" name="pin_code" id="pin-input" class="form-input" placeholder="e.g. 842002" value="{{ old('pin_code') }}">
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Full Address</label>
            <input type="text" name="address" id="address-input" class="form-input" placeholder="Shop Address, Mithanpura" value="{{ old('address') }}" required>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label class="form-label">Opening Time</label>
              <input type="time" name="opens_at" class="form-input" value="{{ old('opens_at', '09:00') }}" required>
            </div>
            <div style="flex:1;">
              <label class="form-label">Closing Time</label>
              <input type="time" name="closes_at" class="form-input" value="{{ old('closes_at', '21:00') }}" required>
            </div>
          </div>

          <!-- Home Delivery Toggle -->
          <div style="background:#F8FAFF; border:1px solid #E0E7FF; border-radius:14px; padding:14px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-weight:800; font-size:13px; color:#1A1A1A; display:flex; align-items:center; gap:6px;">
                🛵 Home Delivery
              </div>
              <div style="font-size:11px; color:#666; margin-top:2px;">Pickup + Delivery dono available</div>
            </div>
            <label class="switch-container" style="position:relative; display:inline-block; width:44px; height:24px;">
              <input type="checkbox" name="delivery_enabled" value="1" checked style="opacity:0; width:0; height:0;" onchange="toggleRegDeliverySection(this.checked)">
              <span class="switch-slider" style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#10B981; transition:.4s; border-radius:24px;"></span>
            </label>
          </div>

          <!-- Delivery Charge Options Container -->
          <div id="reg-delivery-charges-container" style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:14px; padding:14px; margin-bottom:14px;">
            <div style="font-weight:800; font-size:13px; color:#1A1A1A; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
              💰 Delivery Charge
            </div>
            <div style="font-size:11px; color:#666; margin-bottom:12px;">Aap decide karein customer se kitna delivery charge lena hai</div>

            <!-- Buttons Selector -->
            <div style="display:flex; gap:8px; margin-bottom:12px;">
              <button type="button" id="reg-btn-del-free" onclick="setRegDeliveryType('free')" style="flex:1; padding:8px; border:1.5px solid #CBD5E1; background:#fff; border-radius:10px; font-weight:700; font-size:12px; color:#475569; cursor:pointer; outline:none;">🎁 Free</button>
              <button type="button" id="reg-btn-del-perkm" onclick="setRegDeliveryType('perkm')" style="flex:1; padding:8px; border:2px solid #1A3C8F; background:#EFF6FF; border-radius:10px; font-weight:700; font-size:12px; color:#1A3C8F; cursor:pointer; outline:none;">🖊️ Per KM</button>
              <button type="button" id="reg-btn-del-fixed" onclick="setRegDeliveryType('fixed')" style="flex:1; padding:8px; border:1.5px solid #CBD5E1; background:#fff; border-radius:10px; font-weight:700; font-size:12px; color:#475569; cursor:pointer; outline:none;">📦 Fixed</button>
            </div>

            <!-- Value Input -->
            <input type="hidden" name="delivery_charge_type" id="reg-del-type" value="dynamic">
            
            <div id="reg-del-rate-wrapper">
              <label class="form-label" id="reg-del-rate-label" style="font-size:11.5px; font-weight:700; color:#555; margin-bottom:4px; display:block;">Rate per KM (₹)</label>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-weight:800; color:#1A1A1A; font-size:14px;">₹</span>
                <input type="number" step="0.5" name="delivery_charge_rate" id="reg-del-rate-input" class="form-input" style="flex:1; padding:8px 10px; font-size:13px;" value="8">
                <span style="font-size:12px; color:#666;" id="reg-del-rate-suffix">/ km</span>
              </div>
              <div style="font-size:11px; color:#888; margin-top:6px;" id="reg-del-example">Example: 3 km &times; ₹8 = ₹24 delivery charge</div>
            </div>
          </div>

          <!-- Discount Offers (Customer Savings) -->
          <div style="background:#FFFDF5; border:1px solid #FEF3C7; border-radius:14px; padding:14px; margin-bottom:14px;">
            <div style="font-weight:800; font-size:13px; color:#B45309; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
              🎁 Discount Offers (Customer Savings)
            </div>
            <div style="display:flex; gap:10px;">
              <div style="flex:1;">
                <label class="form-label" style="font-size:11.5px; font-weight:700; color:#92400E; margin-bottom:4px; display:block;">Min Bill Amount (₹)</label>
                <input type="number" step="any" name="offer_min_bill" class="form-input" style="padding:8px 10px; font-size:13px;" value="{{ old('offer_min_bill', '0.00') }}">
              </div>
              <div style="flex:1;">
                <label class="form-label" style="font-size:11.5px; font-weight:700; color:#92400E; margin-bottom:4px; display:block;">Discount Percentage (%)</label>
                <input type="number" step="any" name="offer_discount_pct" class="form-input" style="padding:8px 10px; font-size:13px;" value="{{ old('offer_discount_pct', '0.00') }}">
              </div>
            </div>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label class="form-label">Latitude</label>
              <input type="number" step="any" name="latitude" id="lat-input" class="form-input" placeholder="26.1209" value="{{ old('latitude', '26.1209') }}" required>
            </div>
            <div style="flex:1;">
              <label class="form-label">Longitude</label>
              <input type="number" step="any" name="longitude" id="lng-input" class="form-input" placeholder="85.3647" value="{{ old('longitude', '85.3647') }}" required>
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="form-label">Shop Image (Store Photo)</label>
            <input type="file" name="shop_image" class="form-input" accept="image/*">
          </div>
        </div>
      </div>

      <button type="submit" class="btn-blue" style="width:100%; border:none; padding:14px; border-radius:12px; font-weight:800; font-size:15px; color:#fff; cursor:pointer; margin-top:20px; box-shadow: 0 4px 12px rgba(37,99,235,0.2);">
        Register Pharmacy & Launch
      </button>
    </form>

    <div style="text-align:center; margin-top:20px; font-size:13px; color:#666;">
      Pehle se account hai? <a href="{{ url('/login') }}" style="color:#2563EB; font-weight:700; text-decoration:none;">Login Karein</a>
    </div>
  </div>
</div>

<script>
let regMap;
let regMarker;
let regStreetLayer;
let regSatelliteLayer;
let regActiveLayer = 'street';
let isRegMapFullScreen = false;

// Initialize Leaflet Map on load
window.addEventListener('DOMContentLoaded', () => {
  initRegistrationMap();
});

function initRegistrationMap() {
  // Default centered Jaipur coordinates
  regMap = L.map('reg-leaflet-map', { zoomControl: true }).setView([26.9124, 75.7873], 12);

  regStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  });

  regSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19,
    attribution: 'Tiles &copy; Esri'
  });

  // Add default layer
  regStreetLayer.addTo(regMap);

  // Add draggable Marker
  regMarker = L.marker([26.9124, 75.7873], { draggable: true }).addTo(regMap);

  // Event handlers
  regMarker.on('dragend', function(e) {
    const pos = regMarker.getLatLng();
    updateRegFieldsFromCoordinates(pos.lat, pos.lng);
  });

  regMap.on('click', function(e) {
    regMarker.setLatLng(e.latlng);
    updateRegFieldsFromCoordinates(e.latlng.lat, e.latlng.lng);
  });

  // Try GPS auto detect coordinates on load
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      regMap.setView([lat, lng], 16);
      regMarker.setLatLng([lat, lng]);
      updateRegFieldsFromCoordinates(lat, lng);
    }, err => {
      console.warn("GPS lookup denied or failed, using Jaipur default center");
      updateRegFieldsFromCoordinates(26.9124, 75.7873);
    });
  } else {
    updateRegFieldsFromCoordinates(26.9124, 75.7873);
  }
}

function updateRegFieldsFromCoordinates(lat, lng) {
  document.getElementById('lat-input').value = lat.toFixed(6);
  document.getElementById('lng-input').value = lng.toFixed(6);
  document.getElementById('reg-address-preview').innerText = "Fetching address details...";

  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
    headers: { 'Accept-Language': 'en' }
  })
    .then(res => res.json())
    .then(data => {
      if (data && data.address) {
        const addr = data.address;
        
        // Parse State, City, Locality, Pincode
        const stateVal = addr.state || '';
        
        // Dynamic City/District parsing logic
        // Prioritizing official District levels first so it resolves to 'Jaipur' rather than town blocks like 'Sanganer'
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

        // Target map registered landmark name (building, amenity, shop, commercial, road name details)
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

        // Fill form fields
        document.getElementById('state-input').value = stateVal;
        document.getElementById('city-input').value = cityClean;
        document.getElementById('area-name-input').value = addr.suburb || addr.neighbourhood || addr.residential || addr.road || addr.city_district || '';
        document.getElementById('pin-input').value = addr.postcode || '';
        document.getElementById('address-input').value = displayLoc;
        document.getElementById('reg-address-preview').innerText = displayLoc;

        // Auto select area coverage selector
        const areaSelect = document.getElementById('area-select');
        const searchStr = displayLoc.toLowerCase();
        
        // Dynamically add and select the new city to coverage dropdown options if it doesn't exist
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
    })
    .catch(err => {
      console.error(err);
      document.getElementById('reg-address-preview').innerText = "Reverse lookup failed.";
    });
}

function toggleRegSatelliteView() {
  if (!regMap) return;
  const btn = document.getElementById('btn-reg-satellite');
  if (regActiveLayer === 'street') {
    regMap.removeLayer(regStreetLayer);
    regSatelliteLayer.addTo(regMap);
    regActiveLayer = 'satellite';
    btn.innerHTML = '🗺️ Street View';
    btn.style.background = '#3182CE';
  } else {
    regMap.removeLayer(regSatelliteLayer);
    regStreetLayer.addTo(regMap);
    regActiveLayer = 'street';
    btn.innerHTML = '🛰️ Satellite';
    btn.style.background = '#4A5568';
  }
}

function toggleRegFullScreen() {
  const mapDiv = document.getElementById('reg-leaflet-map');
  const btn = document.getElementById('btn-reg-fullscreen');
  if (!isRegMapFullScreen) {
    mapDiv.style.height = '480px';
    btn.innerHTML = '🗗 Minimize';
    btn.style.background = '#E53E3E';
    isRegMapFullScreen = true;
  } else {
    mapDiv.style.height = '220px';
    btn.innerHTML = '🗖 Full Screen';
    btn.style.background = '#1A3C8F';
    isRegMapFullScreen = false;
  }
  setTimeout(() => {
    if (regMap) regMap.invalidateSize();
  }, 300);
}

function toggleRegDeliverySection(checked) {
  const container = document.getElementById('reg-delivery-charges-container');
  if (checked) {
    container.style.display = 'block';
  } else {
    container.style.display = 'none';
  }
}

function setRegDeliveryType(type) {
  const btnFree = document.getElementById('reg-btn-del-free');
  const btnPerKm = document.getElementById('reg-btn-del-perkm');
  const btnFixed = document.getElementById('reg-btn-del-fixed');
  const typeInput = document.getElementById('reg-del-type');
  const rateWrapper = document.getElementById('reg-del-rate-wrapper');
  const rateLabel = document.getElementById('reg-del-rate-label');
  const rateInput = document.getElementById('reg-del-rate-input');
  const rateSuffix = document.getElementById('reg-del-rate-suffix');
  const rateExample = document.getElementById('reg-del-example');

  // Reset styles
  [btnFree, btnPerKm, btnFixed].forEach(btn => {
    btn.style.border = '1.5px solid #CBD5E1';
    btn.style.background = '#fff';
    btn.style.color = '#475569';
  });

  if (type === 'free') {
    btnFree.style.border = '2px solid #1A3C8F';
    btnFree.style.background = '#EFF6FF';
    btnFree.style.color = '#1A3C8F';
    typeInput.value = 'fixed';
    rateWrapper.style.display = 'none';
    rateInput.value = '0';
  } else if (type === 'perkm') {
    btnPerKm.style.border = '2px solid #1A3C8F';
    btnPerKm.style.background = '#EFF6FF';
    btnPerKm.style.color = '#1A3C8F';
    typeInput.value = 'dynamic';
    rateWrapper.style.display = 'block';
    rateLabel.innerText = 'Rate per KM (₹)';
    rateInput.value = '8';
    rateSuffix.innerText = '/ km';
    rateExample.innerText = 'Example: 3 km × ₹8 = ₹24 delivery charge';
  } else if (type === 'fixed') {
    btnFixed.style.border = '2px solid #1A3C8F';
    btnFixed.style.background = '#EFF6FF';
    btnFixed.style.color = '#1A3C8F';
    typeInput.value = 'fixed';
    rateWrapper.style.display = 'block';
    rateLabel.innerText = 'Fixed Delivery Charge (₹)';
    rateInput.value = '20';
    rateSuffix.innerText = 'flat';
    rateExample.innerText = 'Flat rate applies regardless of distance.';
  }
}
</script>

<style>
  .switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }
  input:checked + .switch-slider {
    background-color: #1A3C8F;
  }
  input:checked + .switch-slider:before {
    transform: translateX(20px);
  }
</style>
@endsection
