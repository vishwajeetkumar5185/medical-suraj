@extends('layouts.app')

@section('seo_title', 'Checkout - Dawalo')

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
      <a href="{{ url('/smartcart') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">🛒 Checkout</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:12px; margin:0;">Review and place your order</p>
      </div>
      <div style="background:rgba(255,255,255,0.2); border-radius:8px; padding:6px 12px;">
        <span style="color:#fff; font-size:12px; font-weight:700;">🔒 Secure</span>
      </div>
    </div>
  </div>

  <!-- Alerts -->
  <div style="padding:16px 16px 0 16px;">
    @if(session('success'))
      <div style="background:#D1FAE5; border-left:4px solid #10B981; border-radius:12px; padding:12px 16px; margin-bottom:16px;">
        <div style="font-size:13px; color:#059669; font-weight:700;">✓ {{ session('success') }}</div>
      </div>
    @endif

    @if(session('error'))
      <div style="background:#FEE2E2; border-left:4px solid #EF4444; border-radius:12px; padding:12px 16px; margin-bottom:16px;">
        <div style="font-size:13px; color:#DC2626; font-weight:700;">⚠️ {{ session('error') }}</div>
      </div>
    @endif

    @if($errors->any())
      <div style="background:#FEE2E2; border-left:4px solid #EF4444; border-radius:12px; padding:12px 16px; margin-bottom:16px;">
        @foreach($errors->all() as $error)
          <div style="font-size:13px; color:#DC2626; font-weight:600; margin-bottom:4px;">• {{ $error }}</div>
        @endforeach
      </div>
    @endif
  </div>

  <form id="checkout-form" action="{{ url('/order') }}" method="POST">
    @csrf
    <input type="hidden" name="shop_id" value="{{ $defaultShop->id }}">

    <div style="padding:16px;">
      
      <!-- Hidden input for delivery mode (always delivery) -->
      <input type="hidden" name="mode" value="delivery">

      <!-- Delivery Address Card -->
      <div id="delivery-address-section" style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0 0 16px 0;">📍 Delivery Address</h3>
        
        <div style="display:flex; flex-direction:column; gap:14px;">
          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">👤 Full Name</label>
            <input 
              type="text" 
              name="address_name" 
              id="addr-name"
              value="{{ Auth::user() ? Auth::user()->name : '' }}"
              placeholder="e.g. Rahul Sharma" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;"
              onfocus="this.style.borderColor='#0EA5E9';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>

          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">🏠 Flat, House No., Building</label>
            <input 
              type="text" 
              name="address_line1" 
              id="addr-line1"
              placeholder="e.g. H.No 45, Second Floor" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;"
              onfocus="this.style.borderColor='#0EA5E9';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>

          <div>
            <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">🛣️ Area, Colony, Street</label>
            <input 
              type="text" 
              name="address_line2" 
              id="addr-line2"
              placeholder="e.g. Near Shiv Mandir, Main Road" 
              required
              style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;"
              onfocus="this.style.borderColor='#0EA5E9';"
              onblur="this.style.borderColor='#E5E7EB';"
            >
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
              <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">🏙️ City</label>
              <input 
                type="text" 
                name="address_city" 
                id="addr-city"
                value="Muzaffarpur" 
                required
                style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#0EA5E9';"
                onblur="this.style.borderColor='#E5E7EB';"
              >
            </div>
            <div>
              <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">📮 Pincode</label>
              <input 
                type="text" 
                name="address_pincode" 
                id="addr-pin"
                value="842001" 
                placeholder="6-digit" 
                required
                style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#0EA5E9';"
                onblur="this.style.borderColor='#E5E7EB';"
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items Card -->
      <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
          <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0;">💊 Order Items</h3>
          <span style="font-size:12px; color:#64748B; font-weight:700;">{{ count($cartItems) }} Item{{ count($cartItems) > 1 ? 's' : '' }}</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($cartItems as $item)
            @php
              $qty = $cart[$item->id] ?? 1;
              $subtotal = (float)$item->price * $qty;
              $imgSrc = null;
              if (!empty($item->images) && is_array($item->images)) {
                $imgSrc = $item->images[0];
              }
              $fullImgUrl = $imgSrc ? ((strpos($imgSrc, 'http://') === 0 || strpos($imgSrc, 'https://') === 0) ? $imgSrc : asset($imgSrc)) : null;
            @endphp
            <div style="background:#F9FAFB; border-radius:12px; padding:14px; border:1px solid #E5E7EB;" id="cart-item-row-{{ $item->id }}">
              <!-- Top Row: Image + Info + Price -->
              <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:12px;">
                <!-- Image -->
                <div style="width:60px; height:60px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:1px solid #E5E7EB;">
                  @if($fullImgUrl)
                    <img src="{{ $fullImgUrl }}" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:contain;" alt="{{ $item->name }}" onerror="this.outerHTML='<span style=font-size:32px;>{{ $item->emoji ?? '💊' }}</span>'">
                  @else
                    <span style="font-size:32px;">{{ $item->emoji ?? '💊' }}</span>
                  @endif
                </div>

                <!-- Info + Price -->
                <div style="flex:1; min-width:0;">
                  <div style="font-weight:800; font-size:15px; color:#1A1A1A; margin-bottom:4px; line-height:1.3;">{{ $item->name }}</div>
                  <div style="font-size:12px; color:#64748B; margin-bottom:6px;">
                    ₹{{ number_format($item->price, 2) }} each
                    @if($item->mrp > $item->price)
                      <span style="text-decoration:line-through; color:#94A3B8; margin-left:4px;">₹{{ number_format($item->mrp, 2) }}</span>
                    @endif
                  </div>
                  <div style="font-weight:800; font-size:18px; color:#0EA5E9;">₹{{ number_format($subtotal, 2) }}</div>
                </div>
              </div>

              <!-- Bottom Row: Quantity Controls + Delete -->
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <!-- Quantity Controls -->
                <div style="display:flex; align-items:center; border:2px solid #0EA5E9; border-radius:10px; overflow:hidden; background:#fff;">
                  <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty - 1 }})" style="background:#F0F9FF; border:none; padding:8px 14px; font-weight:800; font-size:18px; cursor:pointer; color:#0EA5E9;">−</button>
                  <span style="padding:8px 16px; font-weight:800; font-size:16px; color:#1A1A1A; min-width:40px; text-align:center;" id="item-qty-val-{{ $item->id }}">{{ $qty }}</span>
                  <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty + 1 }})" style="background:#F0F9FF; border:none; padding:8px 14px; font-weight:800; font-size:18px; cursor:pointer; color:#0EA5E9;">+</button>
                </div>

                <!-- Delete Button -->
                <button type="button" onclick="updateItemQuantity({{ $item->id }}, 0)" style="background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; padding:10px 12px; border-radius:10px; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; min-width:44px;" title="Remove item">
                  🗑️
                </button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Add More Link -->
        <div style="margin-top:16px;">
          <a href="{{ url('/smartcart') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; color:#0EA5E9; font-weight:800; font-size:14px; text-decoration:none; background:#F0F9FF; padding:12px 16px; border-radius:10px; border:2px dashed #0EA5E9; transition:all 0.2s;">
            <span style="font-size:18px;">➕</span>
            <span>Add More Medicines</span>
          </a>
        </div>
      </div>

      <!-- Order Summary Card -->
      <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size:16px; font-weight:800; color:#1A1A1A; margin:0 0 16px 0; padding-bottom:12px; border-bottom:2px solid #F1F5F9;">💰 Order Summary</h3>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#64748B;">
          <span>Items Subtotal:</span>
          <span style="font-weight:800; color:#1A1A1A;">₹{{ number_format($itemsTotal, 2) }}</span>
        </div>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#64748B;" id="summary-delivery-row">
          <span>Delivery Fee:</span>
          <span style="font-weight:800;" id="summary-delivery-text">
            {{ $deliveryCharge > 0 ? '₹' . number_format($deliveryCharge, 2) : 'FREE' }}
          </span>
        </div>

        @if($discountAmount > 0)
          <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#10B981; font-weight:800;" id="summary-discount-row">
            <span>Coupon Discount:</span>
            <span>-₹{{ number_format($discountAmount, 2) }}</span>
          </div>
        @endif
        
        <div style="border-top:2px solid #E5E7EB; padding-top:14px; margin-top:10px; display:flex; justify-content:space-between; margin-bottom:20px;">
          <span style="font-size:16px; font-weight:800; color:#1A1A1A;">Total Payable:</span>
          <span style="font-size:22px; font-weight:800; color:#0EA5E9;" id="summary-total-text">
            ₹{{ number_format(max(0, $itemsTotal - $discountAmount + $deliveryCharge), 2) }}
          </span>
        </div>

        <!-- Place Order Button -->
        <button 
          type="submit" 
          style="width:100%; padding:14px; background:linear-gradient(135deg, #FACC15, #EAB308); color:#1A1A1A; border:none; border-radius:10px; font-size:15px; font-weight:800; cursor:pointer; box-shadow:0 4px 12px rgba(250,204,21,0.3); transition:transform 0.2s;"
          onmouseover="this.style.transform='translateY(-2px)';"
          onmouseout="this.style.transform='translateY(0)';"
        >
          🚀 Place Your Order
        </button>

        <div style="font-size:11px; text-align:center; color:#94A3B8; margin-top:12px; line-height:1.4;">
          🔒 Secure checkout. Orders fulfilled by Dawalo.
        </div>
      </div>

    </div>
  </form>

</div>

<script>
  const baseItemsTotal = {{ $itemsTotal }};
  const baseDeliveryCharge = {{ $deliveryCharge }};
  const baseDiscount = {{ $discountAmount }};

  function updateItemQuantity(medId, newQty) {
    if (newQty < 0) newQty = 0;
    
    fetch("{{ url('/cart/update') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        medicine_id: medId,
        qty: newQty
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      }
    })
    .catch(err => {
      console.error(err);
      window.location.reload();
    });
  }
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
      <div style="width:48px; height:48px; background:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:4px; box-shadow:0 2px 8px rgba(59,130,246,0.3);">
        <span style="font-size:22px;">🛒</span>
      </div>
      @if($cartCount > 0)
        <span style="position:absolute; top:-4px; right:4px; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px; min-width:18px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);">{{ $cartCount }}</span>
      @endif
      <span style="font-size:11px; font-weight:700; color:#3B82F6;">Cart</span>
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
