@extends('layouts.app')

@section('content')
<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="background:#F5F7FA; min-height:100vh; display:block !important;">
  
  <!-- === HEADER === -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:12px 16px 20px; border-radius:0 0 20px 20px; position:relative;">
    
    <!-- Header Row -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ url('/profile') }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
          <span style="color:#fff; font-size:18px;">←</span>
        </a>
        <div>
          <div style="color:#fff; font-size:16px; font-weight:800;">❤️ Favourites</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">Your preferred shops</div>
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

    <!-- Search Box -->
    <div style="margin-bottom:14px;">
      <div onclick="window.location.href='{{ url('/search') }}'" style="background:#fff; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); cursor:pointer; transition:all 0.2s ease;">
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
    
    @if($shops->isEmpty())
      <!-- Empty State -->
      <div style="background:#fff; border-radius:16px; padding:40px 20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-align:center;">
        <div style="font-size:80px; margin-bottom:16px;">❤️</div>
        <h3 style="font-weight:800; font-size:18px; color:#1A1A1A; margin:0 0 8px 0;">No Favourite Shops Yet</h3>
        <p style="font-size:14px; color:#64748B; margin:0 0 20px 0; line-height:1.5;">
          Jab aap kisi pharmacy se order karenge aur unhe pasand karenge, tab wo yahan favourite list mein add ho jayenge.
        </p>
        <a href="{{ url('/') }}" style="background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; padding:12px 24px; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none; display:inline-block;">
          🏪 Explore Pharmacies
        </a>
      </div>
    @else
      <!-- Favourites List -->
      <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
          <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0;">❤️ Favourite Pharmacies</h3>
          <span style="background:#FEF2F2; color:#DC2626; font-size:11px; font-weight:800; padding:4px 8px; border-radius:12px; border:1px solid #FCA5A5;">{{ $shops->count() }} Shops</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($shops as $shop)
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:16px; position:relative;">
              <!-- Shop Header -->
              <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="width:50px; height:50px; background:linear-gradient(135deg, #EF4444, #DC2626); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <span style="font-size:24px; color:#fff;">🏪</span>
                </div>
                <div style="flex:1;">
                  <div style="font-weight:800; font-size:15px; color:#1A1A1A; margin-bottom:2px;">{{ $shop->name }}</div>
                  <div style="font-size:12px; color:#64748B; display:flex; align-items:center; gap:8px;">
                    <span>📍 {{ $shop->area }}</span>
                    <span>⭐ {{ number_format($shop->rating, 1) }}</span>
                    <span style="background:{{ $shop->is_online ? '#DCFCE7' : '#FEE2E2' }}; color:{{ $shop->is_online ? '#166534' : '#DC2626' }}; font-size:10px; font-weight:700; padding:2px 6px; border-radius:6px;">
                      {{ $shop->is_online ? 'OPEN' : 'CLOSED' }}
                    </span>
                  </div>
                </div>
                <button onclick="alert('Remove from favourites coming soon!')" style="width:32px; height:32px; background:#FEE2E2; color:#EF4444; border:1px solid #FCA5A5; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px;">
                  💔
                </button>
              </div>

              <!-- Shop Info -->
              <div style="background:#fff; border-radius:8px; padding:12px; margin-bottom:12px;">
                <div style="font-size:12px; color:#64748B; line-height:1.4;">
                  <div style="margin-bottom:4px;"><strong>Address:</strong> {{ $shop->address ?? $shop->area }}</div>
                  <div><strong>Phone:</strong> {{ $shop->phone ?? 'Not provided' }}</div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div style="display:flex; gap:8px;">
                <a href="{{ url('/search?shop_id='.$shop->id) }}" style="flex:1; background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:700; text-decoration:none; text-align:center;">
                  🛒 Order Now
                </a>
                <button onclick="alert('Call pharmacy coming soon!')" style="flex:1; background:#F0F9FF; color:#0EA5E9; border:1px solid #BFDBFE; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:700; cursor:pointer;">
                  📞 Call
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

  </div>

  <!-- Bottom Navigation -->
  <div style="position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:600px; background:#fff; border-top:1px solid #E5E7EB; padding:8px 20px 12px; display:flex; justify-content:space-around; align-items:center; z-index:1000;">
    <a href="{{ url('/') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🏠</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Home</span>
    </a>
    <a href="{{ url('/smartcart') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🛒</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Cart</span>
    </a>
    <a href="{{ url('/profile') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; background:#0EA5E9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:4px; box-shadow:0 2px 8px rgba(14,165,233,0.3);">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#0EA5E9;">Profile</span>
    </a>
  </div>

</div>
@endsection
