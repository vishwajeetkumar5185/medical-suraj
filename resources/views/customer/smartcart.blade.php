@extends('layouts.app')

@section('seo_title', 'Smart Cart - Auto-Match Medicine Lists | Dawalo')
@section('seo_description', 'Use Dawalo Smart Cart to upload or type medicine lists. Our advanced algorithms automatically match requirements with nearby pharmacy inventories in one go.')
@section('seo_keywords', 'smart cart, medicine matcher, upload prescription, bulk medicine order, online pharmacy aggregator')

@section('content')
<div class="screen">
  <!-- Header -->
  <div class="hdr-gradient" id="cart-header-gradient" style="padding-bottom: 24px; margin-bottom: 20px; transition: all 0.3s ease-in-out;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div id="cart-header-title-block" style="display:flex; align-items:center; gap:12px; margin-bottom:14px; position:relative; z-index:1; transition: all 0.3s ease-in-out; max-height: 100px; opacity: 1; overflow: hidden;">
      <a href="{{ url('/') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <div style="flex:1;">
        <h2 style="color:#fff; font-weight:900; font-size:20px; margin:0;">🛒 Smart Cart</h2>
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Best pharmacy auto-match hogi</p>
      </div>
      <div id="header-cart-badge" style="background:#fff; border-radius:14px; padding:8px 14px; display:{{ $cartCount > 0 ? 'flex' : 'none' }}; align-items:center; gap:6px;">
        <span>🛒</span>
        <strong style="font-weight:900; font-size:14px; color:#1A3C8F;">{{ $cartCount }}</strong>
      </div>
    </div>

    <!-- Search in Smart Cart -->
    <form action="{{ url('/smartcart') }}" method="GET" class="search-box" id="cart-search-form" style="position:relative; z-index:99; margin-bottom:0;" onsubmit="event.preventDefault(); triggerCartSearch();">
      <input name="q" id="cart-search-input" class="search-input" placeholder="Medicine ya category likhein..." type="text" autocomplete="off" oninput="debouncedCartSearchSuggestions(this.value)">
      <button type="submit" class="search-btn">Filter</button>
      
      <!-- Autocomplete Dropdown suggestions list -->
      <div id="cart-search-autocomplete" style="display:none; position:absolute; left:0; right:0; top:100%; background:#fff; border-radius:14px; margin-top:8px; box-shadow:0 10px 25px rgba(0,0,0,0.15); border:1px solid #E5E7EB; max-height:260px; overflow-y:auto; z-index:99999;"></div>
    </form>
  </div>

  <!-- Session Alerts Banners -->
  @if(session('success'))
    <div style="background:#DCFCE7; color:#16A34A; padding:12px 16px; border-radius:12px; font-weight:800; font-size:13px; margin: 0 16px 16px; border:1px solid #BBF7D0;">
      ✓ {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div style="background:#FEE2E2; color:#DC2626; padding:12px 16px; border-radius:12px; font-weight:800; font-size:13px; margin: 0 16px 16px; border:1px solid #FCA5A5;">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  <!-- Medicine Catalog Selection -->
  <div class="scroll" id="cart-scroll-container" style="flex:1;">
    <div class="responsive-grid">
      @include('customer.smartcart_items_inner')
    </div>
  </div>

  <!-- Checkout Button Container -->
  <div id="smartcart-checkout-bar" style="background:#fff; border-top:1px solid #E5E7EB; padding:12px 16px 20px; flex-shrink:0; border-radius:14px; margin-top:16px; {{ $cartCount > 0 ? 'display:block;' : 'display:none;' }}">
    <button type="button" onclick="openDirectCheckoutModal()" class="btn-blue" style="width:100%; padding:15px; background:linear-gradient(135deg,#1A3C8F,#2563EB); border:none; border-radius:14px; color:#fff; font-weight:900; font-size:15px; display:block; text-align:center; cursor:pointer;">
      🚀 Proceed to Checkout (<span id="checkout-item-count">{{ $cartCount }}</span> items) →
    </button>
  </div>

  <!-- Floating Action Button (FAB) for quick checkout -->
  <button type="button" onclick="openDirectCheckoutModal()" id="smartcart-fab" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; border-radius: 50%; width: 56px; height: 56px; display: {{ $cartCount > 0 ? 'flex' : 'none' }}; align-items: center; justify-content: center; background: linear-gradient(135deg,#1A3C8F,#2563EB); color: #fff; font-size: 22px; box-shadow: 0 8px 24px rgba(37,99,235,0.4); border: none; cursor:pointer; transition: transform 0.2s;">
    🛒
  </button>
</div>

@php
  $globalDeliveryFee = \App\Models\Setting::getVal('delivery_charge', '20');
  $globalMinOrder = \App\Models\Setting::getVal('min_delivery_order', '150');
@endphp

<!-- Direct Checkout Modal -->
<div id="direct-checkout-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
  <div style="background:#fff; border-radius:24px; max-width:480px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2); position:relative; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-weight:900; font-size:18px; color:#1E3A8A; margin:0;">🚚 Direct Order Checkout</h3>
      <button type="button" onclick="closeDirectCheckoutModal()" style="background:none; border:none; font-size:22px; color:#888; cursor:pointer; padding:0;">✕</button>
    </div>

    <form action="{{ url('/checkout') }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">Delivery Mode</label>
        <select name="mode" id="checkout-mode-select" class="form-input" style="padding:10px; font-size:13px; font-weight:700; border-radius:10px;" onchange="toggleAddressFields()">
          <option value="delivery" selected>🛵 Home Delivery (₹{{ $globalDeliveryFee }} Fee | Min Bill ₹{{ $globalMinOrder }})</option>
          <option value="pickup">🏪 Self Pickup (No Minimum Bill)</option>
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">Your Full Name *</label>
        <input type="text" name="address_name" value="{{ Auth::user() ? Auth::user()->name : '' }}" required class="form-input" placeholder="e.g. Rahul Sharma" style="padding:10px; font-size:13px; border-radius:10px;">
      </div>

      <div id="delivery-address-block">
        <div style="margin-bottom:12px;">
          <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">House No., Street & Colony *</label>
          <input type="text" name="address_line1" class="form-input" placeholder="e.g. H.No 45, Kurhani Main Road" style="padding:10px; font-size:13px; border-radius:10px;">
        </div>

        <div style="margin-bottom:12px;">
          <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">Landmark / Area</label>
          <input type="text" name="address_line2" class="form-input" placeholder="e.g. Near Shiv Mandir" style="padding:10px; font-size:13px; border-radius:10px;">
        </div>

        <div style="display:flex; gap:10px; margin-bottom:14px;">
          <div style="flex:1;">
            <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">City</label>
            <input type="text" name="address_city" value="Muzaffarpur" class="form-input" style="padding:10px; font-size:13px; border-radius:10px;">
          </div>
          <div style="flex:1;">
            <label class="form-label" style="font-weight:800; font-size:12px; color:#374151;">Pincode</label>
            <input type="text" name="address_pincode" value="842001" class="form-input" style="padding:10px; font-size:13px; border-radius:10px;">
          </div>
        </div>
      </div>

      <!-- Promo Coupon Code Input Box -->
      <div style="background:#FFFBEB; border:1px dashed #F59E0B; border-radius:12px; padding:12px; margin-bottom:14px;">
        <label class="form-label" style="font-weight:800; font-size:12px; color:#B45309; margin-bottom:4px; display:block;">🎟️ Have a Coupon or Offer Code?</label>
        <div style="display:flex; gap:6px;">
          <input type="text" id="checkout-coupon-code" placeholder="e.g. WELCOME50" style="flex:1; padding:8px 10px; font-size:12px; font-weight:800; text-transform:uppercase; border:1px solid #FCD34D; border-radius:8px; outline:none;">
          <button type="button" onclick="applyCheckoutCoupon()" style="background:#D97706; color:#fff; border:none; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:800; cursor:pointer;">
            Apply
          </button>
        </div>
        <div id="coupon-feedback-msg" style="display:none; font-size:11.5px; font-weight:700; margin-top:6px;"></div>
      </div>

      <div style="background:#F0FDF4; border:1px solid #86EFAC; border-radius:12px; padding:12px; margin-bottom:16px; font-size:12px; color:#166534; font-weight:700;">
        ✨ Minimum bill amount ₹{{ $globalMinOrder }} required for Home Delivery.
      </div>

      <button type="submit" class="btn-blue" style="width:100%; padding:14px; background:linear-gradient(135deg,#059669,#10B981); border:none; border-radius:12px; color:#fff; font-weight:900; font-size:15px; cursor:pointer;">
        ✅ Confirm & Place Order
      </button>
    </form>
  </div>
</div>

<script>
  function openDirectCheckoutModal() {
    const modal = document.getElementById('direct-checkout-modal');
    if (modal) modal.style.display = 'flex';
  }

  function closeDirectCheckoutModal() {
    const modal = document.getElementById('direct-checkout-modal');
    if (modal) modal.style.display = 'none';
  }

  function toggleAddressFields() {
    const mode = document.getElementById('checkout-mode-select').value;
    const block = document.getElementById('delivery-address-block');
    if (block) {
      block.style.display = mode === 'delivery' ? 'block' : 'none';
    }
  }

  function applyCheckoutCoupon() {
    const code = document.getElementById('checkout-coupon-code').value.trim();
    const msgEl = document.getElementById('coupon-feedback-msg');

    if (!code) {
      msgEl.style.display = 'block';
      msgEl.style.color = '#DC2626';
      msgEl.innerText = '⚠️ Please enter a coupon code!';
      return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('code', code);

    fetch('{{ url("/cart/apply-coupon") }}', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      msgEl.style.display = 'block';
      if (data.success) {
        msgEl.style.color = '#15803D';
        msgEl.innerText = '🎉 ' + data.message;
      } else {
        msgEl.style.color = '#DC2626';
        msgEl.innerText = '❌ ' + data.message;
      }
    })
    .catch(err => {
      console.error(err);
      msgEl.style.display = 'block';
      msgEl.style.color = '#DC2626';
      msgEl.innerText = '❌ Failed to apply coupon!';
    });
  }
</script>

<script>
  function attachCartSubmitHandlers(container) {
    container.querySelectorAll('.cart-form').forEach(form => {
      if (form.dataset.hasListener) return;
      form.dataset.hasListener = "true";

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const url = this.getAttribute('action');
        const formData = new FormData(this);

        fetch(url, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const medControls = form.closest('.cart-controls');
            const cardEl = form.closest('.cart-item-card');
            
            const addForm = medControls.querySelector('.add-form-el');
            const qtyControl = medControls.querySelector('.qty-control-el');
            const qtyDisplay = medControls.querySelector('.qty-display');
            const qtyInputDec = medControls.querySelector('.qty-input-dec');
            const qtyInputInc = medControls.querySelector('.qty-input-inc');

            if (data.qty === 0) {
              addForm.style.display = 'block';
              qtyControl.style.display = 'none';
              if (cardEl) cardEl.style.borderColor = 'transparent';
            } else {
              addForm.style.display = 'none';
              qtyControl.style.display = 'flex';
              qtyDisplay.innerText = data.qty;
              qtyInputDec.value = data.qty - 1;
              qtyInputInc.value = data.qty + 1;
              if (cardEl) cardEl.style.borderColor = '#BFDBFE';
            }

            // Update checkout button count
            const checkoutBar = document.getElementById('smartcart-checkout-bar');
            const checkoutCountSpan = document.getElementById('checkout-item-count');
            const checkoutFab = document.getElementById('smartcart-fab');
            
            if (data.cartCount > 0) {
              if (checkoutBar) checkoutBar.style.display = 'block';
              if (checkoutCountSpan) checkoutCountSpan.innerText = data.cartCount;
              if (checkoutFab) checkoutFab.style.display = 'flex';
            } else {
              if (checkoutBar) checkoutBar.style.display = 'none';
              if (checkoutFab) checkoutFab.style.display = 'none';
            }

            // Update top header count badge
            let headerBadge = document.getElementById('header-cart-badge');
            if (headerBadge) {
              if (data.cartCount > 0) {
                headerBadge.style.display = 'flex';
                headerBadge.querySelector('strong').innerText = data.cartCount;
              } else {
                headerBadge.style.display = 'none';
              }
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          this.submit();
        });
      });
    });
  }

  window.addEventListener('DOMContentLoaded', () => {
    attachCartSubmitHandlers(document);
  });

  // Autocomplete Suggestions
  let autocompleteTimeout;
  function debouncedCartSearchSuggestions(query) {
    clearTimeout(autocompleteTimeout);
    const dropdown = document.getElementById('cart-search-autocomplete');
    const q = query.trim().toLowerCase();

    if (q.length === 0) {
      dropdown.style.display = 'none';
      restoreCartHeader();
      triggerServerSearch('');
      return;
    }

    // Also trigger instant results search dynamically as they type
    clearTimeout(searchRequestTimeout);
    searchRequestTimeout = setTimeout(() => {
      triggerServerSearch(q);
    }, 100);

    autocompleteTimeout = setTimeout(() => {
      fetch(`{{ url('/medicines/search') }}?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          dropdown.innerHTML = '';
          if (data.length === 0) {
            dropdown.style.display = 'none';
            return;
          }
          dropdown.style.display = 'block';
          data.forEach(item => {
            const row = document.createElement('div');
            row.style.padding = '12px 16px';
            row.style.cursor = 'pointer';
            row.style.borderBottom = '1px solid #F3F4F6';
            row.style.fontSize = '13px';
            row.style.fontWeight = '700';
            row.style.color = '#1A1A1A';
            row.style.display = 'flex';
            row.style.alignItems = 'center';
            row.style.gap = '8px';
            row.innerHTML = `<span style="font-size:18px;">${item.emoji || '💊'}</span> <span>${item.name}</span> <span style="font-size:11px; color:#888; margin-left:auto; font-weight:normal;">in ${item.category}</span>`;
            row.addEventListener('click', () => {
              document.getElementById('cart-search-input').value = item.name;
              dropdown.style.display = 'none';
              triggerCartSearch();
            });
            dropdown.appendChild(row);
          });
        })
        .catch(err => console.error(err));
    }, 50);
  }

  let currentPage = 1;
  let isPageLoading = false;
  let hasMorePages = true;
  let activeSearchQuery = '';

  const scrollContainer = document.getElementById('cart-scroll-container');
  const itemsGrid = scrollContainer.querySelector('.responsive-grid');

  function handleScroll() {
    if (isPageLoading || !hasMorePages) return;
    
    const threshold = 250;
    const windowScrollPosition = document.documentElement.offsetHeight - window.innerHeight - window.scrollY;
    const containerScrollPosition = scrollContainer.scrollHeight - scrollContainer.scrollTop - scrollContainer.clientHeight;
    
    if (windowScrollPosition < threshold || containerScrollPosition < threshold) {
      loadNextBundle();
    }
  }

  window.addEventListener('scroll', handleScroll);
  scrollContainer.addEventListener('scroll', handleScroll);

  function loadNextBundle() {
    isPageLoading = true;
    currentPage++;
    
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'infinite-scroll-loading';
    loadingIndicator.style.width = '100%';
    loadingIndicator.style.textAlign = 'center';
    loadingIndicator.style.padding = '15px';
    loadingIndicator.style.fontSize = '12px';
    loadingIndicator.style.color = '#888';
    loadingIndicator.innerText = '📦 Loading more medicines...';
    itemsGrid.appendChild(loadingIndicator);

    const url = `{{ url('/smartcart') }}?page=${currentPage}&q=${encodeURIComponent(activeSearchQuery)}&_ajax=1&_t=` + Date.now();
    
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.json())
      .then(data => {
        const oldLoader = document.getElementById('infinite-scroll-loading');
        if (oldLoader) oldLoader.remove();
        
        if (data.html && data.html.trim().length > 0) {
          const temp = document.createElement('div');
          temp.innerHTML = data.html;
          while (temp.firstChild) {
            itemsGrid.appendChild(temp.firstChild);
          }
          attachCartSubmitHandlers(itemsGrid);
        }
        
        hasMorePages = data.hasMore;
        isPageLoading = false;
      })
      .catch(err => {
        console.error(err);
        const oldLoader = document.getElementById('infinite-scroll-loading');
        if (oldLoader) oldLoader.remove();
        isPageLoading = false;
      });
  }

  let searchRequestTimeout;
  function triggerServerSearch(q) {
    clearTimeout(searchRequestTimeout);
    searchRequestTimeout = setTimeout(() => {
      activeSearchQuery = q;
      currentPage = 1;
      hasMorePages = true;
      isPageLoading = true;
      
      const url = `{{ url('/smartcart') }}?page=1&q=${encodeURIComponent(q)}&_ajax=1&_t=` + Date.now();
      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          itemsGrid.innerHTML = data.html || '';
          attachCartSubmitHandlers(itemsGrid);
          hasMorePages = data.hasMore;
          isPageLoading = false;
        })
        .catch(err => {
          console.error(err);
          isPageLoading = false;
        });
    }, 200);
  }

  function shrinkCartHeader() {
    const titleBlock = document.getElementById('cart-header-title-block');
    const headerGradient = document.getElementById('cart-header-gradient');

    if (titleBlock) {
      titleBlock.style.maxHeight = '0';
      titleBlock.style.opacity = '0';
      titleBlock.style.marginBottom = '0';
    }
    if (headerGradient) {
      headerGradient.style.paddingBottom = '12px';
      headerGradient.style.marginBottom = '12px';
    }
  }

  function restoreCartHeader() {
    const titleBlock = document.getElementById('cart-header-title-block');
    const headerGradient = document.getElementById('cart-header-gradient');

    if (titleBlock) {
      titleBlock.style.maxHeight = '100px';
      titleBlock.style.opacity = '1';
      titleBlock.style.marginBottom = '14px';
    }
    if (headerGradient) {
      headerGradient.style.paddingBottom = '24px';
      headerGradient.style.marginBottom = '20px';
    }
  }

  function triggerCartSearch() {
    const q = document.getElementById('cart-search-input').value.trim();
    shrinkCartHeader();
    triggerServerSearch(q);
  }

  // Close suggestions list on click outside
  document.addEventListener('click', function(e) {
    if (e.target.id !== 'cart-search-input') {
      const results = document.getElementById('cart-search-autocomplete');
      if (results) results.style.display = 'none';
    }
  });
</script>
@endsection
