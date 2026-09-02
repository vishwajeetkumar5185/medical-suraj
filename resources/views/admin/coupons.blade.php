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
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">🎟️ Coupons & Offer Manager</h2>
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Create Promo Codes, Flat Discounts & Minimum Order Offers</p>
      </div>
    </div>
  </div>

  <!-- Admin Navigation Menu Bar -->
  <div style="display:flex; background:#fff; padding:10px 12px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:20px;">
    <a href="{{ url('/admin') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">📊</span>Overview
    </a>
    <a href="{{ url('/admin/orders') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:700;">
      <span style="font-size:15px;">📋</span>Orders
    </a>
    <a href="{{ url('/admin/coupons') }}" class="admin-tab active" style="background:linear-gradient(135deg,#1F2937,#4B5563); color:#fff; flex:1; text-decoration:none; padding:8px 12px; text-align:center; border-radius:10px; font-size:12px; font-weight:800; box-shadow: 0 4px 12px rgba(31,41,55,0.3);">
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

  <div class="scroll" style="flex:1;">
    @if(session('success'))
      <div style="background:#DCFCE7; border:1px solid #86EFAC; color:#166534; padding:12px; border-radius:12px; font-size:12.5px; font-weight:700; margin-bottom:16px;">
        ✅ {{ session('success') }}
      </div>
    @endif

    <!-- Global Delivery & Minimum Order Settings Card -->
    <div style="background:#fff; border-radius:20px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #BFDBFE; margin-bottom:20px; background:linear-gradient(180deg,#EFF6FF,#fff);">
      <h3 style="font-weight:900; font-size:15px; color:#1E3A8A; margin-bottom:4px;">⚙️ Global Delivery & Minimum Order Rules</h3>
      <p style="font-size:12px; color:#475569; margin-bottom:14px;">Set global delivery charge and enforce minimum order value for home delivery.</p>

      <form action="{{ url('/admin/settings/delivery') }}" method="POST">
        @csrf
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px;">
          <div style="flex:1; min-width:130px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px; color:#1E40AF;">Fixed Delivery Charge (₹) *</label>
            <input type="number" step="0.01" name="delivery_charge" value="{{ $deliveryCharge ?? 20 }}" required class="form-input" style="padding:10px; font-size:13px; font-weight:800;">
          </div>
          <div style="flex:1; min-width:130px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px; color:#B45309;">Min Order Bill for Delivery (₹) *</label>
            <input type="number" step="0.01" name="min_delivery_order" value="{{ $minDeliveryOrder ?? 150 }}" required class="form-input" style="padding:10px; font-size:13px; font-weight:800; color:#B45309; border-color:#FCD34D;">
          </div>
          <div style="flex:1; min-width:130px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px; color:#047857;">Free Delivery Above Bill (₹)</label>
            <input type="number" step="0.01" name="free_delivery_min" value="{{ $freeDeliveryMin ?? 500 }}" class="form-input" style="padding:10px; font-size:13px; font-weight:800;">
          </div>
        </div>

        <button type="submit" style="width:100%; padding:12px; background:linear-gradient(135deg,#1E40AF,#2563EB); color:#fff; border:none; border-radius:12px; font-weight:900; font-size:13.5px; cursor:pointer;">
          💾 Save Global Delivery Rules
        </button>
      </form>
    </div>

    <!-- Add New Coupon Card -->
    <div style="background:#fff; border-radius:20px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #E5E7EB; margin-bottom:20px;">
      <h3 style="font-weight:900; font-size:15px; color:#1E3A8A; margin-bottom:14px;">➕ Create New Coupon Code</h3>
      
      <form action="{{ url('/admin/coupons/add') }}" method="POST">
        @csrf
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
          <div style="flex:1; min-width:140px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px;">Promo Code *</label>
            <input type="text" name="code" placeholder="e.g. WELCOME50" required class="form-input" style="padding:10px; font-size:13px; font-weight:800; text-transform:uppercase;">
          </div>
          <div style="flex:1; min-width:140px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px;">Discount Type *</label>
            <select name="type" class="form-input" style="padding:10px; font-size:13px; font-weight:700;">
              <option value="flat">Flat ₹ Discount</option>
              <option value="percent">Percentage % Off</option>
            </select>
          </div>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
          <div style="flex:1; min-width:140px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px;">Discount Value (₹ or %) *</label>
            <input type="number" step="0.01" name="value" placeholder="e.g. 50" required class="form-input" style="padding:10px; font-size:13px;">
          </div>
          <div style="flex:1; min-width:140px;">
            <label class="form-label" style="font-weight:800; font-size:11.5px;">Min Order Bill Amount (₹)</label>
            <input type="number" step="0.01" name="min_order_amount" placeholder="e.g. 200 (optional)" class="form-input" style="padding:10px; font-size:13px;">
          </div>
        </div>

        <button type="submit" class="btn-blue" style="width:100%; padding:12px; background:linear-gradient(135deg,#1E3A8A,#2563EB); border:none; border-radius:12px; font-weight:900; font-size:14px; cursor:pointer;">
          🚀 Save Promo Coupon
        </button>
      </form>
    </div>

    <!-- Existing Coupons List -->
    <div style="background:#fff; border-radius:20px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #E5E7EB;">
      <h3 style="font-weight:900; font-size:15px; color:#1E3A8A; margin-bottom:14px;">🎟️ Active Promo Coupons List</h3>

      @if($coupons->isEmpty())
        <div style="text-align:center; padding:30px; color:#888;">
          <div style="font-size:32px; margin-bottom:8px;">🏷️</div>
          <p style="font-size:13px; margin:0;">No promo coupons created yet. Add your first coupon above!</p>
        </div>
      @else
        <div style="display:flex; flex-direction:column; gap:10px;">
          @foreach($coupons as $cp)
            <div style="display:flex; justify-content:space-between; align-items:center; background:#F8FAFC; border:1px dashed #CBD5E1; padding:12px 16px; border-radius:14px;">
              <div>
                <div style="font-weight:900; font-size:15px; color:#1E3A8A; letter-spacing:0.5px;">
                  🎟️ {{ $cp->code }}
                </div>
                <div style="font-size:11.5px; color:#475569; margin-top:2px;">
                  @if($cp->type === 'flat')
                    <strong>Flat ₹{{ number_format($cp->value, 2) }} Off</strong>
                  @else
                    <strong>{{ $cp->value }}% Off</strong>
                  @endif
                  @if($cp->min_order_amount > 0)
                    • Min order ₹{{ number_format($cp->min_order_amount, 2) }}
                  @endif
                </div>
              </div>

              <form action="{{ url('/admin/coupons/delete/'.$cp->id) }}" method="POST" onsubmit="return confirm('Delete this coupon code?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:#FEE2E2; color:#DC2626; border:none; padding:6px 12px; border-radius:8px; font-weight:800; font-size:11px; cursor:pointer;">
                  🗑️ Delete
                </button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
