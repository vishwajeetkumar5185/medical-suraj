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
          <div style="color:#fff; font-size:16px; font-weight:800;">📍 My Addresses</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">Saved delivery locations</div>
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
    
    @if($orders->isEmpty())
      <!-- Empty State -->
      <div style="background:#fff; border-radius:16px; padding:40px 20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-align:center;">
        <div style="font-size:80px; margin-bottom:16px;">📍</div>
        <h3 style="font-weight:800; font-size:18px; color:#1A1A1A; margin:0 0 8px 0;">No Saved Addresses</h3>
        <p style="font-size:14px; color:#64748B; margin:0 0 20px 0; line-height:1.5;">
          Jab aap medicine order karenge delivery ke liye, tab address automatically save ho jayega yahan.
        </p>
        <a href="{{ url('/') }}" style="background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; padding:12px 24px; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none; display:inline-block;">
          🛒 Start Shopping
        </a>
      </div>
    @else
      <!-- Addresses List -->
      <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
          <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0;">📍 Saved Addresses</h3>
          <span style="background:#F0F9FF; color:#0EA5E9; font-size:11px; font-weight:800; padding:4px 8px; border-radius:12px; border:1px solid #BFDBFE;">{{ $orders->count() }} Locations</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($orders as $index => $address)
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:16px; position:relative;">
              <!-- Address Icon & Label -->
              <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:12px;">
                <div style="width:40px; height:40px; background:linear-gradient(135deg, #0EA5E9, #0284C7); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <span style="font-size:18px; color:#fff;">{{ $index == 0 ? '🏠' : ($index == 1 ? '🏢' : '📍') }}</span>
                </div>
                <div style="flex:1;">
                  <div style="font-weight:800; font-size:14px; color:#1A1A1A; margin-bottom:4px;">
                    {{ $index == 0 ? 'Home Address' : ($index == 1 ? 'Office Address' : 'Other Address') }}
                  </div>
                  <p style="font-size:13px; color:#64748B; margin:0; line-height:1.4;">{{ $address }}</p>
                </div>
                @if($index == 0)
                  <div style="background:#DCFCE7; color:#166534; font-size:10px; font-weight:700; padding:4px 8px; border-radius:8px;">DEFAULT</div>
                @endif
              </div>

              <!-- Action Buttons -->
              <div style="display:flex; gap:8px;">
                <button onclick="alert('Edit address feature coming soon!')" style="flex:1; background:#F1F5F9; color:#64748B; border:none; border-radius:8px; padding:8px 12px; font-size:12px; font-weight:700; cursor:pointer;">
                  ✏️ Edit
                </button>
                <button onclick="alert('Share address feature coming soon!')" style="flex:1; background:#F0F9FF; color:#0EA5E9; border:1px solid #BFDBFE; border-radius:8px; padding:8px 12px; font-size:12px; font-weight:700; cursor:pointer;">
                  📤 Share
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Add New Address Button -->
      <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <button onclick="alert('Add new address coming soon!')" style="width:100%; background:linear-gradient(135deg, #10B981, #059669); color:#fff; border:none; border-radius:12px; padding:16px; font-size:14px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
          <span style="font-size:18px;">➕</span>
          Add New Address
        </button>
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
