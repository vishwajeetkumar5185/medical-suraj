@extends('layouts.app')

@section('seo_title', 'Nearby Pharmacies - Dawalo')
@section('content')

<style>
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <!-- Header -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px; position:sticky; top:0; z-index:100;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
      <a href="{{ url('/') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">Nearby Pharmacies</h1>
        @if($userLat && $userLng)
          <div style="color:rgba(255,255,255,0.85); font-size:12px; font-weight:600; margin-top:2px;">📍 Sorted by distance</div>
        @else
          <div style="color:rgba(255,255,255,0.85); font-size:12px; font-weight:600; margin-top:2px;">⚠️ Location not detected</div>
        @endif
      </div>
      <a href="{{ url('/smartcart') }}" style="position:relative; text-decoration:none;">
        <span style="font-size:24px;">🛒</span>
        @if($cartCount > 0)
          <span style="position:absolute; top:-8px; right:-8px; background:#EF4444; color:#fff; font-size:11px; font-weight:700; padding:2px 6px; border-radius:10px; min-width:20px; text-align:center;">{{ $cartCount }}</span>
        @endif
      </a>
    </div>
  </div>

  <!-- Pharmacies List -->
  <div style="padding:16px;">
    
    @if(!$userLat || !$userLng)
      <div style="background:#FEF3C7; border:2px solid #FCD34D; border-radius:12px; padding:16px; margin-bottom:20px;">
        <div style="display:flex; align-items:start; gap:12px;">
          <span style="font-size:24px;">📍</span>
          <div>
            <div style="font-size:14px; font-weight:800; color:#92400E; margin-bottom:4px;">Enable Location</div>
            <div style="font-size:12px; color:#78350F; line-height:1.4; margin-bottom:10px;">Allow location access to see pharmacies sorted by distance</div>
            <button onclick="enableLocation()" style="background:#F59E0B; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">Enable Now</button>
          </div>
        </div>
      </div>
    @endif

    <div style="margin-bottom:16px;">
      <div style="font-size:14px; font-weight:700; color:#64748B;">Found {{ $shops->count() }} pharmacies</div>
    </div>

    @if($shops->count() > 0)
      <div style="display:flex; flex-direction:column; gap:14px;">
        @foreach($shops as $index => $shop)
        <a href="{{ url('/search?shop_id='.$shop->id) }}" style="text-decoration:none;">
          <div style="background:#fff; border-radius:16px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); display:flex; gap:14px; align-items:center; position:relative;">
            
            @if($index < 3)
              <div style="position:absolute; top:12px; right:12px; background:#10B981; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px;">TOP {{ $index + 1 }}</div>
            @endif
            
            <div style="width:64px; height:64px; background:#EEF2FF; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:32px; flex-shrink:0;">
              @if($shop->image)
                <img src="{{ asset($shop->image) }}" style="width:100%; height:100%; object-fit:cover; border-radius:14px;" alt="{{ $shop->name }}">
              @else
                🏥
              @endif
            </div>
            
            <div style="flex:1;">
              <div style="font-size:15px; font-weight:800; color:#1A1A1A; margin-bottom:4px;">{{ $shop->name }}</div>
              
              <div style="font-size:12px; color:#64748B; margin-bottom:6px;">
                📍 {{ $shop->area ?? $shop->address }}
              </div>
              
              @if(isset($shop->distance) && $shop->distance < 9999)
                <div style="display:inline-block; background:#DBEAFE; color:#1E40AF; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px; margin-bottom:6px;">
                  🚗 {{ number_format($shop->distance, 1) }} km away
                </div>
              @endif
              
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:6px;">
                <span style="font-size:12px; color:#F59E0B; font-weight:700;">★ {{ number_format($shop->rating ?? 4.5, 1) }}</span>
                
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
                
                @if($shop->is_online)
                  <span style="font-size:11px; color:#10B981; font-weight:600;">✓ Online</span>
                @endif
              </div>
              
              @if($shop->opens_at && $shop->closes_at)
                <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                  🕐 {{ date('g:i A', strtotime($shop->opens_at)) }} - {{ date('g:i A', strtotime($shop->closes_at)) }}
                </div>
              @endif
            </div>
            
            <div style="color:#3B82F6; font-size:24px;">›</div>
          </div>
        </a>
        @endforeach
      </div>
    @else
      <div style="text-align:center; padding:60px 20px;">
        <div style="font-size:80px; margin-bottom:20px;">🏥</div>
        <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:8px;">No Pharmacies Found</h3>
        <p style="font-size:14px; color:#64748B; margin-bottom:20px;">No pharmacies available at the moment</p>
        <a href="{{ url('/') }}" style="display:inline-block; padding:12px 24px; background:#3B82F6; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; box-shadow:0 2px 8px rgba(59,130,246,0.3);">Go to Home</a>
      </div>
    @endif

  </div>

</div>

<script>
  function enableLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        // Reverse geocode to get location name
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
          headers: { 'Accept-Language': 'en' }
        })
        .then(res => res.json())
        .then(data => {
          let locationName = '';
          const addr = data.address || {};
          
          if (addr.suburb) {
            locationName = addr.suburb;
          } else if (addr.neighbourhood) {
            locationName = addr.neighbourhood;
          } else if (addr.road) {
            locationName = addr.road;
          } else if (addr.village) {
            locationName = addr.village;
          }
          
          const city = addr.city || addr.town || addr.county || 'Your Location';
          if (locationName && locationName !== city) {
            locationName = locationName + ', ' + city;
          } else {
            locationName = city;
          }
          
          // Save to server session
          fetch("{{ url('/set-location') }}?city=" + encodeURIComponent(locationName) + "&lat=" + lat + "&lng=" + lng)
            .then(() => {
              window.location.reload();
            })
            .catch(err => console.log('Location save failed:', err));
        })
        .catch(err => console.log('Geocoding failed:', err));
      }, function(error) {
        alert('Location access denied. Please enable location in your browser settings.');
      });
    } else {
      alert('Geolocation is not supported by your browser.');
    }
  }
</script>

@endsection
