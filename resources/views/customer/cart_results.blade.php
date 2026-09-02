@extends('layouts.app')

@section('seo_title', 'Secure Checkout - Review Medicine Order & Checkout | Dawalo')
@section('seo_description', 'Complete your medicine order securely. Review matching store details, delivery address, pricing breakdown, and place orders instantly.')
@section('seo_keywords', 'checkout medicines, secure order checkout, dawalo order summary, pharmacy order processing')

@section('content')
<div class="screen">
  <!-- Minimalist Secure Checkout Header -->
  <div style="background: #1A202C; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; border-radius: 14px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <a href="{{ url('/') }}" style="color: #fff; text-decoration: none; font-size: 16px;">← Home</a>
      <span style="color: #4A5568; font-size: 18px;">|</span>
      <span style="color: #CBD5E0; font-size: 13px; font-weight: 700; text-transform: uppercase;">Checkout</span>
    </div>
    <div style="color: #A0AEC0; font-size: 12px; display: flex; align-items: center; gap: 4px;">
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
      <input type="hidden" name="shop_id" value="{{ $bestMatch['shop']->id }}">

      <!-- Amazon Style 2-Column Grid -->
      <div class="checkout-grid" style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- Left Side: Accordion Sections -->
        <div style="flex: 2; display: flex; flex-direction: column; gap: 16px;">
          
          <!-- SECTION 1: Delivery Mode (Kaise lenge aap?) -->
          <div style="background: #fff; border: 1px solid #D2D6DC; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
              <span style="background: #E2E8F0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #4A5568;">1</span>
              <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Select Delivery Mode</h3>
            </div>

            @php
              $initMode = request('mode', 'pickup');
              $isDeliveryMode = ($initMode === 'delivery' && $bestMatch['shop']->delivery_enabled && !$bestMatch['isOutOfRadius']);
              $isNotDefaultPickupShop = request()->has('shop_id');
            @endphp

            <div style="display: flex; gap: 12px; flex-direction: column;">
              <div style="display: flex; gap: 12px;">
                <!-- Pickup Option -->
                <label style="flex: 1; border: {{ !$isDeliveryMode ? '2.5px solid #1A3C8F' : '1.5px solid #E5E7EB' }}; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: {{ !$isDeliveryMode ? '#EEF2FF' : '#fff' }}; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-pickup">
                  <input type="radio" name="mode" value="pickup" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" required {{ !$isDeliveryMode ? 'checked' : '' }}>
                  <div style="font-size: 24px; margin-bottom: 4px;">🚶</div>
                  <div style="font-weight: 800; font-size: 13px; color: #1A1A1A;">Self Pickup</div>
                  <div style="margin-top: 6px; background: #DCFCE7; color: #16A34A; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;">FREE</div>
                </label>

                <!-- Delivery Option -->
                @if($bestMatch['shop']->delivery_enabled && !$bestMatch['isOutOfRadius'])
                  <label style="flex: 1; border: {{ $isDeliveryMode ? '2.5px solid #1A3C8F' : '1.5px solid #E5E7EB' }}; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: {{ $isDeliveryMode ? '#EEF2FF' : '#fff' }}; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-delivery">
                    <input type="radio" name="mode" value="delivery" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" {{ $isDeliveryMode ? 'checked' : '' }}>
                    <div style="font-size: 24px; margin-bottom: 4px;">🛵</div>
                    <div style="font-weight: 800; font-size: 13px; color: #1A1A1A;">Home Delivery</div>
                    <div style="margin-top: 6px; background: #FEF3C7; color: #D97706; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;">+₹{{ $bestMatch['deliveryCharge'] }}</div>
                  </label>
                @else
                  <!-- Delivery Disabled or Out of Range - Clickable -->
                  @php
                    $deliveryAltShop = null;
                    foreach ($matches as $alt) {
                        if ($alt['shop']->id !== $bestMatch['shop']->id 
                            && $alt['shop']->delivery_enabled 
                            && !$alt['isOutOfRadius']
                            && count($alt['available']) > 0) {
                            $deliveryAltShop = $alt['shop'];
                            break;
                        }
                    }
                  @endphp
                  <div onclick="@if($deliveryAltShop) window.location.href='{{ url('/smartcart/results?shop_id=' . $deliveryAltShop->id . '&mode=delivery') }}' @else alert('Aapke area me koi bhi pharmacy delivery nahi de rahi hai abhi. Please Self Pickup select karein.') @endif" style="flex: 1; border: 1.5px dashed #FCA5A5; border-radius: 12px; padding: 14px 10px; background: #FEF2F2; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer; transition: all 0.2s;">
                    <div style="font-size: 20px;">🛵</div>
                    <div style="font-weight: 800; font-size: 12px; color: #DC2626; margin-top: 2px;">Delivery Unavailable</div>
                    <div style="font-size: 10px; color: #9B1C1C; margin-top: 4px;">{{ $deliveryAltShop ? 'Tap to find delivery store →' : 'No store delivers to your area' }}</div>
                  </div>
                @endif
              </div>

              <!-- Hidden Alternative Delivery Stores list box panel -->
              <div id="alternative-delivery-panel" style="display:none; background:#F8FAFF; border:1px solid #BFDBFE; border-radius:12px; padding:14px; margin-top:6px;">
                <div style="font-weight:800; font-size:12px; color:#1E3A8A; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                  🛵 Nearby pharmacies offering delivery to your location:
                </div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                  @php
                    $altCount = 0;
                  @endphp
                  @foreach($matches as $alt)
                    @if($alt['shop']->id !== $bestMatch['shop']->id && $alt['shop']->delivery_enabled && !$alt['isOutOfRadius'])
                      @php $altCount++; @endphp
                      <div onclick="selectAlternativeDeliveryShop({{ json_encode([
                        'id' => $alt['shop']->id,
                        'name' => $alt['shop']->name,
                        'area' => $alt['shop']->area,
                        'dist' => $alt['shop']->distance_km,
                        'charge' => $alt['deliveryCharge'],
                        'total' => $alt['totalPrice'],
                        'discount' => $alt['discount']
                      ]) }})" style="background:#fff; border:1.5px solid #E2E8F0; border-radius:10px; padding:10px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; transition:all 0.2s;" class="alt-shop-row" id="alt-shop-{{ $alt['shop']->id }}">
                        <div>
                          <div style="font-weight:800; font-size:12.5px; color:#1A1A1A;">🏪 {{ $alt['shop']->name }}</div>
                          <div style="font-size:10.5px; color:#666; margin-top:2px;">📍 {{ $alt['shop']->area }} • {{ $alt['shop']->distance_km }} km away</div>
                        </div>
                        <div style="text-align:right;">
                          <div style="font-weight:800; font-size:13px; color:#1A3C8F;">₹{{ $alt['totalPrice'] - $alt['discount'] }}</div>
                          <div style="font-size:10px; color:#059669; font-weight:700; margin-top:2px;">Delivery: +₹{{ $alt['deliveryCharge'] }}</div>
                        </div>
                      </div>
                    @endif
                  @endforeach
                  @if($altCount == 0)
                    <div style="font-size:11.5px; color:#666; text-align:center; padding:10px 0;">No other stores with delivery coverage found nearby.</div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: Shipping / Delivery Address (Shows only when Home Delivery selected) -->
          <div id="delivery-address-section" style="display: none; background: #fff; border: 1px solid #D2D6DC; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.3s;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
              <span style="background: #E2E8F0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #4A5568;">2</span>
              <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Enter Delivery Address</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
              <input type="text" name="address_name" class="form-input" placeholder="Full Name *" id="addr-name">
              <input type="text" name="address_line1" class="form-input" placeholder="Flat, House no., Building *" id="addr-line1">
              <input type="text" name="address_line2" class="form-input" placeholder="Area, Colony, Street, Sector *" id="addr-line2">
              <div style="display: flex; gap: 10px;">
                <input type="text" name="address_city" class="form-input" placeholder="Town/City *" value="Muzaffarpur" style="flex:1;" id="addr-city">
                <input type="text" name="address_pincode" class="form-input" placeholder="Pincode (6-digit) *" style="flex:1;" id="addr-pin">
              </div>
            </div>
          </div>

          <!-- SECTION 3: Review Items & Availability -->
          <div style="background: #fff; border: 1px solid #D2D6DC; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
              <span style="background: #E2E8F0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #4A5568;">3</span>
              <h3 style="font-weight: 900; font-size: 16px; color: #1A1A1A; margin: 0;">Review Items & Pharmacy</h3>
            </div>

            <!-- Matched Pharmacy Details / Selection Dropdown -->
            <div style="background: #F8FAFF; border: 1.5px solid #BFDBFE; border-radius: 12px; padding: 14px; margin-bottom: 14px;">
              <div id="matched-pharmacy-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label for="pharmacy-selector" style="font-weight: 800; font-size: 13px; color: #1E3A8A; display: flex; align-items: center; gap: 6px;">
                  🏪 Matched Pharmacy (Top 5 Nearest):
                </label>
                <span style="background: #DCFCE7; color: #15803D; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px;">Nearest Store</span>
              </div>
              
              @if(count($matches) > 1)
                <select id="pharmacy-selector" onchange="onPharmacyDropdownChange(this.value)" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #3B82F6; background: #fff; font-weight: 700; font-size: 13.5px; color: #1E293B; cursor: pointer; outline: none;">
                  @foreach($matches as $index => $m)
                    <option value="{{ $m['shop']->id }}" {{ (int)$m['shop']->id === (int)$bestMatch['shop']->id ? 'selected' : '' }}>
                      🏪 {{ $m['shop']->name }} ({{ $m['shop']->area }}) — {{ $m['shop']->distance_km }} km away {{ $index === 0 ? '⭐ [Nearest Store]' : '' }}
                    </option>
                  @endforeach
                </select>
              @else
                <div style="font-weight: 800; font-size: 14px; color: #1A1A1A;">🏪 {{ $bestMatch['shop']->name }}</div>
                <div style="font-size: 11px; color: #718096; margin-top: 2px;">📍 {{ $bestMatch['shop']->area }} • {{ $bestMatch['shop']->distance_km }} km away</div>
              @endif
            </div>

            <!-- Items List with Add/Edit/Delete Controls -->
            <div style="display: flex; flex-direction: column;">
              @foreach($cartItems as $item)
                @php
                  $availItem = collect($bestMatch['available'])->firstWhere('id', $item->id);
                  $qty = $cart[$item->id] ?? 1;
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #F3F4F6;" id="cart-item-row-{{ $item->id }}">
                  <div style="display: flex; gap: 10px; align-items: center; flex: 1;">
                    <span style="font-size: 22px;">{{ $item->emoji ?? '💊' }}</span>
                    <div>
                      <div style="font-weight: 700; font-size: 13.5px; color: #2D3748;">{{ $item->name }}</div>
                      <div style="font-size: 11px; color: #718096; margin-top: 2px;">
                        @if($availItem)
                          ₹{{ $availItem['shopPrice'] }} each
                        @else
                          <span style="color: #E53E3E; font-weight: 700;">❌ Unavailable</span>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Quantity Increment / Decrement -->
                    <div style="display: flex; align-items: center; border: 1.5px solid #CBD5E0; border-radius: 8px; overflow: hidden; background: #F8FAFC;">
                      <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty - 1 }})" style="background: #EDF2F7; border: none; padding: 4px 10px; font-weight: 900; font-size: 14px; cursor: pointer; color: #4A5568;" title="Decrease quantity">-</button>
                      <span style="padding: 4px 10px; font-weight: 800; font-size: 13px; color: #1A202C;" id="item-qty-val-{{ $item->id }}">{{ $qty }}</span>
                      <button type="button" onclick="updateItemQuantity({{ $item->id }}, {{ $qty + 1 }})" style="background: #EDF2F7; border: none; padding: 4px 10px; font-weight: 900; font-size: 14px; cursor: pointer; color: #4A5568;" title="Increase quantity">+</button>
                    </div>

                    <!-- Item Price Subtotal -->
                    <div style="min-width: 65px; text-align: right;">
                      @if($availItem)
                        <span style="font-weight: 800; font-size: 14px; color: #1E293B;">₹{{ $availItem['shopPrice'] * $qty }}</span>
                      @else
                        <span style="font-size: 11px; color: #E53E3E; font-weight: 700;">-</span>
                      @endif
                    </div>

                    <!-- Delete Button -->
                    <button type="button" onclick="updateItemQuantity({{ $item->id }}, 0)" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; padding: 5px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;" title="Delete item">
                      🗑️
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Add More Items Action Link -->
            <div style="margin-top: 14px; display: flex; justify-content: space-between; align-items: center;">
              <a href="{{ url('/search') }}" style="color: #2563EB; font-weight: 800; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 6px; background: #EFF6FF; padding: 8px 14px; border-radius: 8px; border: 1px dashed #93C5FD;">
                ➕ Add More Medicines
              </a>
            </div>

            <!-- Missing Items Warning -->
            @if(count($bestMatch['missing']) > 0)
              <div style="background: #FFFBEB; border-radius: 8px; padding: 10px 12px; border: 1px solid #FDE68A; margin-top: 14px;">
                <div style="font-weight: 800; font-size: 12px; color: #B45309; margin-bottom: 6px;">⚠️ {{ count($bestMatch['missing']) }} item(s) unavailable at this pharmacy</div>
                @foreach($bestMatch['missing'] as $miss)
                  <div style="font-size: 11px; color: #B45309;">• {{ $miss->name }}</div>
                @endforeach
              </div>
            @endif
          </div>
        </div>

        <!-- Right Side: Order Summary (Amazon Style Sidebar) -->
        <div style="flex: 1; display: flex; flex-direction: column; gap: 16px;">
          <div style="background: #F7FAFC; border: 1.5px solid #CBD5E0; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <h3 style="font-weight: 900; font-size: 16px; color: #1D2D44; margin-top: 0; margin-bottom: 14px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #4A5568;">
              <span>Items Total:</span>
              <span style="font-weight: 700; color: #2D3748;">₹{{ $bestMatch['totalPrice'] }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; color: #4A5568;" id="summary-delivery-row">
              <span>Delivery Charges:</span>
              <span style="font-weight: 700; color: #16A34A;" id="summary-delivery-text">FREE</span>
            </div>

            @if(($bestMatch['discount'] ?? 0) > 0)
              <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; color: #16A34A; font-weight: 800;" id="summary-discount-row">
                <span>Bill Discount ({{ $bestMatch['shop']->offer_discount_pct }}%):</span>
                <span>-₹{{ $bestMatch['discount'] }}</span>
              </div>
            @endif
            
            <div style="border-top: 1px solid #CBD5E0; padding-top: 12px; display: flex; justify-content: space-between; margin-bottom: 20px;">
              <span style="font-size: 16px; font-weight: 900; color: #1A202C;">Order Total:</span>
              <span style="font-size: 18px; font-weight: 900; color: #B7791F;" id="summary-total-text">₹{{ $bestMatch['totalPrice'] - ($bestMatch['discount'] ?? 0) }}</span>
            </div>

            <!-- Amazon Style Yellow Button -->
            <button type="submit" class="btn-green" style="background: linear-gradient(to bottom, #f7dfa5, #f0c14b); border: 1px solid #a88734; border-radius: 8px; color: #111; font-weight: 700; font-size: 14px; padding: 12px; width: 100%; cursor: pointer; box-shadow: 0 1px 0 rgba(255,255,255,.4) inset;">
              Place Your Order
            </button>
            <div style="font-size: 11px; text-align: center; color: #718096; margin-top: 10px;">
              By placing your order, you agree to our privacy notice and terms.
            </div>
          </div>
        </div>

      </div>
    </form>
  </div>
</div>

<script>
  const allMatchesData = {!! json_encode(array_values($matches)) !!};
  const priceMedicines = {{ $bestMatch['totalPrice'] }};
  const priceDelivery = {{ $bestMatch['deliveryCharge'] }};
  const discount = {{ $bestMatch['discount'] ?? 0 }};

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

  function onPharmacyDropdownChange(shopId) {
    // Redirect to checkout page with the selected shop
    window.location.href = "{{ url('/smartcart/results') }}?shop_id=" + shopId;
  }
  
  const labelPickup = document.getElementById('label-pickup');
  const labelDelivery = document.getElementById('label-delivery');
  const deliveryAddressSection = document.getElementById('delivery-address-section');
  const summaryDeliveryText = document.getElementById('summary-delivery-text');
  const summaryTotalText = document.getElementById('summary-total-text');
  
  // Fields validation elements
  const addrName = document.getElementById('addr-name');
  const addrLine1 = document.getElementById('addr-line1');
  const addrLine2 = document.getElementById('addr-line2');
  const addrPin = document.getElementById('addr-pin');

  let activePriceMedicines = priceMedicines;
  let activePriceDelivery = priceDelivery;
  let activeDiscount = discount;

  function updatePricing(mode) {
    if (mode === 'delivery') {
      if (labelDelivery) {
        labelDelivery.style.borderColor = '#1A3C8F';
        labelDelivery.style.background = '#EEF2FF';
      }
      if (labelPickup) {
        labelPickup.style.borderColor = '#E5E7EB';
        labelPickup.style.background = '#fff';
      }
      
      // Show Address
      deliveryAddressSection.style.display = 'block';
      addrName.required = true;
      addrLine1.required = true;
      addrLine2.required = true;
      addrPin.required = true;

      summaryDeliveryText.innerText = '₹' + activePriceDelivery;
      summaryDeliveryText.style.color = '#B7791F';
      summaryTotalText.innerText = '₹' + (activePriceMedicines - activeDiscount + activePriceDelivery);
    } else {
      if (labelPickup) {
        labelPickup.style.borderColor = '#1A3C8F';
        labelPickup.style.background = '#EEF2FF';
      }
      if (labelDelivery) {
        labelDelivery.style.borderColor = '#E5E7EB';
        labelDelivery.style.background = '#fff';
      }
      
      // Hide Address
      deliveryAddressSection.style.display = 'none';
      addrName.required = false;
      addrLine1.required = false;
      addrLine2.required = false;
      addrPin.required = false;

      summaryDeliveryText.innerText = 'FREE';
      summaryDeliveryText.style.color = '#16A34A';
      summaryTotalText.innerText = '₹' + (activePriceMedicines - activeDiscount);
    }
  }

  if (labelPickup) {
    labelPickup.addEventListener('click', () => {
      @if($isNotDefaultPickupShop)
        window.location.href = "{{ url('/smartcart/results') }}";
      @else
        updatePricing('pickup');
      @endif
    });
  }
  if (labelDelivery) {
    labelDelivery.addEventListener('click', () => updatePricing('delivery'));
  }

  // Set initial pricing state on page load according to preselected mode
  updatePricing('{{ $isDeliveryMode ? "delivery" : "pickup" }}');

  function showAlternativeDeliveryShops() {
    // Redirect to results without forcing a specific shop.
    // Controller will auto-pick best shop with most medicines available.
    window.location.href = "{{ url('/smartcart/results') }}?prefer_delivery=1";
  }

  function selectAlternativeDeliveryShop(shopData) {
    // Highlight selected row
    document.querySelectorAll('.alt-shop-row').forEach(row => {
      row.style.borderColor = '#E2E8F0';
      row.style.background = '#fff';
    });

    const selectedRow = document.getElementById('alt-shop-' + shopData.id);
    if (selectedRow) {
      selectedRow.style.borderColor = '#1E3A8A';
      selectedRow.style.background = '#EFF6FF';
    }

    // Update form shop ID value parameters
    document.querySelector('input[name="shop_id"]').value = shopData.id;

    // Update active matching pricing rates
    activePriceMedicines = parseFloat(shopData.total);
    activePriceDelivery = parseFloat(shopData.charge);
    activeDiscount = parseFloat(shopData.discount);

    // Update pharmacy display headers
    const reviewHeader = document.getElementById('matched-pharmacy-header');
    if (reviewHeader) {
      // Find parent review container
      const matchedBlock = reviewHeader.closest('div');
      if (matchedBlock) {
        matchedBlock.innerHTML = `
          <div>
            <div style="font-weight: 800; font-size: 14px; color: #1A1A1A;">🏪 ${shopData.name}</div>
            <div style="font-size: 11px; color: #718096; margin-top: 2px;">📍 ${shopData.area} • ${shopData.dist} km away</div>
          </div>
          <span style="background: #E0F2FE; color: #0369A1; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px;">Alternative Delivery</span>
        `;
      }
    }

    // Add and force update home delivery mode select radios
    const deliveryOptionsContainer = document.getElementById('label-delivery')?.parentNode || document.getElementById('label-pickup')?.parentNode;
    if (deliveryOptionsContainer) {
      deliveryOptionsContainer.innerHTML = `
        <!-- Pickup Option -->
        <label style="flex: 1; border: 1.5px solid #E5E7EB; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: #fff; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-pickup">
          <input type="radio" name="mode" value="pickup" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" required>
          <div style="font-size: 24px; margin-bottom: 4px;">🚶</div>
          <div style="font-weight: 800; font-size: 13px; color: #1A1A1A;">Self Pickup</div>
          <div style="margin-top: 6px; background: #DCFCE7; color: #16A34A; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;">FREE</div>
        </label>

        <!-- Delivery Option -->
        <label style="flex: 1; border: 2.5px solid #1A3C8F; border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: #EEF2FF; position: relative; display: block; transition: all 0.2s;" class="mode-label" id="label-delivery">
          <input type="radio" name="mode" value="delivery" style="position: absolute; top: 10px; right: 10px; accent-color: #1A3C8F;" checked>
          <div style="font-size: 24px; margin-bottom: 4px;">🛵</div>
          <div style="font-weight: 800; font-size: 13px; color: #1A1A1A;">Home Delivery</div>
          <div style="margin-top: 6px; background: #FEF3C7; color: #D97706; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 6px; display: inline-block;">+₹${shopData.charge}</div>
        </label>
      `;

      // Re-bind listeners
      document.getElementById('label-pickup').addEventListener('click', () => updatePricing('pickup'));
      document.getElementById('label-delivery').addEventListener('click', () => updatePricing('delivery'));
    }

    // Force update visual elements
    updatePricing('delivery');
  }
</script>
@endsection
