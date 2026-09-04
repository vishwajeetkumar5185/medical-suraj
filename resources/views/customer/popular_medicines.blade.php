@extends('layouts.app')

@section('seo_title', 'Popular Medicines - Dawalo')
@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
  .screen { overflow: visible !important; height: auto !important; min-height: 100vh !important; }
  
  /* Smooth transitions for cards */
  .medicine-card-grid {
    transition: all 0.2s ease;
  }
  
  .medicine-card-grid:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
  }
</style>

<div style="min-height:100vh; background:#F5F7FA; padding-bottom:80px;">
  
  <!-- Header -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px; position:sticky; top:0; z-index:100;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
      <a href="{{ url('/') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0; flex:1;">Popular Dawaiyan</h1>
      <a href="{{ url('/smartcart') }}" style="position:relative; text-decoration:none;">
        <span style="font-size:24px;">🛒</span>
        @if($cartCount > 0)
          <span style="position:absolute; top:-8px; right:-8px; background:#EF4444; color:#fff; font-size:11px; font-weight:700; padding:2px 6px; border-radius:10px; min-width:20px; text-align:center;">{{ $cartCount }}</span>
        @endif
      </a>
    </div>
  </div>

  <!-- Medicines Grid -->
  <div style="padding:16px;">
    
    @if($medicines->count() > 0)
      <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; margin-bottom:20px;">
        @foreach($medicines as $medicine)
        <div class="medicine-card-grid" style="background:#fff; border-radius:16px; padding:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); position:relative;">
          
          @if($loop->index < 3)
            <div style="position:absolute; top:10px; left:10px; background:#10B981; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; z-index:1;">HOT 🔥</div>
          @endif
          
          <a href="{{ url('/medicine/'.$medicine->id) }}" style="text-decoration:none; display:block;">
            <div style="width:100%; height:130px; background:#F8FAFC; border-radius:12px; margin-bottom:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:10px;">
              @if($medicine->images)
                @php
                  $images = is_array($medicine->images) ? $medicine->images : json_decode($medicine->images, true);
                  $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
                @endphp
                @if($firstImage)
                  <img src="{{ asset($firstImage) }}" style="max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain;" alt="{{ $medicine->name }}">
                @else
                  <span style="font-size:60px;">{{ $medicine->emoji ?? '💊' }}</span>
                @endif
              @else
                <span style="font-size:60px;">{{ $medicine->emoji ?? '💊' }}</span>
              @endif
            </div>
            
            <div style="font-size:14px; font-weight:800; color:#1A1A1A; margin-bottom:6px; line-height:1.3; min-height:38px;">{{ $medicine->name }}</div>
            
            <div style="font-size:11px; color:#64748B; font-weight:600; margin-bottom:8px;">{{ $medicine->category }}</div>
            
            <div style="display:flex; align-items:center; gap:6px; margin-bottom:12px;">
              @if($medicine->mrp && $medicine->price < $medicine->mrp)
                <span style="font-size:16px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
                <span style="font-size:12px; color:#94A3B8; text-decoration:line-through;">₹{{ number_format($medicine->mrp, 0) }}</span>
                @php
                  $discount = round((($medicine->mrp - $medicine->price) / $medicine->mrp) * 100);
                @endphp
                <span style="font-size:10px; color:#10B981; font-weight:700; background:#E8F5E9; padding:2px 6px; border-radius:4px;">{{ $discount }}% OFF</span>
              @else
                <span style="font-size:16px; font-weight:800; color:#1A1A1A;">₹{{ number_format($medicine->price, 0) }}</span>
              @endif
            </div>
          </a>
          
          <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0;">
            @csrf
            <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" style="width:100%; background:#3B82F6; color:#fff; border:none; border-radius:10px; padding:10px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 2px 6px rgba(59,130,246,0.3);">+ Add to Cart</button>
          </form>
        </div>
        @endforeach
      </div>

      <!-- Pagination -->
      <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:20px;">
        @if($medicines->onFirstPage())
          <span style="padding:10px 16px; background:#E5E7EB; color:#94A3B8; border-radius:8px; font-size:14px; font-weight:700;">← Previous</span>
        @else
          <a href="{{ $medicines->previousPageUrl() }}" style="padding:10px 16px; background:#3B82F6; color:#fff; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none; box-shadow:0 2px 6px rgba(59,130,246,0.3);">← Previous</a>
        @endif

        <span style="padding:10px 16px; background:#fff; color:#1A1A1A; border-radius:8px; font-size:14px; font-weight:700; box-shadow:0 2px 6px rgba(0,0,0,0.06);">Page {{ $medicines->currentPage() }}</span>

        @if($medicines->hasMorePages())
          <a href="{{ $medicines->nextPageUrl() }}" style="padding:10px 16px; background:#3B82F6; color:#fff; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none; box-shadow:0 2px 6px rgba(59,130,246,0.3);">Next →</a>
        @else
          <span style="padding:10px 16px; background:#E5E7EB; color:#94A3B8; border-radius:8px; font-size:14px; font-weight:700;">Next →</span>
        @endif
      </div>
    @else
      <div style="text-align:center; padding:60px 20px;">
        <div style="font-size:80px; margin-bottom:20px;">💊</div>
        <h3 style="font-size:18px; font-weight:800; color:#1A1A1A; margin-bottom:8px;">No Medicines Found</h3>
        <p style="font-size:14px; color:#64748B; margin-bottom:20px;">Try searching for something else</p>
        <a href="{{ url('/') }}" style="display:inline-block; padding:12px 24px; background:#3B82F6; color:#fff; text-decoration:none; border-radius:10px; font-weight:700; box-shadow:0 2px 8px rgba(59,130,246,0.3);">Go to Home</a>
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
      btn.textContent = 'Adding...';
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
          btn.textContent = '✓ Added';
          btn.style.background = '#10B981';
          setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '#3B82F6';
            btn.disabled = false;
          }, 1500);
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

@endsection
