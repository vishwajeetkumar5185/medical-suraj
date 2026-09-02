@extends('layouts.app')

@section('content')
<div class="screen" style="align-items:center; justify-content:center; padding:30px 20px; background:#fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
  
  <div style="width:100px; height:100px; border-radius:50%; background:#DCFCE7; display:flex; align-items:center; justify-content:center; font-size:48px; margin-bottom:20px; box-shadow:0 0 0 16px rgba(34,197,94,0.1);">
    ✅
  </div>
  
  <h2 style="font-weight:900; font-size:22px; color:#1A1A1A; margin-bottom:6px; text-align:center;">Order Ho Gaya! 🎉</h2>
  <p style="font-size:14px; color:#888; margin-bottom:24px; text-align:center;">
    {{ $order->shop->name }} ko request bheji ja rahi hai...
  </p>
  
  <div class="responsive-grid" style="width: 100%; max-width: 500px;">
    <!-- Items list card -->
    <div class="card" style="background:#F9FAFB; border-radius:20px; padding:20px; width:100%; box-shadow:0 2px 8px rgba(0,0,0,0.02); border:1px solid #E5E7EB; margin-bottom:0;">
      @foreach($order->items as $item)
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <div style="font-size:13px; color:#555;">{{ $item['emoji'] ?? '💊' }} {{ $item['name'] }} (x{{ $item['quantity'] ?? $item['qty'] ?? 1 }})</div>
          <div style="font-size:13px; font-weight:700; color:#1A3C8F;">₹{{ ($item['price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1) }}</div>
        </div>
      @endforeach
      
      <div style="border-top:1px dashed #E5E7EB; padding-top:10px; margin-top:6px; display:flex; flex-direction:column; gap:4px;">
        <div style="display:flex; justify-content:space-between; font-size:12px; color:#555;">
          <span>Items Subtotal:</span>
          <span>₹{{ $order->total_price + $order->discount_amount }}</span>
        </div>
        @if(($order->discount_amount ?? 0) > 0)
          <div style="display:flex; justify-content:space-between; font-size:12px; color:#16A34A; font-weight:700;">
            <span>Bill Discount:</span>
            <span>-₹{{ $order->discount_amount }}</span>
          </div>
        @endif
        @if(($order->delivery_charge ?? 0) > 0)
          <div style="display:flex; justify-content:space-between; font-size:12px; color:#555;">
            <span>Delivery Charges:</span>
            <span>+₹{{ $order->delivery_charge }}</span>
          </div>
        @endif
        <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:800; border-top:1px solid #E5E7EB; padding-top:4px; margin-top:2px;">
          <span>Grand Total:</span>
          <span style="font-weight:900; color:#1A3C8F;">₹{{ $order->total_price + $order->delivery_charge }}</span>
        </div>
      </div>
    </div>

    <!-- Interactive Delivery Route Map Card -->
    <div style="background:#fff; border-radius:20px; padding:16px; border:1px solid #E5E7EB; width:100%; box-shadow:0 2px 8px rgba(0,0,0,0.02); margin-top:10px;">
      <h4 style="font-weight:800; font-size:13.5px; color:#1A1A1A; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
        🗺️ Delivery Route Map
      </h4>
      
      <div id="delivery-route-map" style="width:100%; height:200px; border-radius:12px; border:1px solid #CBD5E0; z-index:9;"></div>
      
      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; font-size:11.5px; font-weight:700; color:#4A5568;">
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
        <span>🏪 Shop: <span style="color:#1A3C8F;">{{ $order->shop->name }}</span></span>
        <span>📍 Distance: <span style="color:#10B981;">{{ $realDist }} KM</span></span>
      </div>
    </div>

    <!-- Dynamic Status / Wait Box Info -->
    <div id="status-card-wrapper" style="width: 100%; margin-top: 10px;">
      @if($order->status === 'Pending')
        <div style="background:#FFFBEB; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #FDE68A;">
          <div style="font-size:13.5px; color:#D97706; font-weight:800;">⏳ Order Status: Pending (Wait karein...)</div>
          <div style="font-size:12px; color:#B45309; margin-top:4px; font-weight: 500;">Dukandaar aapka order review kar rahe hain. 2-3 min me response aayega.</div>
        </div>
      @elseif($order->status === 'Accepted')
        <div style="background:#ECFDF5; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #A7F3D0;">
          <div style="font-size:13.5px; color:#059669; font-weight:800;">✅ Order Status: Accepted!</div>
          <div style="font-size:12px; color:#047857; margin-top:4px; font-weight: 500;">Dukandaar ne aapka order <strong>accept</strong> kar liya hai aur packing shuru ho gayi hai!</div>
        </div>
      @elseif($order->status === 'Cancelled')
        <div style="background:#FEF2F2; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #FCA5A5;">
          <div style="font-size:13.5px; color:#DC2626; font-weight:800;">❌ Order Status: Rejected / Cancelled</div>
          <div style="font-size:12px; color:#B91C1C; margin-top:4px; font-weight: 500;">Sorry, dukandaar ne aapka order reject/cancel kar diya hai.</div>
        </div>
      @elseif($order->status === 'Delivered')
        <div style="background:#F0FDF4; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #BBF7D0;">
          <div style="font-size:13.5px; color:#16A34A; font-weight:800;">📦 Order Status: Delivered</div>
          <div style="font-size:12px; color:#15803D; margin-top:4px; font-weight: 500;">Order successfully deliver ho gaya hai. Dawalo ko use karne ke liye dhanyawaad!</div>
        </div>
      @else
        <div style="background:#F3F4F6; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #E5E7EB;">
          <div style="font-size:13.5px; color:#4B5563; font-weight:800;">ℹ️ Order Status: {{ $order->status }}</div>
        </div>
      @endif
    </div>
  </div>

  <a href="{{ url('/') }}" class="btn-blue" style="width:100%; max-width: 320px; padding:16px; border:none; border-radius:16px; color:#fff; font-weight:800; font-size:15px; text-decoration:none; display:inline-block; text-align:center; margin-top: 20px;">
    ← Wapas Home Pe Jaayein
  </a>
</div>

<!-- Route map script initializer -->
<script>
  window.addEventListener('DOMContentLoaded', () => {
    // Get shop coordinates, fallback to defaults
    const shopLat = parseFloat("{{ $shopLat }}");
    const shopLng = parseFloat("{{ $shopLng }}");
    
    // Get live customer geolocated coordinates from active session
    const custLat = parseFloat("{{ $uLat }}") || (shopLat - 0.008);
    const custLng = parseFloat("{{ $uLng }}") || (shopLng + 0.006);

    const routeMap = L.map('delivery-route-map', { zoomControl: false }).setView([shopLat, shopLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(routeMap);

    // Custom Markers
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

    const shopMarker = L.marker([shopLat, shopLng], { icon: shopIcon }).addTo(routeMap).bindPopup("Pharmacy Store Location");
    const customerMarker = L.marker([custLat, custLng], { icon: customerIcon }).addTo(routeMap).bindPopup("Customer Location");

    // Draw route path line
    const routeLine = L.polyline([
      [shopLat, shopLng],
      [custLat, custLng]
    ], { color: '#1A3C8F', weight: 4, dashArray: '6, 6' }).addTo(routeMap);

    // Zoom map fit bounds
    const group = new L.featureGroup([shopMarker, customerMarker]);
    routeMap.fitBounds(group.getBounds().pad(0.2));
  });

  // Polling order status every 4 seconds in the background
  const orderId = "{{ $order->id }}";
  let currentStatus = "{{ $order->status }}";

  function checkOrderStatus() {
      if (currentStatus === 'Cancelled' || currentStatus === 'Delivered') {
          return; // Stop polling if final status is reached
      }

      fetch(`/order/${orderId}/status`)
          .then(res => res.json())
          .then(data => {
              if (data.success && data.status !== currentStatus) {
                  currentStatus = data.status;
                  
                  // Re-render status card
                  const wrapper = document.getElementById('status-card-wrapper');
                  if (wrapper) {
                      let html = '';
                      if (currentStatus === 'Pending') {
                          html = `
                              <div style="background:#FFFBEB; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #FDE68A;">
                                  <div style="font-size:13.5px; color:#D97706; font-weight:800;">⏳ Order Status: Pending (Wait karein...)</div>
                                  <div style="font-size:12px; color:#B45309; margin-top:4px; font-weight: 500;">Dukandaar aapka order review kar rahe hain. 2-3 min me response aayega.</div>
                              </div>
                          `;
                      } else if (currentStatus === 'Accepted') {
                          html = `
                              <div style="background:#ECFDF5; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #A7F3D0;">
                                  <div style="font-size:13.5px; color:#059669; font-weight:800;">✅ Order Status: Accepted!</div>
                                  <div style="font-size:12px; color:#047857; margin-top:4px; font-weight: 500;">Dukandaar ne aapka order <strong>accept</strong> kar liya hai aur packing shuru ho gayi hai!</div>
                              </div>
                          `;
                      } else if (currentStatus === 'Cancelled') {
                          html = `
                              <div style="background:#FEF2F2; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #FCA5A5;">
                                  <div style="font-size:13.5px; color:#DC2626; font-weight:800;">❌ Order Status: Rejected / Cancelled</div>
                                  <div style="font-size:12px; color:#B91C1C; margin-top:4px; font-weight: 500;">Sorry, dukandaar ne aapka order reject/cancel kar diya hai.</div>
                              </div>
                          `;
                      } else if (currentStatus === 'Delivered') {
                          html = `
                              <div style="background:#F0FDF4; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #BBF7D0;">
                                  <div style="font-size:13.5px; color:#16A34A; font-weight:800;">📦 Order Status: Delivered</div>
                                  <div style="font-size:12px; color:#15803D; margin-top:4px; font-weight: 500;">Order successfully deliver ho gaya hai. Dawalo ko use karne ke liye dhanyawaad!</div>
                              </div>
                          `;
                      } else {
                          html = `
                              <div style="background:#F3F4F6; border-radius:16px; padding:14px 20px; text-align:center; border: 1px solid #E5E7EB;">
                                  <div style="font-size:13.5px; color:#4B5563; font-weight:800;">ℹ️ Order Status: ${currentStatus}</div>
                              </div>
                          `;
                      }
                      wrapper.innerHTML = html;
                  }
              }
          })
          .catch(err => console.error('Error fetching order status:', err));
  }

  // Run every 4 seconds
  setInterval(checkOrderStatus, 4000);
</script>
@endsection
