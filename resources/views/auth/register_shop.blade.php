@extends('layouts.app')

@section('seo_title', 'Pharmacy Partner Registration - Dawalo')
@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
  
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
    background-color: #10B981;
  }
  input:checked + .switch-slider:before {
    transform: translateX(20px);
  }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:40px;">
  
  <!-- Header -->
  <div style="background:linear-gradient(180deg, #10B981 0%, #059669 100%); padding:16px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url('/login') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">🏪 Pharmacy Partner</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:12px; margin:0;">Register your medical store</p>
      </div>
    </div>
  </div>

  <!-- Registration Form -->
  <div style="padding:20px; max-width:680px; margin:0 auto;">
    
    <!-- Welcome Card -->
    <div style="background:#fff; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-align:center;">
      <div style="font-size:56px; margin-bottom:12px;">🏪</div>
      <h2 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:6px;">Partner with Dawalo</h2>
      <p style="font-size:13px; color:#64748B; line-height:1.5;">Register your pharmacy and start reaching more customers in your area.</p>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2; border-left:4px solid #EF4444; border-radius:12px; padding:14px 16px; margin-bottom:16px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
        @foreach($errors->all() as $error)
          <div style="font-size:13px; color:#DC2626; font-weight:600; margin-bottom:4px;">⚠️ {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ url('/register/shop') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- Owner Details Card -->
      <div style="background:#fff; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size:16px; font-weight:800; color:#10B981; margin-bottom:16px; display:flex; align-items:center; gap:8px; border-bottom:2px solid #D1FAE5; padding-bottom:8px;">
          <span>👤</span> Owner Details
        </h3>
        
        <!-- Owner Name -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">👤 Owner Full Name</label>
          <input 
            type="text" 
            name="name" 
            value="{{ old('name') }}"
            placeholder="e.g. Rajesh Sharma" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#10B981';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Email & Phone -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📧 Email</label>
            <input 
              type="email" 
              name="email" 
              value="{{ old('email') }}"
              placeholder="name@example.com" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📱 Phone</label>
            <input 
              type="text" 
              name="phone" 
              value="{{ old('phone') }}"
              placeholder="9876543210" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
        </div>

        <!-- Password & Confirm -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🔒 Password</label>
            <input 
              type="password" 
              name="password" 
              placeholder="Min. 6 characters" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🔒 Confirm</label>
            <input 
              type="password" 
              name="password_confirmation" 
              placeholder="Retype password" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
        </div>
      </div>

      <!-- Pharmacy Details Card -->
      <div style="background:#fff; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size:16px; font-weight:800; color:#10B981; margin-bottom:16px; display:flex; align-items:center; gap:8px; border-bottom:2px solid #D1FAE5; padding-bottom:8px;">
          <span>🏪</span> Pharmacy Store Details
        </h3>
        
        <!-- Map Location -->
        <div style="margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <label style="font-size:13px; font-weight:700; color:#374151;">📍 Map Location</label>
            <div style="display:flex; gap:6px;">
              <button type="button" onclick="toggleRegSatelliteView()" id="btn-reg-satellite" style="background:#64748B; border:none; color:#fff; font-size:10px; font-weight:700; padding:6px 10px; border-radius:6px; cursor:pointer;">🛰️ Satellite</button>
              <button type="button" onclick="toggleRegFullScreen()" id="btn-reg-fullscreen" style="background:#10B981; border:none; color:#fff; font-size:10px; font-weight:700; padding:6px 10px; border-radius:6px; cursor:pointer;">🗖 Full</button>
            </div>
          </div>
          
          <div id="reg-leaflet-map" style="width:100%; height:220px; border-radius:12px; border:2px solid #E5E7EB; position:relative; z-index:9; transition: height 0.3s ease;"></div>
          <div style="font-size:11px; color:#64748B; margin-top:8px; padding:8px; background:#F9FAFB; border-radius:6px;">
            📍 <span id="reg-address-preview" style="color:#10B981; font-weight:700;">Fetching location...</span>
          </div>
        </div>

        <!-- Pharmacy Name -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🏥 Pharmacy Name</label>
          <input 
            type="text" 
            name="shop_name" 
            value="{{ old('shop_name') }}"
            placeholder="e.g. Sharma Medical Store" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#10B981';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Area Coverage -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🗺️ Area Coverage</label>
          <select name="area" id="area-select" required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box; background:#fff;"
            onfocus="this.style.borderColor='#10B981';"
            onblur="this.style.borderColor='#E5E7EB';">
            <option value="Muzaffarpur" {{ old('area') === 'Muzaffarpur' ? 'selected' : '' }}>Muzaffarpur</option>
            <option value="Patna" {{ old('area') === 'Patna' ? 'selected' : '' }}>Patna</option>
            <option value="Jaipur" {{ old('area') === 'Jaipur' ? 'selected' : '' }}>Jaipur</option>
            <option value="Darbhanga" {{ old('area') === 'Darbhanga' ? 'selected' : '' }}>Darbhanga</option>
          </select>
        </div>

        <!-- State & City -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🏛️ State</label>
            <input 
              type="text" 
              name="state" 
              id="state-input"
              value="{{ old('state') }}"
              placeholder="e.g. Bihar" 
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🏙️ City</label>
            <input 
              type="text" 
              name="city" 
              id="city-input"
              value="{{ old('city') }}"
              placeholder="e.g. Muzaffarpur" 
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
        </div>

        <!-- Locality & PIN -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📍 Locality</label>
            <input 
              type="text" 
              name="area_name" 
              id="area-name-input"
              value="{{ old('area_name') }}"
              placeholder="e.g. Mithanpura" 
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📮 PIN Code</label>
            <input 
              type="text" 
              name="pin_code" 
              id="pin-input"
              value="{{ old('pin_code') }}"
              placeholder="842002" 
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
        </div>

        <!-- Full Address -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🏠 Full Address</label>
          <input 
            type="text" 
            name="address" 
            id="address-input"
            value="{{ old('address') }}"
            placeholder="Shop Address, Locality" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#10B981';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Timing -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🕘 Opening Time</label>
            <input 
              type="time" 
              name="opens_at" 
              value="{{ old('opens_at', '09:00') }}"
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🕘 Closing Time</label>
            <input 
              type="time" 
              name="closes_at" 
              value="{{ old('closes_at', '21:00') }}"
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
              onfocus="this.style.borderColor='#10B981';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>
        </div>

        <!-- Home Delivery Toggle -->
        <div style="background:#F0FDF4; border:2px solid #10B981; border-radius:12px; padding:14px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;">
          <div>
            <div style="font-weight:800; font-size:14px; color:#059669; display:flex; align-items:center; gap:6px;">
              🛵 Home Delivery
            </div>
            <div style="font-size:11px; color:#047857; margin-top:2px;">Enable delivery service</div>
          </div>
          <label class="switch-container" style="position:relative; display:inline-block; width:44px; height:24px;">
            <input type="checkbox" name="delivery_enabled" value="1" checked style="opacity:0; width:0; height:0;" onchange="toggleRegDeliverySection(this.checked)">
            <span class="switch-slider" style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#10B981; transition:.4s; border-radius:24px;"></span>
          </label>
        </div>

        <!-- Delivery Charges -->
        <div id="reg-delivery-charges-container" style="background:#F9FAFB; border:2px solid #E5E7EB; border-radius:12px; padding:16px; margin-bottom:16px;">
          <div style="font-weight:800; font-size:14px; color:#374151; margin-bottom:12px;">💰 Delivery Charge</div>
          
          <div style="display:flex; gap:8px; margin-bottom:12px;">
            <button type="button" id="reg-btn-del-free" onclick="setRegDeliveryType('free')" style="flex:1; padding:10px; border:2px solid #E5E7EB; background:#fff; border-radius:8px; font-weight:700; font-size:12px; color:#64748B; cursor:pointer;">🎁 Free</button>
            <button type="button" id="reg-btn-del-perkm" onclick="setRegDeliveryType('perkm')" style="flex:1; padding:10px; border:2px solid #10B981; background:#F0FDF4; border-radius:8px; font-weight:700; font-size:12px; color:#10B981; cursor:pointer;">📏 Per KM</button>
            <button type="button" id="reg-btn-del-fixed" onclick="setRegDeliveryType('fixed')" style="flex:1; padding:10px; border:2px solid #E5E7EB; background:#fff; border-radius:8px; font-weight:700; font-size:12px; color:#64748B; cursor:pointer;">📦 Fixed</button>
          </div>

          <input type="hidden" name="delivery_charge_type" id="reg-del-type" value="dynamic">
          
          <div id="reg-del-rate-wrapper">
            <label id="reg-del-rate-label" style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px;">Rate per KM (₹)</label>
            <div style="display:flex; align-items:center; gap:8px;">
              <span style="font-weight:800; color:#1A1A1A; font-size:16px;">₹</span>
              <input type="number" step="0.5" name="delivery_charge_rate" id="reg-del-rate-input" value="8"
                style="flex:1; padding:10px 12px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;">
              <span style="font-size:12px; color:#64748B;" id="reg-del-rate-suffix">/ km</span>
            </div>
            <div style="font-size:11px; color:#64748B; margin-top:6px;" id="reg-del-example">Example: 3 km × ₹8 = ₹24</div>
          </div>
        </div>

        <!-- Discount Offers -->
        <div style="background:#FFFBEB; border:2px solid #FDE047; border-radius:12px; padding:16px; margin-bottom:16px;">
          <div style="font-weight:800; font-size:14px; color:#CA8A04; margin-bottom:12px;">🎁 Discount Offers</div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#854D0E; margin-bottom:6px;">Min Bill (₹)</label>
              <input 
                type="number" 
                step="any" 
                name="offer_min_bill" 
                value="{{ old('offer_min_bill', '0.00') }}"
                style="width:100%; padding:10px 12px; border:2px solid #FDE047; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;">
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#854D0E; margin-bottom:6px;">Discount (%)</label>
              <input 
                type="number" 
                step="any" 
                name="offer_discount_pct" 
                value="{{ old('offer_discount_pct', '0.00') }}"
                style="width:100%; padding:10px 12px; border:2px solid #FDE047; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;">
            </div>
          </div>
        </div>

        <!-- Lat/Lng -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🌍 Latitude</label>
            <input 
              type="number" 
              step="any" 
              name="latitude" 
              id="lat-input"
              value="{{ old('latitude', '26.1209') }}"
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;">
          </div>
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🌍 Longitude</label>
            <input 
              type="number" 
              step="any" 
              name="longitude" 
              id="lng-input"
              value="{{ old('longitude', '85.3647') }}"
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;">
          </div>
        </div>

        <!-- Shop Image -->
        <div>
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📸 Shop Image</label>
          <input 
            type="file" 
            name="shop_image" 
            accept="image/*"
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box; background:#fff;">
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        style="width:100%; padding:14px; background:linear-gradient(135deg, #10B981, #059669); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:800; cursor:pointer; box-shadow:0 4px 12px rgba(16,185,129,0.3); transition:transform 0.2s;"
        onmouseover="this.style.transform='translateY(-2px)';"
        onmouseout="this.style.transform='translateY(0)';"
      >
        🚀 Register Pharmacy & Launch
      </button>
    </form>

    <!-- Login Link -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-top:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-align:center;">
      <a href="{{ url('/login') }}" style="font-size:13px; color:#10B981; font-weight:700; text-decoration:none;">
        Already have an account? <span style="text-decoration:underline;">Login here</span>
      </a>
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

window.addEventListener('DOMContentLoaded', () => {
  initRegistrationMap();
});

function initRegistrationMap() {
  regMap = L.map('reg-leaflet-map', { zoomControl: true }).setView([26.9124, 75.7873], 12);

  regStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  });

  regSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19,
    attribution: 'Tiles © Esri'
  });

  regStreetLayer.addTo(regMap);
  regMarker = L.marker([26.9124, 75.7873], { draggable: true }).addTo(regMap);

  regMarker.on('dragend', function(e) {
    const pos = regMarker.getLatLng();
    updateRegFieldsFromCoordinates(pos.lat, pos.lng);
  });

  regMap.on('click', function(e) {
    regMarker.setLatLng(e.latlng);
    updateRegFieldsFromCoordinates(e.latlng.lat, e.latlng.lng);
  });

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      regMap.setView([lat, lng], 16);
      regMarker.setLatLng([lat, lng]);
      updateRegFieldsFromCoordinates(lat, lng);
    }, err => {
      console.warn("GPS lookup denied, using default");
      updateRegFieldsFromCoordinates(26.9124, 75.7873);
    });
  } else {
    updateRegFieldsFromCoordinates(26.9124, 75.7873);
  }
}

function updateRegFieldsFromCoordinates(lat, lng) {
  document.getElementById('lat-input').value = lat.toFixed(6);
  document.getElementById('lng-input').value = lng.toFixed(6);
  document.getElementById('reg-address-preview').innerText = "Fetching address...";

  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
    headers: { 'Accept-Language': 'en' }
  })
    .then(res => res.json())
    .then(data => {
      if (data && data.address) {
        const addr = data.address;
        const stateVal = addr.state || '';
        
        let cityClean = addr.district || addr.city_district || addr.city || addr.town || addr.village || 'Jaipur';
        cityClean = cityClean.replace(/\s+(District|Division|City|Tehsil)/i, '').trim();

        let displayLoc = '';
        if (data.display_name) {
          let parts = data.display_name.split(',').map(p => p.trim());
          let filteredParts = parts.filter(p => {
            let lower = p.toLowerCase();
            return !lower.includes('india') && !lower.includes('rajasthan') && !lower.match(/^\d{6}$/) && lower !== cityClean.toLowerCase();
          });
          if (filteredParts.length > 0) {
            displayLoc = filteredParts.slice(0, 2).join(', ') + ', ' + cityClean;
          }
        }

        if (!displayLoc) {
          let landmark = data.name || addr.amenity || addr.shop || addr.building || '';
          let road = addr.road || addr.suburb || addr.neighbourhood || '';
          if (landmark) displayLoc += landmark.trim();
          if (road) displayLoc += (displayLoc ? ', ' : '') + road.trim();
          if (!displayLoc) {
            displayLoc = cityClean;
          } else {
            displayLoc += ', ' + cityClean;
          }
        }

        document.getElementById('state-input').value = stateVal;
        document.getElementById('city-input').value = cityClean;
        document.getElementById('area-name-input').value = addr.suburb || addr.neighbourhood || addr.road || '';
        document.getElementById('pin-input').value = addr.postcode || '';
        document.getElementById('address-input').value = displayLoc;
        document.getElementById('reg-address-preview').innerText = displayLoc;

        const areaSelect = document.getElementById('area-select');
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
    btn.innerHTML = '🗺️ Street';
    btn.style.background = '#3B82F6';
  } else {
    regMap.removeLayer(regSatelliteLayer);
    regStreetLayer.addTo(regMap);
    regActiveLayer = 'street';
    btn.innerHTML = '🛰️ Satellite';
    btn.style.background = '#64748B';
  }
}

function toggleRegFullScreen() {
  const mapDiv = document.getElementById('reg-leaflet-map');
  const btn = document.getElementById('btn-reg-fullscreen');
  if (!isRegMapFullScreen) {
    mapDiv.style.height = '480px';
    btn.innerHTML = '🗗 Min';
    btn.style.background = '#EF4444';
    isRegMapFullScreen = true;
  } else {
    mapDiv.style.height = '220px';
    btn.innerHTML = '🗖 Full';
    btn.style.background = '#10B981';
    isRegMapFullScreen = false;
  }
  setTimeout(() => {
    if (regMap) regMap.invalidateSize();
  }, 300);
}

function toggleRegDeliverySection(checked) {
  const container = document.getElementById('reg-delivery-charges-container');
  container.style.display = checked ? 'block' : 'none';
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

  [btnFree, btnPerKm, btnFixed].forEach(btn => {
    btn.style.border = '2px solid #E5E7EB';
    btn.style.background = '#fff';
    btn.style.color = '#64748B';
  });

  if (type === 'free') {
    btnFree.style.border = '2px solid #10B981';
    btnFree.style.background = '#F0FDF4';
    btnFree.style.color = '#10B981';
    typeInput.value = 'fixed';
    rateWrapper.style.display = 'none';
    rateInput.value = '0';
  } else if (type === 'perkm') {
    btnPerKm.style.border = '2px solid #10B981';
    btnPerKm.style.background = '#F0FDF4';
    btnPerKm.style.color = '#10B981';
    typeInput.value = 'dynamic';
    rateWrapper.style.display = 'block';
    rateLabel.innerText = 'Rate per KM (₹)';
    rateInput.value = '8';
    rateSuffix.innerText = '/ km';
    rateExample.innerText = 'Example: 3 km × ₹8 = ₹24';
  } else if (type === 'fixed') {
    btnFixed.style.border = '2px solid #10B981';
    btnFixed.style.background = '#F0FDF4';
    btnFixed.style.color = '#10B981';
    typeInput.value = 'fixed';
    rateWrapper.style.display = 'block';
    rateLabel.innerText = 'Fixed Charge (₹)';
    rateInput.value = '20';
    rateSuffix.innerText = 'flat';
    rateExample.innerText = 'Flat rate regardless of distance';
  }
}
</script>

@endsection
