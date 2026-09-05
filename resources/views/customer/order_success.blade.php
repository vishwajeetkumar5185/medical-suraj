@extends('layouts.app')

@section('seo_title', 'Order Success - Dawalo')

@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <!-- Header -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url('/') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">✅ Order Successful</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:12px; margin:0;">Your order has been placed</p>
      </div>
    </div>
  </div>

  <div style="padding:20px;">
    
    <!-- Success Message Card -->
    <div style="background:#fff; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-align:center;">
      <div style="width:80px; height:80px; border-radius:50%; background:#D1FAE5; display:flex; align-items:center; justify-content:center; font-size:40px; margin:0 auto 16px; box-shadow:0 0 0 12px rgba(16,185,129,0.1);">
        ✅
      </div>
      <h2 style="font-weight:800; font-size:20px; color:#1A1A1A; margin-bottom:8px;">Order Placed Successfully!</h2>
      <p style="font-size:14px; color:#64748B; margin-bottom:16px;">
        Order #{{ $order->id }} has been sent to {{ $order->shop->name }}
      </p>
      <div style="display:inline-block; background:#D1FAE5; color:#059669; font-size:12px; font-weight:700; padding:8px 16px; border-radius:20px;">
        🏪 {{ $order->shop->name }}
      </div>
    </div>

    <!-- Order Status Card -->
    <div id="status-card-wrapper" style="margin-bottom:16px;">
      @if($order->status === 'Pending')
        <div style="background:#FFFBEB; border-radius:16px; padding:16px; border:2px solid #FDE047;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:32px;">⏳</div>
            <div style="flex:1;">
              <div style="font-size:15px; color:#CA8A04; font-weight:800;">Waiting for Confirmation</div>
              <div style="font-size:12px; color:#854D0E; margin-top:2px;">Shop is reviewing your order...</div>
            </div>
          </div>
        </div>
      @elseif($order->status === 'Accepted')
        <div style="background:#D1FAE5; border-radius:16px; padding:16px; border:2px solid #10B981;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:32px;">✅</div>
            <div style="flex:1;">
              <div style="font-size:15px; color:#059669; font-weight:800;">Order Accepted!</div>
              <div style="font-size:12px; color:#047857; margin-top:2px;">Shop is preparing your order for delivery</div>
            </div>
          </div>
        </div>
      @elseif($order->status === 'Cancelled')
        <div style="background:#FEE2E2; border-radius:16px; padding:16px; border:2px solid #EF4444;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:32px;">❌</div>
            <div style="flex:1;">
              <div style="font-size:15px; color:#DC2626; font-weight:800;">Order Cancelled</div>
              <div style="font-size:12px; color:#B91C1C; margin-top:2px;">Shop has rejected this order</div>
            </div>
          </div>
        </div>
      @elseif($order->status === 'Delivered')
        <div style="background:#D1FAE5; border-radius:16px; padding:16px; border:2px solid #10B981;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:32px;">📦</div>
            <div style="flex:1;">
              <div style="font-size:15px; color:#059669; font-weight:800;">Order Delivered!</div>
              <div style="font-size:12px; color:#047857; margin-top:2px;">Thank you for ordering with Dawalo</div>
            </div>
          </div>
        </div>
      @else
        <div style="background:#F3F4F6; border-radius:16px; padding:16px; border:2px solid #E5E7EB;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:32px;">ℹ️</div>
            <div style="flex:1;">
              <div style="font-size:15px; color:#64748B; font-weight:800;">Order Status: {{ $order->status }}</div>
            </div>
          </div>
        </div>
      @endif
    </div>

    <!-- Delivery Route Map Card -->
    @php
      $uLat = session('user_lat');
      $uLng = session('user_lng');
      $shopLat = (float)($order->shop->latitude ?? 26.9124);
      $shopLng = (float)($order->shop->longitude ?? 75.7873);
      
      $realDist = (float)($order->shop->distance_km ?? 1.2);
      if ($uLat && $uLng && $shopLat && $shopLng) {
          $theta = $uLng - $shopLng;
          $dist = sin(deg2rad($uLat)) * sin(deg2rad($shopLat)) +  cos(deg2rad($uLat)) * cos(deg2rad($shopLat)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $realDist = round($miles * 1.609344, 1);
      }
    @endphp
    
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0 0 12px 0;">🗺️ Delivery Route</h3>
      
      <div id="delivery-route-map" style="width:100%; height:200px; border-radius:12px; border:2px solid #E5E7EB; margin-bottom:12px;"></div>
      
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; font-weight:700;">
        <div style="color:#64748B;">
          📍 Distance: <span style="color:#10B981;">{{ $realDist }} KM</span>
        </div>
        <div style="color:#64748B;">
          🏪 {{ $order->shop->name }}
        </div>
      </div>
    </div>

    <!-- Order Items Card -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0 0 16px 0;">📦 Order Items</h3>
      
      @foreach($order->items as $item)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #F3F4F6;">
          <div style="flex:1;">
            <div style="font-size:14px; font-weight:700; color:#1A1A1A;">{{ $item['emoji'] ?? '💊' }} {{ $item['name'] }}</div>
            <div style="font-size:12px; color:#64748B;">Qty: {{ $item['quantity'] ?? $item['qty'] ?? 1 }}</div>
          </div>
          <div style="font-size:15px; font-weight:800; color:#0EA5E9;">
            ₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1), 2) }}
          </div>
        </div>
      @endforeach

      <!-- Price Breakdown -->
      <div style="margin-top:16px; padding-top:16px; border-top:2px solid #E5E7EB;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; color:#64748B;">
          <span>Items Subtotal:</span>
          <span style="font-weight:700; color:#1A1A1A;">₹{{ number_format($order->total_price + $order->discount_amount, 2) }}</span>
        </div>
        
        @if(($order->discount_amount ?? 0) > 0)
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; color:#10B981; font-weight:700;">
            <span>Discount:</span>
            <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
          </div>
        @endif
        
        @if(($order->delivery_charge ?? 0) > 0)
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; color:#64748B;">
            <span>Delivery Charges:</span>
            <span style="font-weight:700;">+₹{{ number_format($order->delivery_charge, 2) }}</span>
          </div>
        @endif
        
        <div style="display:flex; justify-content:space-between; padding-top:12px; border-top:2px solid #E5E7EB; margin-top:8px;">
          <span style="font-size:16px; font-weight:800; color:#1A1A1A;">Grand Total:</span>
          <span style="font-size:20px; font-weight:800; color:#10B981;">₹{{ number_format($order->total_price + $order->delivery_charge, 2) }}</span>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div style="display:flex; flex-direction:column; gap:12px;">
      <a href="{{ url('/') }}" style="display:block; padding:14px; background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; text-align:center; border-radius:10px; font-weight:800; font-size:15px; text-decoration:none; box-shadow:0 4px 12px rgba(14,165,233,0.3);">
        🏠 Go to Home
      </a>
      <a href="{{ url('/profile/orders') }}" style="display:block; padding:14px; background:#fff; color:#0EA5E9; text-align:center; border-radius:10px; font-weight:800; font-size:15px; text-decoration:none; border:2px solid #0EA5E9;">
        📋 View All Orders
      </a>
    </div>

  </div>

</div>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const shopLat = parseFloat("{{ $shopLat }}");
    const shopLng = parseFloat("{{ $shopLng }}");
    const custLat = parseFloat("{{ $uLat }}") || (shopLat - 0.008);
    const custLng = parseFloat("{{ $uLng }}") || (shopLng + 0.006);

    const routeMap = L.map('delivery-route-map', { zoomControl: false }).setView([shopLat, shopLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(routeMap);

    const shopIcon = L.divIcon({
      html: '🏪',
      className: 'custom-div-icon',
      iconSize: [24, 24],
      iconAnchor: [12, 12]
    });
    
    const customerIcon = L.divIcon({
      html: '🏠',
      className: 'custom-div-icon',
      iconSize: [24, 24],
      iconAnchor: [12, 12]
    });

    const shopMarker = L.marker([shopLat, shopLng], { icon: shopIcon }).addTo(routeMap).bindPopup("Pharmacy Store");
    const customerMarker = L.marker([custLat, custLng], { icon: customerIcon }).addTo(routeMap).bindPopup("Your Location");

    L.polyline([[shopLat, shopLng], [custLat, custLng]], { 
      color: '#0EA5E9', 
      weight: 4, 
      dashArray: '8, 8' 
    }).addTo(routeMap);

    const group = new L.featureGroup([shopMarker, customerMarker]);
    routeMap.fitBounds(group.getBounds().pad(0.2));
  });

  const orderId = "{{ $order->id }}";
  let currentStatus = "{{ $order->status }}";

  function checkOrderStatus() {
    if (currentStatus === 'Cancelled' || currentStatus === 'Delivered') return;

    fetch(`/order/${orderId}/status`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.status !== currentStatus) {
          currentStatus = data.status;
          const wrapper = document.getElementById('status-card-wrapper');
          if (wrapper) {
            let html = '';
            if (currentStatus === 'Pending') {
              html = '<div style="background:#FFFBEB; border-radius:16px; padding:16px; border:2px solid #FDE047;"><div style="display:flex; align-items:center; gap:12px;"><div style="font-size:32px;">⏳</div><div style="flex:1;"><div style="font-size:15px; color:#CA8A04; font-weight:800;">Waiting for Confirmation</div><div style="font-size:12px; color:#854D0E; margin-top:2px;">Shop is reviewing your order...</div></div></div></div>';
            } else if (currentStatus === 'Accepted') {
              html = '<div style="background:#D1FAE5; border-radius:16px; padding:16px; border:2px solid #10B981;"><div style="display:flex; align-items:center; gap:12px;"><div style="font-size:32px;">✅</div><div style="flex:1;"><div style="font-size:15px; color:#059669; font-weight:800;">Order Accepted!</div><div style="font-size:12px; color:#047857; margin-top:2px;">Shop is preparing your order</div></div></div></div>';
            } else if (currentStatus === 'Cancelled') {
              html = '<div style="background:#FEE2E2; border-radius:16px; padding:16px; border:2px solid #EF4444;"><div style="display:flex; align-items:center; gap:12px;"><div style="font-size:32px;">❌</div><div style="flex:1;"><div style="font-size:15px; color:#DC2626; font-weight:800;">Order Cancelled</div><div style="font-size:12px; color:#B91C1C; margin-top:2px;">Shop has rejected this order</div></div></div></div>';
            } else if (currentStatus === 'Delivered') {
              html = '<div style="background:#D1FAE5; border-radius:16px; padding:16px; border:2px solid #10B981;"><div style="display:flex; align-items:center; gap:12px;"><div style="font-size:32px;">📦</div><div style="flex:1;"><div style="font-size:15px; color:#059669; font-weight:800;">Order Delivered!</div><div style="font-size:12px; color:#047857; margin-top:2px;">Thank you for ordering</div></div></div></div>';
            }
            wrapper.innerHTML = html;
          }
        }
      })
      .catch(err => console.error('Status check error:', err));
  }

  setInterval(checkOrderStatus, 4000);
</script>

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
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Profile</span>
    </a>
  </div>

</div>

@endsection
