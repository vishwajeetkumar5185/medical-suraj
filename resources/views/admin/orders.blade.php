@extends('layouts.app')

@section('content')
<div class="screen">
  <!-- Admin Header -->
  <div class="hdr-gradient" style="background:linear-gradient(135deg,#1F2937,#374151,#4B5563); padding:24px 20px 24px; position:relative; overflow:hidden; flex-shrink:0; border-radius: 20px; margin-bottom:20px;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; position:relative; z-index:1;">
      <a href="{{ url('/admin') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0; text-decoration:none;">←</a>
      <div>
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">📋 Live Orders Manager</h2>
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Centralized Dispatch & Customer Location Control</p>
      </div>
    </div>

    <!-- Quick Stats Summary -->
    <div style="display:flex; gap:10px; position:relative; z-index:1;">
      <div style="flex:1; background:rgba(255,255,255,0.12); border-radius:16px; padding:12px; border:1px solid rgba(255,255,255,0.15);">
        <div style="font-size:18px; margin-bottom:2px;">📦</div>
        <div style="color:#fff; font-weight:900; font-size:20px;">{{ $totalOrdersCount }}</div>
        <div style="color:rgba(255,255,255,0.7); font-size:10.5px; font-weight:600;">Total Orders</div>
      </div>
      <div style="flex:1; background:rgba(255,255,255,0.12); border-radius:16px; padding:12px; border:1px solid rgba(255,255,255,0.15);">
        <div style="font-size:18px; margin-bottom:2px;">⏳</div>
        <div style="color:#F59E0B; font-weight:900; font-size:20px;">{{ $pendingCount }}</div>
        <div style="color:rgba(255,255,255,0.7); font-size:10.5px; font-weight:600;">Pending</div>
      </div>
      <div style="flex:1; background:rgba(255,255,255,0.12); border-radius:16px; padding:12px; border:1px solid rgba(255,255,255,0.15);">
        <div style="font-size:18px; margin-bottom:2px;">🛵</div>
        <div style="color:#60A5FA; font-weight:900; font-size:20px;">{{ $outForDeliveryCount }}</div>
        <div style="color:rgba(255,255,255,0.7); font-size:10.5px; font-weight:600;">On Delivery</div>
      </div>
    </div>
  </div>

  <!-- Admin Navigation Menu Bar -->
  <div style="display:flex; background:#fff; padding:10px 12px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:16px;">
    <a href="{{ url('/admin') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">📊</span>Overview
    </a>
    <a href="{{ url('/admin/orders') }}" class="admin-tab active" style="background:linear-gradient(135deg,#1F2937,#4B5563); color:#fff; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:800; box-shadow:0 4px 12px rgba(31,41,55,0.3);">
      <span style="font-size:15px;">📋</span>Orders
    </a>
    <a href="{{ url('/admin/coupons') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">🎟️</span>Coupons
    </a>
    <a href="{{ url('/admin/stores') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">🏪</span>Stores
    </a>
    <a href="{{ url('/admin/approvals') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700; position:relative;">
      <span style="font-size:15px;">✅</span>Approvals
      @if($pendingApprovalsCount > 0)
        <div style="position:absolute; top:2px; right:2px; background:#DC2626; color:#fff; font-size:8px; font-weight:900; width:14px; height:14px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
          {{ $pendingApprovalsCount }}
        </div>
      @endif
    </a>
    <a href="{{ url('/admin/medicines') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">💊</span>Medicines
    </a>
  </div>

  <!-- Status Filter Tabs -->
  <div style="display:flex; gap:6px; overflow-x:auto; padding-bottom:6px; margin-bottom:16px;">
    <a href="{{ url('/admin/orders?status=all') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'all' ? 'background:#1E3A8A; color:#fff;' : 'background:#E5E7EB; color:#4B5563;' }}">
      All Orders ({{ $totalOrdersCount }})
    </a>
    <a href="{{ url('/admin/orders?status=pending') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'pending' ? 'background:#D97706; color:#fff;' : 'background:#FEF3C7; color:#92400E;' }}">
      ⏳ Pending ({{ $pendingCount }})
    </a>
    <a href="{{ url('/admin/orders?status=accepted') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'accepted' ? 'background:#2563EB; color:#fff;' : 'background:#DBEAFE; color:#1E40AF;' }}">
      ✅ Accepted ({{ $acceptedCount }})
    </a>
    <a href="{{ url('/admin/orders?status=out for delivery') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'out for delivery' ? 'background:#7C3AED; color:#fff;' : 'background:#EDE9FE; color:#5B21B6;' }}">
      🛵 On Delivery ({{ $outForDeliveryCount }})
    </a>
    <a href="{{ url('/admin/orders?status=delivered') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'delivered' ? 'background:#059669; color:#fff;' : 'background:#D1FAE5; color:#065F46;' }}">
      🎉 Delivered ({{ $deliveredCount }})
    </a>
    <a href="{{ url('/admin/orders?status=cancelled') }}" style="text-decoration:none; padding:6px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; {{ $status === 'cancelled' ? 'background:#DC2626; color:#fff;' : 'background:#FEE2E2; color:#991B1B;' }}">
      ❌ Cancelled ({{ $cancelledCount }})
    </a>
  </div>

  <div class="scroll" style="flex:1;">
    @if(session('success'))
      <div style="background:#DCFCE7; border:1px solid #86EFAC; color:#166534; padding:12px; border-radius:12px; font-size:12.5px; font-weight:700; margin-bottom:14px;">
        ✅ {{ session('success') }}
      </div>
    @endif

    @if($orders->isEmpty())
      <div style="background:#fff; border-radius:18px; padding:40px 20px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.04); border:1px solid #E5E7EB;">
        <div style="font-size:40px; margin-bottom:10px;">📦</div>
        <h4 style="font-weight:900; color:#374151; margin-bottom:4px;">No Orders Found</h4>
        <p style="font-size:12px; color:#6B7280;">No orders matching the selected status query.</p>
      </div>
    @else
      <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($orders as $order)
          @php
            $badgeColor = '#6B7280';
            $badgeBg = '#F3F4F6';
            if ($order->status === 'Pending') { $badgeColor = '#B45309'; $badgeBg = '#FEF3C7'; }
            elseif ($order->status === 'Accepted') { $badgeColor = '#1D4ED8'; $badgeBg = '#EFF6FF'; }
            elseif ($order->status === 'Out for Delivery') { $badgeColor = '#6D28D9'; $badgeBg = '#F5F3FF'; }
            elseif ($order->status === 'Delivered') { $badgeColor = '#15803D'; $badgeBg = '#F0FDF4'; }
            elseif ($order->status === 'Cancelled') { $badgeColor = '#B91C1C'; $badgeBg = '#FEF2F2'; }

            $grandTotal = $order->total_price + $order->delivery_charge;
            $itemsList = is_array($order->items) ? $order->items : json_decode($order->items, true) ?? [];
          @endphp

          <div style="background:#fff; border-radius:20px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #E5E7EB;">
            
            <!-- Order Header -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; border-bottom:1px solid #F3F4F6; padding-bottom:10px;">
              <div>
                <div style="font-weight:900; font-size:16px; color:#1E3A8A;">Order #{{ $order->id }}</div>
                <div style="font-size:11px; color:#888; margin-top:2px;">
                  📅 {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }} | 🛵 {{ ucfirst($order->mode) }}
                </div>
              </div>
              <span style="background:{{ $badgeBg }}; color:{{ $badgeColor }}; font-size:11px; font-weight:900; padding:4px 10px; border-radius:12px; text-transform:uppercase;">
                {{ $order->status }}
              </span>
            </div>

            <!-- Customer & Delivery Info -->
            <div style="background:#F8FAFC; border-radius:14px; padding:12px; margin-bottom:12px; border:1px solid #E2E8F0;">
              <div style="font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:6px;">👤 Customer & Location Details</div>
              <div style="font-size:13px; font-weight:800; color:#1E293B;">{{ $order->user ? $order->user->name : 'Guest Customer' }}</div>
              
              @if($order->user && $order->user->phone)
                <div style="margin-top:3px;">
                  <a href="tel:{{ $order->user->phone }}" style="font-size:12px; font-weight:700; color:#2563EB; text-decoration:none;">
                    📞 {{ $order->user->phone }}
                  </a>
                </div>
              @endif

              @if($order->mode === 'delivery' && $order->delivery_address)
                <div style="font-size:12px; color:#334155; margin-top:6px; line-height:1.4;">
                  📍 <strong>Delivery Address:</strong> {{ $order->delivery_address }}
                </div>
                @php
                  $encodedAddress = urlencode($order->delivery_address);
                  $mapUrl = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
                @endphp
                <div style="margin-top:8px;">
                  <a href="{{ $mapUrl }}" target="_blank" style="display:inline-flex; align-items:center; gap:6px; background:#10B981; color:#fff; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:800; text-decoration:none;">
                    🗺️ Open Location in Google Maps
                  </a>
                </div>
              @endif
            </div>

            <!-- Assigned Shop Info & Re-assignment -->
            <div style="background:#EFF6FF; border-radius:14px; padding:12px; margin-bottom:12px; border:1px solid #BFDBFE;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <div style="font-size:11px; font-weight:800; color:#1E40AF; text-transform:uppercase;">🏪 Assigned Pharmacy</div>
                @if($order->shop)
                  <a href="tel:{{ $order->shop->phone }}" style="font-size:11px; font-weight:800; color:#2563EB; text-decoration:none;">
                    📞 {{ $order->shop->phone }}
                  </a>
                @endif
              </div>

              <div style="font-size:13px; font-weight:800; color:#1E3A8A; margin-bottom:8px;">
                {{ $order->shop ? $order->shop->name : 'Unassigned Shop' }}
              </div>

              <!-- Shop Reassignment Form -->
              <form action="{{ url('/admin/orders/assign-shop') }}" method="POST" style="display:flex; gap:6px; margin-top:4px;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <select name="shop_id" class="form-input" style="padding:6px 10px; font-size:11px; font-weight:700; border-radius:8px; border:1px solid #93C5FD; flex:1;">
                  @foreach($allShops as $s)
                    <option value="{{ $s->id }}" {{ $order->shop_id == $s->id ? 'selected' : '' }}>
                      {{ $s->name }}
                    </option>
                  @endforeach
                </select>
                <button type="submit" style="background:#1E40AF; color:#fff; border:none; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:800; cursor:pointer;">
                  Re-assign
                </button>
              </form>
            </div>

            <!-- Ordered Medicines List -->
            <div style="margin-bottom:12px;">
              <div style="font-size:11px; font-weight:800; color:#64748B; text-transform:uppercase; margin-bottom:6px;">💊 Ordered Medicines</div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach($itemsList as $item)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:#F8FAFC; padding:8px 10px; border-radius:8px; font-size:12px; border:1px solid #F1F5F9;">
                    <div style="font-weight:700; color:#1E293B;">
                      {{ $item['emoji'] ?? '💊' }} {{ $item['name'] }}
                    </div>
                    <div style="font-weight:800; color:#475569;">
                      Qty: {{ $item['quantity'] }} × ₹{{ number_format($item['price'], 2) }}
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- Financials & Charges Adjustment Form -->
            <div style="background:#F1F5F9; border-radius:14px; padding:12px; margin-bottom:14px; border:1px solid #E2E8F0;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <div style="font-size:11px; font-weight:800; color:#475569; text-transform:uppercase;">💵 Pricing & Charges Breakdown</div>
                <div style="font-size:16px; font-weight:900; color:#0F172A;">Grand Total: ₹{{ number_format(($order->total_price - $order->discount_amount) + $order->delivery_charge, 2) }}</div>
              </div>

              <!-- Inline Admin Adjust Form -->
              <form action="{{ url('/admin/orders/update-charges') }}" method="POST" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; background:#fff; padding:10px; border-radius:10px; border:1px solid #CBD5E1;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div style="flex:1; min-width:110px;">
                  <label style="font-size:10px; font-weight:800; color:#64748B; display:block; margin-bottom:2px;">🛵 Delivery Charge (₹)</label>
                  <input type="number" step="0.01" name="delivery_charge" value="{{ $order->delivery_charge }}" class="form-input" style="padding:6px 8px; font-size:12px; font-weight:800; border-radius:6px;">
                </div>
                <div style="flex:1; min-width:110px;">
                  <label style="font-size:10px; font-weight:800; color:#64748B; display:block; margin-bottom:2px;">🎟️ Discount / Offer (₹)</label>
                  <input type="number" step="0.01" name="discount_amount" value="{{ $order->discount_amount }}" class="form-input" style="padding:6px 8px; font-size:12px; font-weight:800; border-radius:6px; color:#059669;">
                </div>
                <button type="submit" style="background:#0F172A; color:#fff; border:none; padding:7px 12px; border-radius:6px; font-size:11px; font-weight:800; cursor:pointer;">
                  💾 Update Charges
                </button>
              </form>
            </div>

            <!-- Status Action Buttons -->
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
              @if($order->status !== 'Accepted')
                <form action="{{ url('/admin/orders/status') }}" method="POST" style="flex:1;">
                  @csrf
                  <input type="hidden" name="order_id" value="{{ $order->id }}">
                  <input type="hidden" name="status" value="Accepted">
                  <button type="submit" style="width:100%; background:#2563EB; color:#fff; border:none; padding:9px; border-radius:10px; font-size:11.5px; font-weight:800; cursor:pointer;">
                    ✅ Accept Order
                  </button>
                </form>
              @endif

              @if($order->status !== 'Out for Delivery' && $order->mode === 'delivery')
                <form action="{{ url('/admin/orders/status') }}" method="POST" style="flex:1;">
                  @csrf
                  <input type="hidden" name="order_id" value="{{ $order->id }}">
                  <input type="hidden" name="status" value="Out for Delivery">
                  <button type="submit" style="width:100%; background:#7C3AED; color:#fff; border:none; padding:9px; border-radius:10px; font-size:11.5px; font-weight:800; cursor:pointer;">
                    🛵 Out for Delivery
                  </button>
                </form>
              @endif

              @if($order->status !== 'Delivered')
                <form action="{{ url('/admin/orders/status') }}" method="POST" style="flex:1;">
                  @csrf
                  <input type="hidden" name="order_id" value="{{ $order->id }}">
                  <input type="hidden" name="status" value="Delivered">
                  <button type="submit" style="width:100%; background:#059669; color:#fff; border:none; padding:9px; border-radius:10px; font-size:11.5px; font-weight:800; cursor:pointer;">
                    🎉 Delivered
                  </button>
                </form>
              @endif

              @if($order->status !== 'Cancelled')
                <form action="{{ url('/admin/orders/status') }}" method="POST" style="flex:1;">
                  @csrf
                  <input type="hidden" name="order_id" value="{{ $order->id }}">
                  <input type="hidden" name="status" value="Cancelled">
                  <button type="submit" onclick="return confirm('Cancel this order?')" style="width:100%; background:#EF4444; color:#fff; border:none; padding:9px; border-radius:10px; font-size:11.5px; font-weight:800; cursor:pointer;">
                    ❌ Cancel
                  </button>
                </form>
              @endif
            </div>

          </div>
        @endforeach
      </div>

      <div style="margin-top:20px;">
        {{ $orders->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
