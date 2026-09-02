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
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Pending Pharmacy Reviews</p>
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
    <a href="{{ url('/admin/stores') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">🏪</span>Stores
    </a>
    <a href="{{ url('/admin/approvals') }}" class="admin-tab active" style="background:linear-gradient(135deg,#1F2937,#4B5563); color:#fff; flex:1; box-shadow: 0 4px 12px rgba(31,41,55,0.3); position:relative;">
      <span style="font-size:15px;">✅</span>Approvals
      @if($pendingApprovals->count() > 0)
        <div style="position:absolute; top:2px; right:2px; background:#DC2626; color:#fff; font-size:8px; font-weight:900; width:14px; height:14px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
          {{ $pendingApprovals->count() }}
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
    @if($pendingApprovals->isEmpty())
      <div style="text-align:center; padding:40px 20px; color:#888;">
        <div style="font-size:40px; margin-bottom:10px;">✅</div>
        <div style="font-weight:700; font-size:15px;">Sab clear hai!</div>
        <div style="font-size:12px; margin-top:4px;">Koi pending pharmacy registration review nahi hai.</div>
      </div>
    @else
      <div class="responsive-grid">
        @foreach($pendingApprovals as $s)
          <div class="card" style="padding:14px; border:1px solid #FDE68A; margin:0; display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; gap:12px; align-items:flex-start;">
              <div style="width:64px; height:64px; border-radius:10px; background:#F3F4F6; flex-shrink:0; overflow:hidden; border:1px solid #E5E7EB; display:flex; align-items:center; justify-content:center;">
                @if($s->image)
                  <img src="{{ asset($s->image) }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                  <span style="font-size:28px;">🏪</span>
                @endif
              </div>
              <div style="flex:1; min-width:0;">
                <div style="font-weight:800; font-size:14px; color:#1A1A1A; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $s->name }}</div>
                <div style="font-size:12px; color:#4B5563; margin-top:2px; font-weight:700;">👤 {{ $s->owner_name }}</div>
                <div style="font-size:11.5px; color:#6B7280; margin-top:2px;">📍 {{ $s->area }}</div>
              </div>
            </div>

            <!-- Primary Quick Info -->
            <div style="font-size:11.5px; color:#374151; display:flex; flex-direction:column; gap:4px; background:#FEF3C7; padding:8px 10px; border-radius:8px; border:1px solid #FDE68A;">
              <div>📞 <strong>Phone:</strong> <a href="tel:{{ $s->phone ?? $s->user?->phone }}" style="color:#1A3C8F; text-decoration:none; font-weight:800;">{{ $s->phone ?? $s->user?->phone ?? 'N/A' }}</a></div>
              <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">📍 <strong>Address:</strong> {{ $s->address ?? $s->area }}</div>
            </div>

            <!-- Expandable Complete Shop Details -->
            <details style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:10px; padding:8px 10px; font-size:11.5px; color:#78350F;">
              <summary style="font-weight:800; color:#92400E; cursor:pointer; font-size:12px; outline:none; display:flex; justify-content:space-between; align-items:center;">
                <span>ℹ️ View Complete Application Details</span>
              </summary>
              
              <div style="margin-top:8px; display:flex; flex-direction:column; gap:6px; border-top:1px dashed #F59E0B; padding-top:8px; line-height:1.4;">
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

                @if($s->user)
                  <div style="margin-top:4px; padding-top:6px; border-top:1px solid #FCD34D; color:#78350F;">
                    <strong>👤 Applicant User Account:</strong><br>
                    • Name: {{ $s->user->name }}<br>
                    • Email: {{ $s->user->email }}<br>
                    • Phone: {{ $s->user->phone ?? 'N/A' }}
                  </div>
                @endif
              </div>
            </details>
            
            <div style="display:flex; gap:8px;">
              <form action="{{ url('/admin/stores/status') }}" method="POST" style="flex:1;">
                @csrf
                <input type="hidden" name="shop_id" value="{{ $s->id }}">
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn-blue" style="width:100%; font-size:12px; padding:10px;">✅ Approve</button>
              </form>
              
              <form action="{{ url('/admin/stores/status') }}" method="POST" style="flex:1;">
                @csrf
                <input type="hidden" name="shop_id" value="{{ $s->id }}">
                <input type="hidden" name="status" value="blocked">
                <button type="submit" class="btn-danger" style="width:100%; font-size:12px; padding:10px;">❌ Block</button>
              </form>

              <form action="{{ url('/admin/stores/delete/' . $s->id) }}" method="POST" onsubmit="return confirm('Kya aap iss store \'{{ addslashes($s->name) }}\' ko database se permanently delete karna chahte hain?')" style="margin:0;">
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
    @endif
  </div>
</div>
@endsection
