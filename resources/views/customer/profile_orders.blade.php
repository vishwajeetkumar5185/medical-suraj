@extends('layouts.app')

@section('seo_title', 'My Orders - Dawalo')

@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url('/profile') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">📋 My Orders</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:12px; margin:0;">View your order history</p>
      </div>
    </div>
  </div>

  <div style="padding:16px;">
    
    <div style="display:flex; background:#fff; padding:4px; gap:4px; border-radius:12px; margin-bottom:16px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
      <button onclick="switchTab('med-orders')" id="tab-med-orders" style="flex:1; border:none; border-radius:10px; padding:12px; font-weight:800; font-size:13px; cursor:pointer; background:#0EA5E9; color:#fff; transition:all 0.2s;">
        🛒 Medicine Orders
      </button>
      <button onclick="switchTab('rx-orders')" id="tab-rx-orders" style="flex:1; border:none; border-radius:10px; padding:12px; font-weight:800; font-size:13px; cursor:pointer; background:#F3F4F6; color:#64748B; transition:all 0.2s;">
        📋 Prescriptions
      </button>
    </div>

    <div id="med-orders-list" style="display:flex; flex-direction:column; gap:12px;">
      @if($orders->isEmpty())
        <div style="background:#fff; border-radius:16px; padding:40px 20px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
          <div style="font-size:56px; margin-bottom:12px;">📦</div>
          <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:8px;">No Orders Yet</h3>
          <p style="font-size:13px; color:#64748B; margin-bottom:20px;">Start shopping and place your first order!</p>
          <a href="{{ url('/smartcart') }}" style="display:inline-block; padding:12px 24px; background:#0EA5E9; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; font-size:14px;">
            🛒 Go to Cart
          </a>
        </div>
      @else
        @foreach($orders as $order)
          <div style="background:#fff; border-radius:16px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
              <div>
                <div style="font-size:11px; color:#94A3B8; font-weight:700; margin-bottom:4px;">ORDER #{{ $order->id }}</div>
                <div style="font-size:15px; font-weight:800; color:#1A1A1A;">🏪 {{ $order->shop->name }}</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">{{ $order->created_at->format('d M Y, h:i A') }}</div>
              </div>
              <span style="background:{{ $order->status === 'Delivered' ? '#D1FAE5' : ($order->status === 'Cancelled' ? '#FEE2E2' : '#DBEAFE') }}; color:{{ $order->status === 'Delivered' ? '#059669' : ($order->status === 'Cancelled' ? '#DC2626' : '#0284C7') }}; font-size:11px; font-weight:800; padding:6px 12px; border-radius:8px;">
                {{ $order->status }}
              </span>
            </div>

            <div style="background:#F9FAFB; border-radius:10px; padding:12px; margin-bottom:12px;">
              @foreach($order->items as $item)
                <div style="display:flex; justify-content:space-between; padding:6px 0; font-size:13px;">
                  <span style="color:#64748B;">{{ $item['emoji'] ?? '💊' }} {{ $item['name'] }} (×{{ $item['quantity'] }})</span>
                  <span style="font-weight:700; color:#1A1A1A;">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                </div>
              @endforeach
            </div>

            @if($order->delivery_address)
              <div style="background:#F0F9FF; border-left:3px solid #0EA5E9; padding:10px 12px; border-radius:8px; margin-bottom:12px;">
                <div style="font-size:11px; color:#0284C7; font-weight:700; margin-bottom:4px;">DELIVERY ADDRESS</div>
                <div style="font-size:12px; color:#1E3A8A;">📍 {{ $order->delivery_address }}</div>
              </div>
            @endif

            <div style="display:flex; justify-content:space-between; align-items:center; padding-top:12px; border-top:2px solid #F1F5F9;">
              <span style="font-size:14px; font-weight:700; color:#64748B;">Grand Total:</span>
              <span style="font-size:18px; font-weight:800; color:#0EA5E9;">₹{{ number_format($order->total_price + $order->delivery_charge, 2) }}</span>
            </div>
          </div>
        @endforeach
      @endif
    </div>

    <div id="rx-orders-list" style="display:none; flex-direction:column; gap:12px;">
      @if($prescriptions->isEmpty())
        <div style="background:#fff; border-radius:16px; padding:40px 20px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
          <div style="font-size:56px; margin-bottom:12px;">📋</div>
          <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:8px;">No Prescriptions</h3>
          <p style="font-size:13px; color:#64748B; margin-bottom:20px;">Upload a prescription to get started!</p>
          <a href="{{ url('/prescription/upload') }}" style="display:inline-block; padding:12px 24px; background:#10B981; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; font-size:14px;">
            📤 Upload Prescription
          </a>
        </div>
      @else
        @foreach($prescriptions as $rx)
          <div style="background:#fff; border-radius:16px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
              <div>
                <div style="font-size:11px; color:#94A3B8; font-weight:700; margin-bottom:4px;">PRESCRIPTION #RX-{{ str_pad($rx->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size:15px; font-weight:800; color:#1A1A1A;">🏪 {{ $rx->shop->name ?? 'Pharmacy' }}</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">{{ $rx->created_at->format('d M Y, h:i A') }}</div>
              </div>
              <span style="background:{{ $rx->status === 'Delivered' ? '#D1FAE5' : ($rx->status === 'Cancelled' ? '#FEE2E2' : '#DBEAFE') }}; color:{{ $rx->status === 'Delivered' ? '#059669' : ($rx->status === 'Cancelled' ? '#DC2626' : '#0284C7') }}; font-size:11px; font-weight:800; padding:6px 12px; border-radius:8px;">
                {{ $rx->status }}
              </span>
            </div>

            <div style="display:flex; gap:12px; background:#F9FAFB; border-radius:10px; padding:12px; margin-bottom:12px;">
              <a href="{{ $rx->image_path }}" target="_blank" style="flex-shrink:0;">
                <img src="{{ $rx->image_path }}" style="width:60px; height:60px; border-radius:8px; object-fit:cover; border:2px solid #E5E7EB;" alt="RX">
              </a>
              <div style="flex:1; font-size:13px;">
                <div style="font-weight:700; color:#1A1A1A; margin-bottom:4px;">{{ $rx->patient_name }} ({{ $rx->patient_age ?? 'N/A' }} Yrs)</div>
                <div style="font-size:11px; color:#64748B;">📍 {{ Str::limit($rx->delivery_address, 50) }}</div>
              </div>
            </div>

            <div style="text-align:right;">
              <a href="{{ url('/prescription/'.$rx->id.'/success') }}" style="display:inline-block; font-size:13px; font-weight:700; color:#0EA5E9; text-decoration:none; padding:8px 16px; background:#F0F9FF; border-radius:8px;">
                View Details →
              </a>
            </div>
          </div>
        @endforeach
      @endif
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
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Profile</span>
    </a>
  </div>

</div>

<script>
  function switchTab(tab) {
    const medBtn = document.getElementById('tab-med-orders');
    const rxBtn = document.getElementById('tab-rx-orders');
    const medList = document.getElementById('med-orders-list');
    const rxList = document.getElementById('rx-orders-list');

    if (tab === 'med-orders') {
      medBtn.style.background = '#0EA5E9';
      medBtn.style.color = '#fff';
      rxBtn.style.background = '#F3F4F6';
      rxBtn.style.color = '#64748B';
      medList.style.display = 'flex';
      rxList.style.display = 'none';
    } else {
      rxBtn.style.background = '#0EA5E9';
      rxBtn.style.color = '#fff';
      medBtn.style.background = '#F3F4F6';
      medBtn.style.color = '#64748B';
      medList.style.display = 'none';
      rxList.style.display = 'flex';
    }
  }
</script>

@endsection
