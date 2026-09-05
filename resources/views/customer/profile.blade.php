@extends('layouts.app')

@section('seo_title', 'My Profile - Dawalo')

@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:24px 16px;">
    <div style="text-align:center;">
      <div style="width:80px; height:80px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:40px; margin:0 auto 12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        👤
      </div>
      @if(Auth::check())
        <h1 style="color:#fff; font-size:22px; font-weight:800; margin:0 0 4px 0;">{{ Auth::user()->name }}</h1>
        <p style="color:rgba(255,255,255,0.9); font-size:14px; margin:0;">📞 {{ Auth::user()->phone ?? 'No Phone' }}</p>
        <span style="background:rgba(255,255,255,0.25); color:#fff; font-size:11px; font-weight:800; padding:4px 12px; border-radius:20px; display:inline-block; margin-top:8px; text-transform:uppercase;">
          🛡️ {{ Auth::user()->role }}
        </span>
      @else
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0 0 4px 0;">Guest User</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:13px; margin:0;">Login/Register with your phone</p>
      @endif
    </div>
  </div>

  <div style="padding:16px;">
    
    @if($registeredShop)
      <div style="background:#fff; border-radius:16px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:16px;">
        <div style="display:flex; gap:12px; align-items:center;">
          <div style="width:56px; height:56px; background:#F0F9FF; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0;">
            🏪
          </div>
          <div style="flex:1;">
            <div style="font-size:16px; font-weight:800; color:#1A1A1A; margin-bottom:2px;">{{ $registeredShop->name }}</div>
            <div style="font-size:12px; color:#64748B;">Your pharmacy is listed</div>
          </div>
          <a href="{{ url('/shop/dashboard') }}" style="padding:10px 16px; background:#0EA5E9; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; font-size:13px;">
            Dashboard
          </a>
        </div>
      </div>
    @else
      <div style="background:#fff; border-radius:16px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:16px;">
        <div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">
          <div style="width:56px; height:56px; background:#F0F9FF; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0;">
            🏪
          </div>
          <div style="flex:1; min-width:200px;">
            <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0 0 6px 0;">Medical Store Owner?</h3>
            <p style="font-size:13px; color:#64748B; margin:0 0 12px 0; line-height:1.5;">List your pharmacy on Dawalo and reach nearby customers. Completely free!</p>
            <a href="{{ url('/profile?showRegisterForm=1') }}" style="display:inline-block; padding:10px 20px; background:#0EA5E9; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; font-size:13px; margin-bottom:12px; text-align:center; width:auto;">
              + List Your Store
            </a>
            
            <div style="border-top:1px solid #F1F5F9; padding-top:12px; margin-top:8px;">
              <div style="font-size:13px; font-weight:700; color:#1A1A1A; margin-bottom:8px;">Already registered?</div>
              <form action="{{ url('/shop/login-phone') }}" method="POST" style="display:flex; gap:8px; flex-wrap:wrap;">
                @csrf
                <input type="text" name="phone" placeholder="Registered phone number..." style="flex:1; min-width:180px; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:13px; outline:none;" required>
                <button type="submit" style="padding:10px 16px; background:#0EA5E9; color:#fff; border:none; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                  Manage
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endif

    @if(request('showRegisterForm') || request('modal') === 'shopForm')
      <div style="background:#fff; border-radius:16px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:16px; border:2px solid #0EA5E9;">
        <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin:0 0 16px 0;">🏪 Register Your Pharmacy</h3>
        
        <form action="{{ url('/shop/register') }}" method="POST">
          @csrf
          <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Shop Name *</label>
            <input type="text" name="name" placeholder="e.g. Verma Medical Hall" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
          </div>
          <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Owner Name *</label>
            <input type="text" name="owner" placeholder="e.g. Ramesh Verma" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
          </div>
          <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Phone Number *</label>
            <input type="text" name="phone" placeholder="e.g. 9876543210" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
          </div>
          <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">City / Area Coverage *</label>
            <select name="area" id="area-select" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
              <option value="">Select Area</option>
              <option value="Muzaffarpur">Muzaffarpur</option>
              <option value="Patna">Patna</option>
              <option value="Jaipur">Jaipur</option>
              <option value="Darbhanga">Darbhanga</option>
            </select>
          </div>
          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Complete Address *</label>
            <textarea name="address" style="width:100%; height:60px; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none; resize:none; font-family:inherit;" placeholder="Complete shop location..." required></textarea>
          </div>
          
          <div style="margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="font-size:12px; font-weight:700; color:#64748B;">Select Location on Map *</label>
              <div style="display:flex; gap:6px;">
                <button type="button" onclick="toggleProfileSatelliteView()" id="btn-profile-satellite" style="background:#64748B; border:none; color:#fff; font-size:10px; font-weight:700; padding:5px 10px; border-radius:6px; cursor:pointer;">
                  🛰️ Satellite
                </button>
                <button type="button" onclick="toggleProfileFullScreen()" id="btn-profile-fullscreen" style="background:#0EA5E9; border:none; color:#fff; font-size:10px; font-weight:700; padding:5px 10px; border-radius:6px; cursor:pointer;">
                  🗖 Full
                </button>
              </div>
            </div>
            
            <div id="register-map" style="height:220px; border-radius:12px; border:1px solid #E5E7EB; margin-bottom:8px; transition:height 0.3s;"></div>
            
            <div style="font-size:12px; color:#64748B; margin-bottom:12px; font-weight:600;">
              📍 <span id="profile-address-preview" style="color:#0EA5E9; font-weight:800;">Fetching location...</span>
            </div>
            
            <div style="display:flex; gap:10px;">
              <input type="text" name="latitude" id="reg-lat" placeholder="Latitude" readonly required style="flex:1; padding:10px 12px; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px; font-size:13px;">
              <input type="text" name="longitude" id="reg-lng" placeholder="Longitude" readonly required style="flex:1; padding:10px 12px; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px; font-size:13px;">
            </div>
          </div>

          <div style="display:flex; gap:10px; margin-bottom:14px;">
            <div style="flex:1;">
              <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Opening Time *</label>
              <input type="time" name="opens_at" value="09:00" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
            </div>
            <div style="flex:1;">
              <label style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Closing Time *</label>
              <input type="time" name="closes_at" value="21:00" style="width:100%; padding:10px 12px; border:1px solid #E5E7EB; border-radius:10px; font-size:14px; outline:none;" required>
            </div>
          </div>

          <div style="background:#F0F9FF; border:1px solid #BAE6FD; border-radius:12px; padding:14px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-size:14px; font-weight:800; color:#1A1A1A; margin-bottom:2px;">🛵 Home Delivery</div>
              <div style="font-size:11px; color:#64748B;">Enable delivery service</div>
            </div>
            <label style="position:relative; display:inline-block; width:44px; height:24px;">
              <input type="checkbox" name="delivery_enabled" value="1" checked style="opacity:0; width:0; height:0;" onchange="toggleProfileDeliverySection(this.checked)">
              <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#10B981; transition:0.3s; border-radius:24px; box-shadow:inset 0 1px 3px rgba(0,0,0,0.1);"></span>
              <span style="position:absolute; content:''; height:18px; width:18px; left:3px; bottom:3px; background:#fff; transition:0.3s; border-radius:50%; box-shadow:0 1px 2px rgba(0,0,0,0.2);"></span>
            </label>
          </div>

          <div id="profile-delivery-charges-container" style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:12px; padding:14px; margin-bottom:14px;">
            <div style="font-size:14px; font-weight:800; color:#1A1A1A; margin-bottom:4px;">💰 Delivery Charge</div>
            <div style="font-size:11px; color:#64748B; margin-bottom:12px;">Set your delivery pricing</div>

            <div style="display:flex; gap:8px; margin-bottom:12px;">
              <button type="button" id="btn-del-free" onclick="setProfileDeliveryType('free')" style="flex:1; padding:8px; border:1.5px solid #CBD5E1; background:#fff; border-radius:10px; font-weight:700; font-size:12px; color:#64748B; cursor:pointer;">
                🎁 Free
              </button>
              <button type="button" id="btn-del-perkm" onclick="setProfileDeliveryType('perkm')" style="flex:1; padding:8px; border:2px solid #0EA5E9; background:#F0F9FF; border-radius:10px; font-weight:700; font-size:12px; color:#0EA5E9; cursor:pointer;">
                🖊️ Per KM
              </button>
              <button type="button" id="btn-del-fixed" onclick="setProfileDeliveryType('fixed')" style="flex:1; padding:8px; border:1.5px solid #CBD5E1; background:#fff; border-radius:10px; font-weight:700; font-size:12px; color:#64748B; cursor:pointer;">
                📦 Fixed
              </button>
            </div>

            <input type="hidden" name="delivery_charge_type" id="profile-del-type" value="dynamic">
            
            <div id="profile-del-rate-wrapper">
              <label id="profile-del-rate-label" style="display:block; font-size:12px; font-weight:700; color:#64748B; margin-bottom:6px;">Rate per KM (₹)</label>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-weight:800; color:#1A1A1A; font-size:16px;">₹</span>
                <input type="number" step="0.5" name="delivery_charge_rate" id="profile-del-rate-input" value="8" style="flex:1; padding:8px 10px; border:1px solid #E5E7EB; border-radius:8px; font-size:14px;">
                <span id="profile-del-rate-suffix" style="font-size:12px; color:#64748B;">/ km</span>
              </div>
              <div id="profile-del-example" style="font-size:11px; color:#94A3B8; margin-top:6px;">Example: 3 km × ₹8 = ₹24 delivery</div>
            </div>
          </div>

          <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:12px; padding:14px; margin-bottom:14px;">
            <div style="font-size:14px; font-weight:800; color:#B45309; margin-bottom:12px;">🎁 Discount Offers</div>
            <div style="display:flex; gap:10px;">
              <div style="flex:1;">
                <label style="display:block; font-size:11px; font-weight:700; color:#92400E; margin-bottom:6px;">Min Bill (₹)</label>
                <input type="number" step="any" name="offer_min_bill" value="0.00" style="width:100%; padding:8px 10px; border:1px solid #FDE68A; border-radius:8px; font-size:13px;">
              </div>
              <div style="flex:1;">
                <label style="display:block; font-size:11px; font-weight:700; color:#92400E; margin-bottom:6px;">Discount (%)</label>
                <input type="number" step="any" name="offer_discount_pct" value="0.00" style="width:100%; padding:8px 10px; border:1px solid #FDE68A; border-radius:8px; font-size:13px;">
              </div>
            </div>
          </div>

          <div style="display:flex; gap:10px;">
            <a href="{{ url('/profile') }}" style="flex:1; padding:12px; text-align:center; background:#F3F4F6; color:#64748B; text-decoration:none; border-radius:10px; font-weight:700; font-size:14px;">
              Cancel
            </a>
            <button type="submit" style="flex:1; padding:12px; background:#0EA5E9; color:#fff; border:none; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer;">
              Save & List
            </button>
          </div>
        </form>
      </div>

      <script>
        let profileMap, profileMarker, profileStreetLayer, profileSatelliteLayer;
        let profileActiveLayer = 'street';
        let isProfileMapFullScreen = false;

        window.addEventListener('DOMContentLoaded', () => {
          profileMap = L.map('register-map', { zoomControl: true }).setView([26.9124, 75.7873], 12);
          
          profileStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
          });

          profileSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles © Esri'
          });

          profileStreetLayer.addTo(profileMap);
          profileMarker = L.marker([26.9124, 75.7873], { draggable: true }).addTo(profileMap);
          
          profileMarker.on('dragend', function(e) {
            const pos = profileMarker.getLatLng();
            updateProfileMapFields(pos.lat, pos.lng);
          });

          profileMap.on('click', function(e) {
            profileMarker.setLatLng(e.latlng);
            updateProfileMapFields(e.latlng.lat, e.latlng.lng);
          });

          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
              const lat = position.coords.latitude;
              const lng = position.coords.longitude;
              profileMap.setView([lat, lng], 15);
              profileMarker.setLatLng([lat, lng]);
              updateProfileMapFields(lat, lng);
            }, err => {
              updateProfileMapFields(26.9124, 75.7873);
            });
          } else {
            updateProfileMapFields(26.9124, 75.7873);
          }
        });

        function updateProfileMapFields(lat, lng) {
          document.getElementById('reg-lat').value = lat.toFixed(6);
          document.getElementById('reg-lng').value = lng.toFixed(6);
          document.getElementById('profile-address-preview').innerText = "Fetching...";

          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': 'en' }
          })
            .then(res => res.json())
            .then(data => {
              if (data && data.address) {
                const addr = data.address;
                let city = addr.district || addr.city_district || addr.city || addr.town || 'Jaipur';
                city = city.replace(/\s+(District|Division|City)/i, '').trim();

                let displayLoc = '';
                if (data.display_name) {
                  let parts = data.display_name.split(',').map(p => p.trim());
                  displayLoc = parts.slice(0, 2).join(', ') + ', ' + city;
                }

                if (!displayLoc) {
                  displayLoc = (data.name || addr.road || city);
                }

                document.querySelector('textarea[name="address"]').value = displayLoc;
                document.getElementById('profile-address-preview').innerText = displayLoc;

                const areaSelect = document.getElementById('area-select');
                for (let i = 0; i < areaSelect.options.length; i++) {
                  if (areaSelect.options[i].value.toLowerCase() === city.toLowerCase()) {
                    areaSelect.selectedIndex = i;
                    return;
                  }
                }
                if (city) {
                  const newOpt = new Option(city, city, true, true);
                  areaSelect.add(newOpt);
                }
              }
            })
            .catch(err => {
              document.getElementById('profile-address-preview').innerText = "Lookup failed";
            });
        }

        function toggleProfileSatelliteView() {
          const btn = document.getElementById('btn-profile-satellite');
          if (profileActiveLayer === 'street') {
            profileMap.removeLayer(profileStreetLayer);
            profileSatelliteLayer.addTo(profileMap);
            profileActiveLayer = 'satellite';
            btn.innerHTML = '🗺️ Street';
            btn.style.background = '#0284C7';
          } else {
            profileMap.removeLayer(profileSatelliteLayer);
            profileStreetLayer.addTo(profileMap);
            profileActiveLayer = 'street';
            btn.innerHTML = '🛰️ Satellite';
            btn.style.background = '#64748B';
          }
        }

        function toggleProfileFullScreen() {
          const mapDiv = document.getElementById('register-map');
          const btn = document.getElementById('btn-profile-fullscreen');
          if (!isProfileMapFullScreen) {
            mapDiv.style.height = '480px';
            btn.innerHTML = '🗗 Min';
            btn.style.background = '#DC2626';
            isProfileMapFullScreen = true;
          } else {
            mapDiv.style.height = '220px';
            btn.innerHTML = '🗖 Full';
            btn.style.background = '#0EA5E9';
            isProfileMapFullScreen = false;
          }
          setTimeout(() => profileMap.invalidateSize(), 300);
        }

        function toggleProfileDeliverySection(checked) {
          document.getElementById('profile-delivery-charges-container').style.display = checked ? 'block' : 'none';
        }

        function setProfileDeliveryType(type) {
          const btnFree = document.getElementById('btn-del-free');
          const btnPerKm = document.getElementById('btn-del-perkm');
          const btnFixed = document.getElementById('btn-del-fixed');
          
          [btnFree, btnPerKm, btnFixed].forEach(btn => {
            btn.style.border = '1.5px solid #CBD5E1';
            btn.style.background = '#fff';
            btn.style.color = '#64748B';
          });

          const typeInput = document.getElementById('profile-del-type');
          const rateWrapper = document.getElementById('profile-del-rate-wrapper');
          const rateLabel = document.getElementById('profile-del-rate-label');
          const rateInput = document.getElementById('profile-del-rate-input');
          const rateSuffix = document.getElementById('profile-del-rate-suffix');
          const rateExample = document.getElementById('profile-del-example');

          if (type === 'free') {
            btnFree.style.border = '2px solid #0EA5E9';
            btnFree.style.background = '#F0F9FF';
            btnFree.style.color = '#0EA5E9';
            typeInput.value = 'fixed';
            rateWrapper.style.display = 'none';
            rateInput.value = '0';
          } else if (type === 'perkm') {
            btnPerKm.style.border = '2px solid #0EA5E9';
            btnPerKm.style.background = '#F0F9FF';
            btnPerKm.style.color = '#0EA5E9';
            typeInput.value = 'dynamic';
            rateWrapper.style.display = 'block';
            rateLabel.innerText = 'Rate per KM (₹)';
            rateInput.value = '8';
            rateSuffix.innerText = '/ km';
            rateExample.innerText = 'Example: 3 km × ₹8 = ₹24 delivery';
          } else {
            btnFixed.style.border = '2px solid #0EA5E9';
            btnFixed.style.background = '#F0F9FF';
            btnFixed.style.color = '#0EA5E9';
            typeInput.value = 'fixed';
            rateWrapper.style.display = 'block';
            rateLabel.innerText = 'Fixed Charge (₹)';
            rateInput.value = '20';
            rateSuffix.innerText = 'flat';
            rateExample.innerText = 'Flat rate regardless of distance';
          }
        }
      </script>
    @endif

    <div style="background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      @php
        $menuItems = [
          ['icon' => '📋', 'label' => 'My Orders', 'url' => url('/profile/orders')],
          ['icon' => '📍', 'label' => 'Saved Addresses', 'url' => url('/profile/addresses')],
          ['icon' => '❤️', 'label' => 'Favourite Shops', 'url' => url('/profile/favourites')],
          ['icon' => '🔔', 'label' => 'Notifications', 'url' => url('/profile/notifications')],
          ['icon' => '⚙️', 'label' => 'Settings', 'url' => url('/profile/settings')],
          ['icon' => '❓', 'label' => 'Help & Support', 'url' => url('/profile/help')]
        ];
      @endphp
      @foreach($menuItems as $item)
        <a href="{{ $item['url'] }}" style="display:flex; align-items:center; gap:14px; padding:16px 18px; border-bottom:1px solid #F1F5F9; text-decoration:none;">
          <span style="font-size:22px;">{{ $item['icon'] }}</span>
          <div style="flex:1; font-weight:700; font-size:14px; color:#1A1A1A;">{{ $item['label'] }}</div>
          <span style="color:#CBD5E1; font-size:20px;">›</span>
        </a>
      @endforeach
      @if(Auth::check())
        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">
          @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display:flex; align-items:center; gap:14px; padding:16px 18px; text-decoration:none;">
          <span style="font-size:22px;">🚪</span>
          <div style="flex:1; font-weight:700; font-size:14px; color:#DC2626;">Logout</div>
          <span style="color:#DC2626; font-size:20px;">›</span>
        </a>
      @endif
    </div>

    <div style="text-align:center; margin-top:24px; padding:8px;">
      <a href="{{ url('/admin') }}" style="font-size:12px; color:#94A3B8; text-decoration:none; font-weight:600;">
        🛡️ Admin Operations Login
      </a>
    </div>

  </div>

  <!-- Bottom Navigation -->
  <div style="position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:600px; background:#fff; border-top:1px solid #E5E7EB; padding:8px 20px 12px; display:flex; justify-content:space-around; align-items:center; z-index:1000;">
    <a href="{{ url('/') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🏠</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Home</span>
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
      <div style="width:48px; height:48px; background:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:4px; box-shadow:0 2px 8px rgba(59,130,246,0.3);">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#3B82F6;">Profile</span>
    </a>
  </div>

</div>

@endsection
