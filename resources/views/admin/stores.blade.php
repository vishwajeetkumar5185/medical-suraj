@extends('layouts.app')

@section('content')
<div class="screen">
  <!-- Admin Header -->
  <div class="hdr-gradient" style="background:linear-gradient(135deg,#1F2937,#374151,#4B5563); padding:24px 20px 24px; position:relative; overflow:hidden; flex-shrink:0; border-radius: 20px; margin-bottom:20px;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; position:relative; z-index:1;">
      <a href="{{ url('/profile') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <div>
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">🛡️ Admin Panel</h2>
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Dawalo Registered Stores</p>
      </div>
    </div>
  </div>

  <!-- Admin Navigation Menu Bar -->
  <div style="display:flex; background:#fff; padding:10px 12px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:20px;">
    <a href="{{ url('/admin') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">📊</span>Overview
    </a>
    <a href="{{ url('/admin/orders') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">📋</span>Orders
    </a>
    <a href="{{ url('/admin/stores') }}" class="admin-tab active" style="background:linear-gradient(135deg,#1F2937,#4B5563); color:#fff; flex:1; box-shadow: 0 4px 12px rgba(31,41,55,0.3);">
      <span style="font-size:15px;">🏪</span>Stores
    </a>
    <a href="{{ url('/admin/approvals') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; position:relative;">
      <span style="font-size:15px;">✅</span>Approvals
      @if($pendingApprovalsCount > 0)
        <div style="position:absolute; top:2px; right:2px; background:#DC2626; color:#fff; font-size:8px; font-weight:900; width:14px; height:14px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
          {{ $pendingApprovalsCount }}
        </div>
      @endif
    </a>
    <a href="{{ url('/admin/medicines') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">💊</span>Medicines
    </a>
    <a href="{{ url('/admin/commission') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">💰</span>Commission
    </a>
  </div>

  <div class="scroll" style="flex:1;">
    @php
      $statusStyles = [
        'approved' => ['bg' => '#DCFCE7', 'color' => '#16A34A', 'label' => 'Approved'],
        'pending' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Pending'],
        'blocked' => ['bg' => '#FEE2E2', 'color' => '#DC2626', 'label' => 'Blocked']
      ];
    @endphp

    <div class="responsive-grid">
      @foreach($stores as $s)
        @php
          $style = $statusStyles[$s->status] ?? ['bg' => '#F3F4F6', 'color' => '#555', 'label' => $s->status];
        @endphp
        <div class="card" style="padding:14px; margin:0; display:flex; flex-direction:column; gap:10px;">
          <div style="display:flex; gap:12px; align-items:flex-start;">
            <div style="width:64px; height:64px; border-radius:10px; background:#F3F4F6; flex-shrink:0; overflow:hidden; border:1px solid #E5E7EB; display:flex; align-items:center; justify-content:center;">
              @if($s->image)
                <img src="{{ asset($s->image) }}" style="width:100%; height:100%; object-fit:cover;">
              @else
                <span style="font-size:28px;">🏪</span>
              @endif
            </div>
            <div style="flex:1; min-width:0;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div style="font-weight:800; font-size:14px; color:#1A1A1A; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $s->name }}</div>
                <div style="display:flex; gap:4px; flex-wrap:wrap; justify-content:flex-end;">
                  <span style="font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; background:{{ $style['bg'] }}; color:{{ $style['color'] }}; white-space:nowrap;">
                    {{ $style['label'] }}
                  </span>
                  <span style="font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; background:{{ $s->is_online ? '#DCFCE7' : '#FEE2E2' }}; color:{{ $s->is_online ? '#15803D' : '#DC2626' }}; white-space:nowrap;">
                    {{ $s->is_online ? '🟢 Online' : '🔴 Offline' }}
                  </span>
                </div>
              </div>
              <div style="font-size:12px; color:#4B5563; margin-top:2px; font-weight:700;">👤 {{ $s->owner_name }}</div>
              <div style="font-size:11.5px; color:#6B7280; margin-top:2px;">📍 {{ $s->area }}</div>
            </div>
          </div>
          
          <!-- Primary Quick Info -->
          <div style="font-size:11.5px; color:#374151; display:flex; flex-direction:column; gap:4px; background:#F9FAFB; padding:8px 10px; border-radius:8px; border:1px solid #F3F4F6;">
            <div>📞 <strong>Phone:</strong> <a href="tel:{{ $s->phone ?? $s->user?->phone }}" style="color:#1A3C8F; text-decoration:none; font-weight:800;">{{ $s->phone ?? $s->user?->phone ?? 'N/A' }}</a></div>
            <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">📍 <strong>Address:</strong> {{ $s->address ?? $s->area }}</div>
          </div>

          <!-- Quick Sales & Orders Metrics Bar -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #E5E7EB; border-bottom:1px dashed #E5E7EB; padding:8px 0; font-size:12px; color:#1F2937; font-weight:800;">
            <span>📦 {{ $s->orders_count ?? 0 }} orders</span>
            <span style="color:#059669;">💰 ₹{{ number_format($s->orders_sum_total_price ?? 0, 1) }}</span>
          </div>

          <!-- Expandable Complete Shop Details -->
          <details style="background:#EEF2FF; border:1px solid #C7D2FE; border-radius:10px; padding:8px 10px; font-size:11.5px; color:#1E1B4B;">
            <summary style="font-weight:800; color:#3730A3; cursor:pointer; font-size:12px; outline:none; display:flex; justify-content:space-between; align-items:center;">
              <span>ℹ️ View Complete Shop Details</span>
            </summary>
            
            <div style="margin-top:8px; display:flex; flex-direction:column; gap:6px; border-top:1px dashed #A5B4FC; padding-top:8px; line-height:1.4;">
              <div><strong>🏪 Store Name:</strong> {{ $s->name }}</div>
              <div><strong>👤 Owner Name:</strong> {{ $s->owner_name }}</div>
              <div><strong>📞 Shop Phone:</strong> {{ $s->phone ?? 'N/A' }}</div>
              <div><strong>📍 Area:</strong> {{ $s->area }}</div>
              <div><strong>🏡 Full Address:</strong> {{ $s->address ?? 'Not specified' }}</div>
              @if($s->latitude && $s->longitude)
                <div><strong>🗺️ Map Location:</strong> {{ $s->latitude }}, {{ $s->longitude }} (<a href="https://www.google.com/maps?q={{ $s->latitude }},{{ $s->longitude }}" target="_blank" style="color:#2563EB; font-weight:700;">Open Map 🗺️</a>)</div>
              @endif
              <div><strong>⏰ Timings:</strong> {{ $s->opens_at ? date('h:i A', strtotime($s->opens_at)) : '09:00 AM' }} - {{ $s->closes_at ? date('h:i A', strtotime($s->closes_at)) : '09:00 PM' }}</div>
              <div><strong>🛵 Delivery Mode:</strong> {{ $s->delivery_enabled ? 'Enabled (' . ($s->delivery_radius_km ?? 10) . ' KM radius)' : 'Disabled (Self Pickup Only)' }}</div>
              @if($s->delivery_enabled)
                <div><strong>💵 Delivery Charge:</strong> {{ $s->delivery_charge_type === 'fixed' ? 'Fixed ₹' . ($s->delivery_charge_fixed ?? 20) : '₹' . ($s->delivery_charge_per_km ?? 8) . ' / KM' }}</div>
              @endif
              <div><strong>🏷️ Active Offer:</strong> {{ $s->offer_discount_pct > 0 ? $s->offer_discount_pct . '% OFF (Min Bill ₹' . $s->offer_min_bill . ')' : 'None' }}</div>
              <div><strong>🟢 Live Status:</strong> <span style="font-weight:800; color:{{ $s->is_online ? '#15803D' : '#DC2626' }};">{{ $s->is_online ? 'Online (Accepting Orders)' : 'Offline' }}</span></div>

              @if($s->user)
                <div style="margin-top:4px; padding-top:6px; border-top:1px solid #C7D2FE; color:#312E81;">
                  <strong>👤 Linked User Account:</strong><br>
                  • Name: {{ $s->user->name }}<br>
                  • Email: {{ $s->user->email }}<br>
                  • Phone: {{ $s->user->phone ?? 'N/A' }}
                </div>
              @endif
            </div>
          </details>

          <!-- Edit Shop Image -->
          <form action="{{ url('/admin/stores/image') }}" method="POST" enctype="multipart/form-data" style="margin:0;">
            @csrf
            <input type="hidden" name="shop_id" value="{{ $s->id }}">
            <label class="form-label" style="font-size:10px; margin-bottom:4px; font-weight:800; color:#4B5563;">Change Store Image:</label>
            <div style="display:flex; gap:6px;">
              <input type="file" name="shop_image" accept="image/*" style="font-size:10px; flex:1;" required>
              <button type="submit" class="btn-blue" style="font-size:10px; padding:6px 10px; border-radius:8px;">Upload</button>
            </div>
          </form>
          
          <div style="margin-top:4px; display:flex; gap:8px;">
            <div style="flex:1;">
              <form action="{{ url('/admin/stores/status') }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="shop_id" value="{{ $s->id }}">
                
                @if($s->status !== 'blocked')
                  <input type="hidden" name="status" value="blocked">
                  <button type="submit" class="btn-danger" style="width:100%; padding:10px; font-size:12px;">🚫 Block Store</button>
                @else
                  <input type="hidden" name="status" value="approved">
                  <button type="submit" class="btn-green" style="width:100%; padding:10px; font-size:12px; background:#F0FDF4; border:1px solid #BBF7D0; color:#16A34A;">✅ Approve / Unblock</button>
                @endif
              </form>
            </div>

            <form action="{{ url('/admin/stores/delete/' . $s->id) }}" method="POST" onsubmit="return confirm('Kya aap iss store \'{{ addslashes($s->name) }}\' aur iska sara inventory data database se permanently delete karna chahte hain?')" style="margin:0;">
              @csrf
              @method('DELETE')
              <button type="submit" style="background:#EF4444; color:#fff; border:none; border-radius:10px; padding:10px 14px; font-size:12px; font-weight:800; cursor:pointer; height:100%;" title="Delete Store Permanently">
                🗑️ Delete
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
