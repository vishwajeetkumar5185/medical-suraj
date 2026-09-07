@extends('layouts.app')

@section('seo_title', $medicine->name . ' (' . $medicine->strength . ') - Price, Formula & Uses | Dawalo')
@section('seo_description', 'View complete details of ' . $medicine->name . ' including generic composition ' . $medicine->generic_name . ', manufacturer MRP, strength, and check local store availability.')
@section('seo_keywords', $medicine->name . ', ' . $medicine->generic_name . ', generic composition, medicine formula uses, dawalo medicine details')

@section('content')
<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
  
  /* Hide scrollbar for horizontal scrolls */
  div[style*="overflow-x:auto"]::-webkit-scrollbar {
    display: none;
  }
  
  div[style*="overflow-x:auto"] {
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  
  /* Add hover effect for medicine cards */
  .medicine-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
  }
  
  .medicine-card {
    transition: all 0.2s ease;
  }
  
  .medicine-card button:hover {
    background: #2563EB !important;
  }
</style>

<div style="background:#F5F7FA; min-height:100vh; display:block !important;">
  
  <!-- === HEADER === -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:12px 16px 20px; border-radius:0 0 20px 20px; position:relative;">
    
    <!-- Header Row -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ !empty(request('shop_id')) ? url('/search?shop_id='.request('shop_id')) : url()->previous() }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
          <span style="color:#fff; font-size:18px;">←</span>
        </a>
        <div>
          <div style="color:#fff; font-size:16px; font-weight:800;">💊 Medicine Details</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">{{ $medicine->category }}</div>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">💬</span>
        </div>
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">🔔</span>
        </div>
        <a href="{{ url('/profile') }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">👤</span>
        </a>
      </div>
    </div>
    <!-- Search Box - Redirect to Search -->
    <div style="margin-bottom:14px;">
      <div 
        onclick="window.location.href='{{ url('/search') }}'" 
        style="background:#fff; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); cursor:pointer; transition:all 0.2s ease;"
      >
        <span style="font-size:20px; color:#3B82F6;">🔍</span>
        <span style="flex:1; font-size:14px; color:#94A3B8; font-weight:500;">Medicine ya lab test search karein...</span>
        <span style="font-size:16px; color:#3B82F6;">›</span>
      </div>
    </div>

    <!-- Category Pills -->
    <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:2px;">
      <a href="{{ url('/search?q=Bukhar') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🤒</span> Bukhar
      </a>
      <a href="{{ url('/search?q=Diabetes') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🩸</span> Diabetes
      </a>
      <a href="{{ url('/search?q=Skin') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💧</span> Skin Care
      </a>
      <a href="{{ url('/search?q=Pain') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💊</span> Pain
      </a>
    </div>
  </div>

  <!-- === MAIN CONTENT === -->
  <div style="padding:16px; padding-bottom:100px;">
    
    <!-- Medicine Image Card -->
    <div style="padding:24px; margin-bottom:16px; display:flex; flex-direction:column; align-items:center; position:relative;">
      @if(!empty($medicine->images))
        <div style="position:relative; width:100%; display:flex; align-items:center;">
          <!-- Left Arrow -->
          @if(count($medicine->images) > 1)
            <button type="button" onclick="scrollCarousel(-1)" style="position:absolute; left:4px; z-index:10; background:rgba(255,255,255,0.9); border:1px solid #CBD5E1; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-weight:bold; box-shadow:0 2px 6px rgba(0,0,0,0.1); color:#0EA5E9; font-size:14px;">‹</button>
          @endif

          <div style="width:100%; height:280px; display:flex; overflow-x:auto; scroll-snap-type:x mandatory; gap:10px; border-radius:12px; scroll-behavior:smooth;" id="detail-carousel">
            @foreach($medicine->images as $img)
              @php
                $isAbsolute = strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0;
                $imgUrl = $isAbsolute ? $img : asset($img);
              @endphp
              <div style="width:100%; height:100%; flex-shrink:0; scroll-snap-align:start; border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <img src="{{ $imgUrl }}" referrerpolicy="no-referrer"
                  style="max-width:100%; max-height:100%; object-fit:contain; border-radius:12px;"
                  alt="{{ $medicine->name }}"
                  onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display:none; font-size:100px; align-items:center; justify-content:center; width:100%; height:100%;">{{ $medicine->emoji }}</div>
              </div>
            @endforeach
          </div>

          <!-- Right Arrow -->
          @if(count($medicine->images) > 1)
            <button type="button" onclick="scrollCarousel(1)" style="position:absolute; right:4px; z-index:10; background:rgba(255,255,255,0.9); border:1px solid #CBD5E1; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-weight:bold; box-shadow:0 2px 6px rgba(0,0,0,0.1); color:#0EA5E9; font-size:14px;">›</button>
          @endif
        </div>

        @if(count($medicine->images) > 1)
          <div style="display:flex; gap:6px; margin-top:12px;" id="carousel-dots">
            @foreach($medicine->images as $k => $img)
              <span style="width:8px; height:8px; border-radius:50%; background:#0EA5E9; opacity:{{ $k == 0 ? '1' : '0.3' }}; transition:opacity 0.2s;"></span>
            @endforeach
          </div>
        @endif
      @else
        <div style="font-size:120px; padding:40px 20px; margin-bottom:12px;">
          {{ $medicine->emoji }}
        </div>
      @endif
      
      @if($selectedShop)
        <div style="position:absolute; top:12px; left:12px; color:#0EA5E9; font-size:11px; font-weight:800; padding:6px 12px; border-radius:20px; background:rgba(255,255,255,0.9); backdrop-filter:blur(10px);">
          🏪 {{ $selectedShop->name }}
        </div>
      @endif
    </div>
    <!-- Medicine Information Card -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
        <span style="background:#F0F9FF; color:#0EA5E9; font-size:11px; font-weight:800; padding:6px 12px; border-radius:20px; text-transform:uppercase; border:1px solid #BFDBFE;">
          {{ $medicine->category }}
        </span>
        
        @if(strtolower($medicine->prescription_required) === 'yes')
          <span style="background:#FEF2F2; color:#DC2626; font-size:11px; font-weight:800; padding:6px 12px; border-radius:20px; border:1px solid #FCA5A5;">
            ⚠️ Rx Required
          </span>
        @endif
      </div>
      
      <h3 style="font-weight:800; font-size:20px; color:#1A1A1A; margin:0 0 8px 0; line-height:1.2;">
        {{ $medicine->name }}
      </h3>
      
      @if($medicine->composition)
        <p style="font-size:12px; color:#64748B; margin-bottom:4px;">
          <strong style="color:#374151;">Composition:</strong> {{ $medicine->composition }}
        </p>
      @endif

      @if($medicine->marketer)
        <p style="font-size:12px; color:#64748B; margin-bottom:8px;">
          <strong style="color:#374151;">Marketer:</strong> {{ $medicine->marketer }}
        </p>
      @endif

      @if($medicine->packaging_detail)
        <p style="font-size:12px; color:#64748B; margin-bottom:12px;">{{ $medicine->packaging_detail }}</p>
      @endif

      <div style="display:flex; align-items:baseline; gap:12px; margin-bottom:16px;">
        <span style="font-size:24px; font-weight:800; color:#0EA5E9;">₹{{ number_format($price, 2) }}</span>
        @if($medicine->mrp > $price)
          <span style="font-size:14px; color:#94A3B8; text-decoration:line-through;">₹{{ number_format($medicine->mrp, 2) }}</span>
          <span style="background:#DCFCE7; color:#166534; font-size:11px; font-weight:700; padding:4px 8px; border-radius:12px;">
            {{ round((($medicine->mrp - $price) / $medicine->mrp) * 100) }}% OFF
          </span>
        @endif
      </div>

      <!-- Add to Cart Controls -->
      @php
        $qty = $cart[$medicine->id] ?? 0;
      @endphp
      <div data-med-id="{{ $medicine->id }}">
        @if($qty == 0)
          <form action="{{ url('/cart/add') }}" method="POST" class="cart-form">
            @csrf
            <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
            <button type="submit" style="width:100%; padding:14px; background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(14,165,233,0.25); transition:all 0.2s;">
              ➕ ADD TO CART
            </button>
          </form>
        @else
          <div style="display:flex; align-items:center; border:2px solid #0EA5E9; border-radius:12px; overflow:hidden; background:#fff;">
            <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1;">
              @csrf
              <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
              <input type="hidden" name="qty" value="{{ $qty - 1 }}">
              <button type="submit" style="width:100%; background:#F0F9FF; color:#0EA5E9; border:none; padding:12px; font-weight:800; font-size:18px; cursor:pointer;">−</button>
            </form>
            
            <div style="padding:12px 20px; font-weight:800; font-size:16px; color:#0EA5E9; min-width:60px; text-align:center;">{{ $qty }}</div>
            
            <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1;">
              @csrf
              <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
              <input type="hidden" name="qty" value="{{ $qty + 1 }}">
              <button type="submit" style="width:100%; background:#F0F9FF; color:#0EA5E9; border:none; padding:12px; font-weight:800; font-size:18px; cursor:pointer;">+</button>
            </form>
          </div>
        @endif
      </div>
    </div>
    <!-- Related Medicines -->
    @if($relatedMedicines->count() > 0)
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0;">📋 Related Medicines</h3>
        <span style="font-size:12px; color:#64748B; font-weight:600;">{{ $relatedMedicines->count() }} items</span>
      </div>
      
      <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:4px;">
        @foreach($relatedMedicines as $rel)
          @php
            $relUrl = url('/medicine/'.$rel->id.(!empty(request('shop_id')) ? '?shop_id='.request('shop_id') : ''));
            $relQty = $cart[$rel->id] ?? 0;
          @endphp
          <div class="medicine-card" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:12px; width:140px; flex-shrink:0; display:flex; flex-direction:column; position:relative;">
            <a href="{{ $relUrl }}" style="text-decoration:none;">
              <div style="height:70px; display:flex; align-items:center; justify-content:center; font-size:28px; background:#fff; border-radius:8px; margin-bottom:8px; overflow:hidden;">
                @if(!empty($rel->images))
                  @php
                    $isRelAbsolute = strpos($rel->images[0], 'http://') === 0 || strpos($rel->images[0], 'https://') === 0;
                    $relImgUrl = $isRelAbsolute ? $rel->images[0] : asset($rel->images[0]);
                  @endphp
                  <img src="{{ $relImgUrl }}" referrerpolicy="no-referrer"
                    style="width:100%; height:100%; object-fit:contain;"
                    onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=font-size:28px>{{ $rel->emoji }}</span>';">
                @else
                  {{ $rel->emoji }}
                @endif
              </div>
              <div style="font-weight:700; font-size:12px; color:#1A1A1A; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; height:32px; line-height:1.3; margin-bottom:6px;">
                {{ $rel->name }}
              </div>
              <div style="font-weight:800; font-size:14px; color:#0EA5E9; margin-bottom:8px;">₹{{ number_format($rel->price, 2) }}</div>
            </a>
            
            @if($relQty == 0)
              <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin-top:auto;">
                @csrf
                <input type="hidden" name="medicine_id" value="{{ $rel->id }}">
                <button type="submit" style="width:100%; background:#0EA5E9; color:#fff; border:none; border-radius:8px; padding:8px 12px; font-size:11px; font-weight:700; cursor:pointer;">ADD</button>
              </form>
            @else
              <div style="display:flex; align-items:center; border:1px solid #0EA5E9; border-radius:8px; overflow:hidden; margin-top:auto;">
                <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1;">
                  @csrf
                  <input type="hidden" name="medicine_id" value="{{ $rel->id }}">
                  <input type="hidden" name="qty" value="{{ $relQty - 1 }}">
                  <button type="submit" style="width:100%; background:#fff; color:#0EA5E9; border:none; padding:6px; font-weight:700; font-size:12px;">−</button>
                </form>
                <span style="padding:6px 8px; font-weight:700; font-size:12px; color:#0EA5E9; text-align:center;">{{ $relQty }}</span>
                <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1;">
                  @csrf
                  <input type="hidden" name="medicine_id" value="{{ $rel->id }}">
                  <input type="hidden" name="qty" value="{{ $relQty + 1 }}">
                  <button type="submit" style="width:100%; background:#fff; color:#0EA5E9; border:none; padding:6px; font-weight:700; font-size:12px;">+</button>
                </form>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>
  <!-- Cart Floating Bar -->
  @if($cartCount > 0)
    <div style="position:fixed; bottom:80px; left:50%; transform:translateX(-50%); width:100%; max-width:600px; padding:0 16px; z-index:999;">
      <div style="background:linear-gradient(135deg, #0EA5E9, #0284C7); border-radius:12px; padding:14px 16px; box-shadow:0 4px 20px rgba(14,165,233,0.3); display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="background:rgba(255,255,255,0.2); border-radius:8px; width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:#fff;">
            {{ $cartCount }}
          </div>
          <div>
            <div style="color:#fff; font-weight:800; font-size:13px;">Cart में {{ $cartCount }} item{{ $cartCount > 1 ? 's' : '' }}</div>
            <div style="color:rgba(255,255,255,0.7); font-size:11px;">Checkout karne ke liye ready</div>
          </div>
        </div>
        <a href="{{ url('/smartcart/results') }}" style="background:#fff; color:#0EA5E9; border:none; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:800; text-decoration:none;">
          Checkout →
        </a>
      </div>
    </div>
  @endif

  <!-- Bottom Navigation -->
  <div style="position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:600px; background:#fff; border-top:1px solid #E5E7EB; padding:8px 20px 12px; display:flex; justify-content:space-around; align-items:center; z-index:1000;">
    <a href="{{ url('/') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🏠</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Home</span>
    </a>
    <a href="{{ url('/smartcart') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none; position:relative;">
      <div style="width:48px; height:48px; {{ $cartCount > 0 ? 'background:#0EA5E9; border-radius:50%;' : '' }} display:flex; align-items:center; justify-content:center; margin-bottom:4px; {{ $cartCount > 0 ? 'box-shadow:0 2px 8px rgba(14,165,233,0.3);' : '' }}">
        <span style="font-size:22px;">🛒</span>
      </div>
      @if($cartCount > 0)
        <span style="position:absolute; top:-4px; right:4px; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px; min-width:18px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);">{{ $cartCount }}</span>
      @endif
      <span style="font-size:11px; font-weight:700; color:{{ $cartCount > 0 ? '#0EA5E9' : '#64748B' }};">Cart</span>
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
  // Carousel functionality
  const carousel = document.getElementById('detail-carousel');
  const dotsContainer = document.getElementById('carousel-dots');
  
  if (carousel && dotsContainer) {
    const dots = dotsContainer.querySelectorAll('span');
    carousel.addEventListener('scroll', () => {
      const index = Math.round(carousel.scrollLeft / carousel.offsetWidth);
      dots.forEach((dot, idx) => {
        dot.style.opacity = idx === index ? '1' : '0.3';
      });
    });
  }

  function scrollCarousel(direction) {
    const carousel = document.getElementById('detail-carousel');
    if (carousel) {
      const offset = carousel.offsetWidth;
      carousel.scrollLeft += direction * offset;
    }
  }

  // AJAX cart functionality
  document.querySelectorAll('.cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const btn = this.querySelector('button');
      const originalText = btn.textContent;
      const medicineId = this.querySelector('input[name="medicine_id"]').value;
      
      btn.textContent = btn.textContent === 'ADD' ? 'Adding...' : '...';
      btn.disabled = true;

      fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message || 'Failed to update cart');
          btn.textContent = originalText;
          btn.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        btn.textContent = originalText;
        btn.disabled = false;
        // Fallback to regular form submission
        this.submit();
      });
    });
  });
</script>
@endsection