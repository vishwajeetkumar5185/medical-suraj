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
          <div style="color:#fff; font-size:16px; font-weight:800;">🔔 Notifications</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">Recent updates & alerts</div>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">💬</span>
        </div>
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center; position:relative;">
          <span style="font-size:18px;">🔔</span>
          <div style="position:absolute; top:2px; right:2px; width:8px; height:8px; background:#EF4444; border-radius:50%; border:1px solid #fff;"></div>
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
    
    <!-- Notification Settings -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0 0 16px 0;">⚙️ Notification Settings</h3>
      
      <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #F1F5F9;">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:20px;">📱</span>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">Push Notifications</div>
            <div style="font-size:11px; color:#64748B;">Order updates & offers</div>
          </div>
        </div>
        <div style="width:44px; height:24px; background:#0EA5E9; border-radius:12px; position:relative; cursor:pointer;">
          <div style="width:20px; height:20px; background:#fff; border-radius:50%; position:absolute; top:2px; right:2px; box-shadow:0 1px 3px rgba(0,0,0,0.2);"></div>
        </div>
      </div>

      <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0;">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:20px;">📧</span>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">Email Notifications</div>
            <div style="font-size:11px; color:#64748B;">Weekly summaries & promotions</div>
          </div>
        </div>
        <div style="width:44px; height:24px; background:#E5E7EB; border-radius:12px; position:relative; cursor:pointer;">
          <div style="width:20px; height:20px; background:#fff; border-radius:50%; position:absolute; top:2px; left:2px; box-shadow:0 1px 3px rgba(0,0,0,0.2);"></div>
        </div>
      </div>
    </div>

    <!-- Notifications List -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0;">📋 Recent Notifications</h3>
        <button onclick="alert('Mark all as read coming soon!')" style="background:#F0F9FF; color:#0EA5E9; border:1px solid #BFDBFE; border-radius:8px; padding:6px 12px; font-size:11px; font-weight:700; cursor:pointer;">
          Mark All Read
        </button>
      </div>

      <div style="display:flex; flex-direction:column; gap:12px;">
        @foreach($notifications as $index => $notif)
          <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:16px; position:relative; border-left:4px solid {{ $index == 0 ? '#0EA5E9' : '#E5E7EB' }};">
            <!-- Notification Header -->
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:8px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:20px;">{{ $index == 0 ? '🎉' : ($index == 1 ? '💰' : '📦') }}</span>
                <div>
                  <div style="font-weight:800; font-size:14px; color:#1A1A1A;">{{ $notif['title'] }}</div>
                  <div style="font-size:11px; color:#64748B;">{{ $notif['time'] }}</div>
                </div>
              </div>
              @if($index == 0)
                <div style="width:8px; height:8px; background:#0EA5E9; border-radius:50%;"></div>
              @endif
            </div>

            <!-- Notification Content -->
            <p style="font-size:13px; color:#374151; margin:0 0 12px 0; line-height:1.4; padding-left:28px;">
              {{ $notif['text'] }}
            </p>

            <!-- Action Buttons -->
            <div style="display:flex; gap:8px; padding-left:28px;">
              <button onclick="alert('View details coming soon!')" style="background:#0EA5E9; color:#fff; border:none; border-radius:6px; padding:6px 12px; font-size:11px; font-weight:700; cursor:pointer;">
                View Details
              </button>
              <button onclick="this.parentElement.parentElement.style.display='none'" style="background:#F1F5F9; color:#64748B; border:none; border-radius:6px; padding:6px 12px; font-size:11px; font-weight:700; cursor:pointer;">
                Dismiss
              </button>
            </div>
          </div>
        @endforeach

        <!-- Load More Notifications -->
        <div style="text-align:center; padding:16px;">
          <button onclick="alert('Load more notifications coming soon!')" style="background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0; border-radius:12px; padding:12px 24px; font-size:12px; font-weight:700; cursor:pointer;">
            Load More Notifications
          </button>
        </div>
      </div>
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
