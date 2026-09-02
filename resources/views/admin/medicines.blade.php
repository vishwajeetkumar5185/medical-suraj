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
        <p style="color:rgba(255,255,255,0.7); font-size:12px; margin:0;">Master Catalog Management</p>
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
    <a href="{{ url('/admin/approvals') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1; position:relative;">
      <span style="font-size:15px;">✅</span>Approvals
      @if($pendingApprovalsCount > 0)
        <div style="position:absolute; top:2px; right:2px; background:#DC2626; color:#fff; font-size:8px; font-weight:900; width:14px; height:14px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
          {{ $pendingApprovalsCount }}
        </div>
      @endif
    </a>
    <a href="{{ url('/admin/medicines') }}" class="admin-tab active" style="background:linear-gradient(135deg,#1F2937,#4B5563); color:#fff; flex:1; box-shadow: 0 4px 12px rgba(31,41,55,0.3);">
      <span style="font-size:15px;">💊</span>Medicines
    </a>
    <a href="{{ url('/admin/commission') }}" class="admin-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:15px;">💰</span>Commission
    </a>
  </div>

  <div class="scroll" style="flex:1;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
      <div>
        <h3 style="font-weight:900; font-size:17px; color:#1A1A1A; margin:0;">💊 Master Medicine Catalog</h3>
        <p style="font-size:12px; color:#6B7280; margin-top:2px;">Listed medicines for pharmacy inventory mapping</p>
      <a href="{{ url('/admin/medicines?showForm=1') }}" class="btn-blue" style="font-size:13px; text-decoration:none; padding:10px 18px; border-radius:12px; box-shadow:0 4px 12px rgba(37,99,235,0.25);">+ Add New Medicine</a>
    </div>

    <!-- Master Medicine Add Form -->
    @if(request('showForm'))
      <div style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 8px 30px rgba(0,0,0,0.08); border:2px solid #BFDBFE; margin-bottom:20px; max-width:550px; margin-left:auto; margin-right:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
          <h4 style="font-weight:900; font-size:15px; color:#1E3A8A; margin:0;">➕ Add New Master Medicine</h4>
          <a href="{{ url('/admin/medicines') }}" style="color:#6B7280; text-decoration:none; font-weight:800; font-size:18px;">✕</a>
        </div>
        
        <form action="{{ url('/admin/medicines/add') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div style="margin-bottom:12px;">
            <label style="font-size:11px; font-weight:800; color:#374151; display:block; margin-bottom:4px;">Medicine Name *</label>
            <input type="text" name="name" class="form-input" placeholder="e.g. Paracetamol 650mg" required style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:10px; font-size:13px;">
          </div>
          <div style="margin-bottom:12px;">
            <label style="font-size:11px; font-weight:800; color:#374151; display:block; margin-bottom:4px;">Category *</label>
            <select name="category" class="form-input" required style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:10px; font-size:13px; background:#fff;">
              <option value="">Select Category</option>
              @foreach(['Fever', 'Antibiotic', 'Allergy', 'Acidity', 'Pain', 'Diabetes', 'Heart', 'Supplement', 'Skin', 'Eye', 'Dental'] as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div style="flex:1;">
              <label style="font-size:11px; font-weight:800; color:#374151; display:block; margin-bottom:4px;">MRP (₹) *</label>
              <input type="number" step="0.01" name="mrp" class="form-input" placeholder="0.00" required style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:10px; font-size:13px;">
            </div>
            <div style="flex:1;">
              <label style="font-size:11px; font-weight:800; color:#374151; display:block; margin-bottom:4px;">Selling Price (₹) *</label>
              <input type="number" step="0.01" name="price" class="form-input" placeholder="0.00" required style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:10px; font-size:13px;">
            </div>
          </div>
          <div style="margin-bottom:16px;">
            <label style="font-size:11px; font-weight:800; color:#374151; display:block; margin-bottom:4px;">Medicine Images</label>
            <input type="file" name="images[]" multiple class="form-input" accept="image/*" style="width:100%; font-size:11px;">
          </div>
          <div style="display:flex; gap:10px;">
            <a href="{{ url('/admin/medicines') }}" class="btn-outline" style="flex:1; text-decoration:none; text-align:center; padding:11px; font-size:13px; color:#555; border:1px solid #D1D5DB; border-radius:10px;">Cancel</a>
            <button type="submit" class="btn-blue" style="flex:1; padding:11px; font-size:13px; border-radius:10px;">Save Medicine</button>
          </div>
        </form>
      </div>
    @endif

    <!-- Master Medicine Grid -->
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:14px; margin-bottom:24px;">
      @foreach($medicines as $med)
        <div style="background:#fff; border-radius:16px; padding:14px; display:flex; gap:12px; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.04); border:1px solid #E5E7EB; position:relative;">
          
          <!-- Image / Emoji Container -->
          <div style="width:58px; height:58px; border-radius:14px; background:#F3F4F6; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; overflow:hidden; border:1px solid #E5E7EB; position:relative;">
            @if(!empty($med->images) && is_array($med->images) && count($med->images) > 0)
              <img src="{{ asset($med->images[0]) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
              {{ $med->emoji ?? '💊' }}
            @endif
          </div>

          <!-- Medicine Details -->
          <div style="flex:1; min-width:0;">
            <div style="font-weight:800; font-size:14px; color:#111827; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:3px;">
              {{ $med->name }}
            </div>
            
            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:4px;">
              <span style="font-size:10px; font-weight:800; padding:2px 8px; border-radius:6px; background:#EFF6FF; color:#1D4ED8; border:1px solid #DBEAFE;">
                {{ $med->category }}
              </span>
              @if(!empty($med->prescription_required) && $med->prescription_required === 'Yes')
                <span style="font-size:9.5px; font-weight:800; padding:2px 6px; border-radius:5px; background:#FEF2F2; color:#DC2626; border:1px solid #FECACA;">
                  Rx Required
                </span>
              @endif
            </div>

            <div style="display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:14px; color:#16A34A; font-weight:900;">₹{{ number_format($med->price, 2) }}</span>
              @if($med->mrp > $med->price)
                <span style="font-size:11px; color:#9CA3AF; text-decoration:line-through;">₹{{ number_format($med->mrp, 2) }}</span>
                <span style="font-size:10px; color:#DC2626; font-weight:800;">
                  ({{ round((($med->mrp - $med->price) / $med->mrp) * 100) }}% OFF)
                </span>
              @endif
            </div>
          </div>

          <!-- Delete Button -->
          <div style="flex-shrink:0;">
            <form action="{{ url('/admin/medicines/delete/'.$med->id) }}" method="POST" onsubmit="return confirm('Kya aap \'{{ addslashes($med->name) }}\' ko master catalog se delete karna chahte hain?');" style="margin:0;">
              @csrf
              @method('DELETE')
              <button type="submit" style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; width:36px; height:36px; cursor:pointer; font-size:14px; color:#DC2626; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" title="Delete Medicine">
                🗑️
              </button>
            </form>
          </div>

        </div>
      @endforeach
    </div>

    <!-- Pagination Links -->
    <div style="margin-top:20px; padding:10px 0; display:flex; justify-content:center;">
      {{ $medicines->links() }}
    </div>

  </div>
</div>
@endsection
