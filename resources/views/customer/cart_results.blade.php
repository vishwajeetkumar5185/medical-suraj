@extends('layouts.app')

@section('seo_title', 'Secure Checkout - Review Medicine Order & Checkout | Dawalo')
@section('seo_description', 'Complete your medicine order securely. Enter delivery address, review pricing breakdown, and place orders instantly.')
@section('seo_keywords', 'checkout medicines, secure order checkout, dawalo order summary, medicine order processing')

@section('content')
<div class="screen">
  <!-- Minimalist Secure Checkout Header -->
  <div style="background: #1A202C; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-radius: 16px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <a href="{{ url('/') }}" style="color: #fff; text-decoration: none; font-size: 16px; font-weight: 700;">← Home</a>
      <span style="color: #4A5568; font-size: 18px;">|</span>
      <span style="color: #CBD5E0; font-size: 13px; font-weight: 800; text-transform: uppercase;">Checkout</span>
    </div>
    <div style="color: #4ADE80; font-size: 12px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
      🔒 Secure Checkout
    </div>
  </div>

  <!-- Session Alerts Banners -->
  @if(session('success'))
    <div style="background:#DCFCE7; color:#16A34A; padding:12px 16px; border-radius:12px; font-weight:800; font-size:13px; margin-bottom:16px; border:1px solid #BBF7D0;">
      ✓ {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div style="background:#FEE2E2; color:#DC2626; padding:12px 16px; border-radius:12px; font-weight:800; font-size:13px; margin-bottom:16px; border:1px solid #FCA5A5;">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#FEE2E2; color:#DC2626; padding:12px 16px; border-radius:12px; font-weight:800; font-size:13px; margin-bottom:16px; border:1px solid #FCA5A5;">
      @foreach($errors->all() as $error)
        <div>• {{ $error }}</div>
      @endforeach
    </div>
  @endif

  <style>
    @media (min-width: 992px) {
      .checkout-grid {
        flex-direction: row !important;
        align-items: flex-start;
      }
    }
  </style>

  <div class="scroll" style="flex:1;">
    <form id="checkout-form" action="{{ url('/order') }}" method="POST">
      @csrf
      <input type="hidden" name="shop_id" value="{{ $defaultShop->id }}">

      <!-- 2-Column Grid -->
      <div class="checkout-grid" style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- Left Side: Delivery Details & Items -->
        <div style="flex: 2; display: flex; flex-direction: column; gap: 16px;">
          
          <!-- SECTION 1: Select Delivery Mode -->
          <div style="background: #fff; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
              <span style="background: #1A3C8F; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #fff;">1</span>
              <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Select Delivery Mode</h3>
            </div>

            <div style="display: flex; gap: 12px;">
              <!-- Home Delivery Option -->
              <label style="flex: 1; border: 2.5px solid #1A3C8F; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: #EEF2FF; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-delivery">
                <input type="radio" name="mode" value="delivery" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" checked onclick="updateDeliveryMode('delivery')">
                <div style="font-size: 26px; margin-bottom: 4px;">🛵</div>
                <div style="font-weight: 800; font-size: 13.5px; color: #1A1A1A;">Home Delivery</div>
                <div style="margin-top: 6px; background: #FEF3C7; color: #D97706; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;" id="delivery-fee-badge">
                  {{ $deliveryCharge > 0 ? '+₹' . $deliveryCharge : 'FREE' }}
                </div>
              </label>

              <!-- Self Pickup Option -->
              <label style="flex: 1; border: 1.5px solid #E5E7EB; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: #fff; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-pickup">
                <input type="radio" name="mode" value="pickup" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" onclick="updateDeliveryMode('pickup')">
                <div style="font-size: 26px; margin-bottom: 4px;">🚶</div>
                <div style="font-weight: 800; font-size: 13.5px; color: #1A1A1A;">Self Pickup</div>
                <div style="margin-top: 6px; background: #DCFCE7; color: #16A34A; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;">FREE</div>
              </label>
            </div>
          </div>

          <!-- SECTION 2: Shipping / Delivery Address -->
          <div id="delivery-address-section" style="background: #fff; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
              <span style="background: #1A3C8F; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #fff;">2</span>
              <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Enter Delivery Address</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
              <div>
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: #4B5563; margin-bottom: 4px; display: block;">Full Name *</label>
                <input type="text" name="address_name" class="form-input" placeholder="e.g. Rahul Sharma" id="addr-name" value="{{ Auth::user() ? Auth::user()->name : '' }}" required style="width:100%; padding:11px; border-radius:10px; border:1.5px solid #CBD5E1;">
              </div>
              
              <div>
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: #4B5563; margin-bottom: 4px; display: block;">Flat, House No., Building *</label>
                <input type="text" name="address_line1" class="form-input" placeholder="e.g. H.No 45, Second Floor" id="addr-line1" required style="width:100%; padding:11px; border-radius:10px; border:1.5px solid #CBD5E1;">
              </div>
              
              <div>
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: #4B5563; margin-bottom: 4px; display: block;">Area, Colony, Street, Sector *</label>
                <input type="text" name="address_line2" class="form-input" placeholder="e.g. Near Shiv Mandir, Main Road" id="addr-line2" required style="width:100%; padding:11px; border-radius:10px; border:1.5px solid #CBD5E1;">
              </div>
              
              <div style="display: flex; gap: 12px;">
                <div style="flex:1;">
                  <label class="form-label" style="font-weight: 700; font-size: 12px; color: #4B5563; margin-bottom: 4px; display: block;">Town / City *</label>
                  <input type="text" name="address_city" class="form-input" value="Muzaffarpur" id="addr-city" required style="width:100%; padding:11px; border-radius:10px; border:1.5px solid #CBD5E1;">
                </div>
                <div style="flex:1;">
                  <label class="form-label" style="font-weight: 700; font-size: 12px; color: #4B5563; margin-bottom: 4px; display: block;">Pincode *</label>
                  <input type="text" name="address_pincode" class="form-input" placeholder="6-digit pincode" id="addr-pin" value="842001" required style="width:100%; padding:11px; border-radius:10px; border:1.5px solid #CBD5E1;">
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 3: Review Ordered Items -->
          <div style="background: #fff; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: #1A3C8F; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #fff;">3</span>
                <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Review Ordered Medicines</h3>
              </div>
              <span style="font-size: 12px; color: #64748B; font-weight: 700;">{{ count($cartItems) }} Item{{ count($cartItems) > 1 ? 's' : '' }}</span>
            </div>

            <!-- Items List -->
            <div style="display: flex; flex-direction: column;">
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
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #F3F4F6;" id="cart-item-row-{{ $item->id }}">
                  <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
                    <div style="width: 44px; height: 44px; background: #F8FAFC; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; border: 1px solid #E2E8F0;">
                      @if($fullImgUrl)
                        <img src="{{ $fullImgUrl }}" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:contain;" alt="{{ $item->name }}" onerror="this.outerHTML='<span style=\\'font-size:24px;\\'>{{ $item->emoji ?? '💊' }}</span>'">
                      @else
                        <span style="font-size: 24px;">{{ $item->emoji ?? '💊' }}</span>
                      @endif
                    </div>
                    <div>
                      <div style="font-weight: 800; font-size: 14px; color: #1A1A1A;">{{ $item->name }}</div>
                      <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                        ₹{{ number_format($item->price, 2) }} each
                        @if($item->mrp > $item->price)
                          <span style="text-decoration: line-through; color: #94A3B8; margin-left: 4px;">₹{{ number_format($item->mrp, 2) }}</span>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div style="display: flex; align-items: center; gap: 12px;">
                    <!-- Quantity Increment / Decrement -->
                    <div style="display: flex; align-items: center; border: 1.5px solid #CBD5E0; border-radius: 8px; overflow: hidden; background: #F8FAFC;">
                      <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty - 1 }})" style="background: #EDF2F7; border: none; padding: 5px 12px; font-weight: 900; font-size: 15px; cursor: pointer; color: #4A5568;" title="Decrease quantity">-</button>
                      <span style="padding: 5px 12px; font-weight: 800; font-size: 13px; color: #1A202C;" id="item-qty-val-{{ $item->id }}">{{ $qty }}</span>
                      <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty + 1 }})" style="background: #EDF2F7; border: none; padding: 5px 12px; font-weight: 900; font-size: 15px; cursor: pointer; color: #4A5568;" title="Increase quantity">+</button>
                    </div>

                    <!-- Item Price Subtotal -->
                    <div style="min-width: 70px; text-align: right;">
                      <span style="font-weight: 900; font-size: 14px; color: #1E293B;">₹{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <!-- Delete Button -->
                    <button type="button" onclick="updateItemQuantity({{ $item->id }}, 0)" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; padding: 6px 10px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;" title="Delete item">
                      🗑️
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Add More Items Action Link -->
            <div style="margin-top: 16px; display: flex; justify-content: flex-start;">
              <a href="{{ url('/smartcart') }}" style="color: #2563EB; font-weight: 800; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; padding: 10px 16px; border-radius: 10px; border: 1px dashed #93C5FD; transition: background 0.2s ease;">
                ➕ Add More Medicines
              </a>
            </div>
          </div>
        </div>

        <!-- Right Side: Order Summary Sidebar -->
        <div style="flex: 1; display: flex; flex-direction: column; gap: 16px;">
          <div style="background: #fff; border: 1.5px solid #CBD5E0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); position: sticky; top: 20px;">
            <h3 style="font-weight: 900; font-size: 17px; color: #1D2D44; margin-top: 0; margin-bottom: 16px; border-bottom: 2px solid #F1F5F9; padding-bottom: 10px;">Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13.5px; color: #4A5568;">
              <span>Items Subtotal:</span>
              <span style="font-weight: 800; color: #2D3748;">₹{{ number_format($itemsTotal, 2) }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13.5px; color: #4A5568;" id="summary-delivery-row">
              <span>Delivery Fee:</span>
              <span style="font-weight: 800; color: {{ $deliveryCharge > 0 ? '#B7791F' : '#16A34A' }};" id="summary-delivery-text">
                {{ $deliveryCharge > 0 ? '₹' . number_format($deliveryCharge, 2) : 'FREE' }}
              </span>
            </div>

            @if($discountAmount > 0)
              <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13.5px; color: #16A34A; font-weight: 800;" id="summary-discount-row">
                <span>Coupon Discount:</span>
                <span>-₹{{ number_format($discountAmount, 2) }}</span>
              </div>
            @endif
            
            <div style="border-top: 2px solid #E2E8F0; padding-top: 14px; margin-top: 6px; display: flex; justify-content: space-between; margin-bottom: 20px;">
              <span style="font-size: 16px; font-weight: 900; color: #1A202C;">Total Payable:</span>
              <span style="font-size: 20px; font-weight: 900; color: #1D4ED8;" id="summary-total-text">
                ₹{{ number_format(max(0, $itemsTotal - $discountAmount + $deliveryCharge), 2) }}
              </span>
            </div>

            <!-- Amazon Style Checkout Place Order Button -->
            <button type="submit" class="btn-green" style="background: linear-gradient(180deg, #FACC15 0%, #EAB308 100%); border: 1px solid #CA8A04; border-radius: 12px; color: #1E293B; font-weight: 900; font-size: 15px; padding: 14px; width: 100%; cursor: pointer; box-shadow: 0 2px 6px rgba(234,179,8,0.3); transition: transform 0.1s ease;">
              🚀 Place Your Order
            </button>

            <div style="font-size: 11.5px; text-align: center; color: #64748B; margin-top: 12px; line-height: 1.4;">
              🔒 Orders are fulfilled directly by Dawalo Central Operations Team.
            </div>
          </div>
        </div>

      </div>
    </form>
  </div>
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

  function updateDeliveryMode(mode) {
    const labelDelivery = document.getElementById('label-delivery');
    const labelPickup = document.getElementById('label-pickup');
    const addressSection = document.getElementById('delivery-address-section');
    const deliveryText = document.getElementById('summary-delivery-text');
    const totalText = document.getElementById('summary-total-text');

    const addrName = document.getElementById('addr-name');
    const addrLine1 = document.getElementById('addr-line1');
    const addrLine2 = document.getElementById('addr-line2');
    const addrCity = document.getElementById('addr-city');
    const addrPin = document.getElementById('addr-pin');

    if (mode === 'delivery') {
      labelDelivery.style.borderColor = '#1A3C8F';
      labelDelivery.style.background = '#EEF2FF';
      labelPickup.style.borderColor = '#E5E7EB';
      labelPickup.style.background = '#fff';

      addressSection.style.display = 'block';
      addrName.required = true;
      addrLine1.required = true;
      addrLine2.required = true;
      addrCity.required = true;
      addrPin.required = true;

      const currentFee = baseDeliveryCharge;
      deliveryText.innerText = currentFee > 0 ? '₹' + currentFee.toFixed(2) : 'FREE';
      deliveryText.style.color = currentFee > 0 ? '#B7791F' : '#16A34A';
      
      const finalTotal = Math.max(0, baseItemsTotal - baseDiscount + currentFee);
      totalText.innerText = '₹' + finalTotal.toFixed(2);
    } else {
      labelPickup.style.borderColor = '#1A3C8F';
      labelPickup.style.background = '#EEF2FF';
      labelDelivery.style.borderColor = '#E5E7EB';
      labelDelivery.style.background = '#fff';

      addressSection.style.display = 'none';
      addrName.required = false;
      addrLine1.required = false;
      addrLine2.required = false;
      addrCity.required = false;
      addrPin.required = false;

      deliveryText.innerText = 'FREE';
      deliveryText.style.color = '#16A34A';

      const finalTotal = Math.max(0, baseItemsTotal - baseDiscount);
      totalText.innerText = '₹' + finalTotal.toFixed(2);
    }
  }

  // Initialize view mode state
  updateDeliveryMode('delivery');
</script>
@endsection
