@extends('layouts.app')

@section('seo_title', 'Search Medicines - Dawalo')
@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <!-- Header with Search -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px; position:sticky; top:0; z-index:100;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
      <a href="{{ url('/') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0; flex:1;">Search Results</h1>
      <a href="{{ url('/smartcart') }}" style="position:relative; text-decoration:none;">
        <span style="font-size:24px;">🛒</span>
        @if($cartCount > 0)
          <span style="position:absolute; top:-8px; right:-8px; background:#EF4444; color:#fff; font-size:11px; font-weight:700; padding:2px 6px; border-radius:10px; min-width:20px; text-align:center;">{{ $cartCount }}</span>
        @endif
      </a>
    </div>

    <!-- Search Form -->
    <form action="{{ url('/search') }}" method="GET" style="margin-bottom:0;">
      <div style="background:#fff; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        <span style="font-size:20px; color:#94A3B8;">🔍</span>
        <input 
          type="text" 
          name="q"
          placeholder="Medicine ya lab test search karein..." 
          value="{{ $query }}"
          style="flex:1; border:none; outline:none; font-size:14px; color:#1A1A1A; font-weight:500; background:transparent;"
          autocomplete="off"
        >
        <button type="submit" style="background:none; border:none; padding:0; cursor:pointer; font-size:14px; color:#0EA5E9; font-weight:700;">Search</button>
      </div>
    </form>
  </div>

  <!-- Medicine Results -->
  <div style="padding:16px;">
    
    @if($medicines->count() > 0)
      <div style="margin-bottom:16px;">
        <h3 style="font-size:16px; font-weight:700; color:#1f2937; margin:0;">Medicines</h3>
      </div>

      <div style="display:flex; flex-direction:column; gap:12px;">
        @foreach($medicines as $med)
        @php
          $qty = $cart[$med->id] ?? 0;
        @endphp
        <div style="background:#fff; border-radius:12px; padding:12px; box-shadow:0 2px 6px rgba(0,0,0,0.06); display:flex; align-items:center; gap:12px;">
          <!-- Medicine Image -->
          <a href="{{ url('/medicine/'.$med->id) }}" style="flex-shrink:0; text-decoration:none;">
            <div style="width:60px; height:60px; background:#f8fafc; border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
              @if(!empty($med->images))
                @php
                  $images = is_array($med->images) ? $med->images : json_decode($med->images, true);
                  $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
                  $firstImgUrl = $firstImage ? ((strpos($firstImage, 'http://') === 0 || strpos($firstImage, 'https://') === 0) ? $firstImage : asset($firstImage)) : null;
                @endphp
                @if($firstImgUrl)
                  <img src="{{ $firstImgUrl }}" style="width:100%; height:100%; object-fit:contain;" alt="{{ $med->name }}">
                @else
                  <span style="font-size:28px;">{{ $med->emoji ?? '💊' }}</span>
                @endif
              @else
                <span style="font-size:28px;">{{ $med->emoji ?? '💊' }}</span>
              @endif
            </div>
          </a>

          <!-- Medicine Info -->
          <div style="flex:1; min-width:0;">
            <a href="{{ url('/medicine/'.$med->id) }}" style="text-decoration:none;">
              <div style="font-size:14px; font-weight:700; color:#1f2937; margin-bottom:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $med->name }}</div>
              <div style="font-size:12px; color:#0284c7; font-weight:600; margin-bottom:2px;">Strip of tablets</div>
              <div style="font-size:11px; color:#6b7280;">{{ $med->category }}</div>
            </a>
          </div>

          <!-- Price & Action -->
          <div style="flex-shrink:0; text-align:right;">
            <div style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:6px;">₹{{ number_format($med->price, 2) }}</div>
            
            @if($qty == 0)
              <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0;">
                @csrf
                <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" style="background:#0ea5e9; color:#fff; border:none; border-radius:6px; padding:6px 16px; font-size:12px; font-weight:700; cursor:pointer;">ADD</button>
              </form>
            @else
              <div style="display:flex; align-items:center; gap:8px; border:1.5px solid #0ea5e9; border-radius:8px; overflow:hidden;">
                <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="margin:0;">
                  @csrf
                  <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                  <input type="hidden" name="qty" value="{{ $qty - 1 }}">
                  <button type="submit" style="background:#fff; color:#0ea5e9; border:none; padding:4px 8px; font-size:14px; font-weight:700; cursor:pointer;">−</button>
                </form>
                <span style="font-size:13px; font-weight:700; color:#0ea5e9; min-width:20px; text-align:center;">{{ $qty }}</span>
                <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="margin:0;">
                  @csrf
                  <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                  <input type="hidden" name="qty" value="{{ $qty + 1 }}">
                  <button type="submit" style="background:#fff; color:#0ea5e9; border:none; padding:4px 8px; font-size:14px; font-weight:700; cursor:pointer;">+</button>
                </form>
              </div>
            @endif
          </div>
        </div>
        @endforeach
      </div>

      <!-- Pagination -->
      @if($medicines->hasMorePages())
        <div style="margin-top:20px; text-align:center;">
          <a href="{{ $medicines->nextPageUrl() }}" style="display:inline-block; padding:12px 24px; background:#0ea5e9; color:#fff; text-decoration:none; border-radius:10px; font-weight:700;">Load More</a>
        </div>
      @endif

    @else
      <div style="text-align:center; padding:60px 20px;">
        <div style="font-size:80px; margin-bottom:20px;">😔</div>
        <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:8px;">No Medicines Found</h3>
        <p style="font-size:14px; color:#64748B; margin-bottom:20px;">Try searching for something else</p>
        <a href="{{ url('/') }}" style="display:inline-block; padding:12px 24px; background:#3B82F6; color:#fff; text-decoration:none; border-radius:10px; font-weight:700;">Go to Home</a>
      </div>
    @endif

  </div>

</div>

<script>
  // Handle cart form submissions
  document.querySelectorAll('.cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = this.querySelector('button');
      const originalText = btn.textContent;
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
          alert(data.message || 'Failed to add item');
          btn.textContent = originalText;
          btn.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        btn.textContent = originalText;
        btn.disabled = false;
      });
    });
  });
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
