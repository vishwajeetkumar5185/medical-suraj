@foreach($catalog as $med)
  @php
    $qty = $cart[$med->id] ?? 0;
    $disc = $med->mrp > 0 ? round((($med->mrp - $med->price) / $med->mrp) * 100) : 0;
    $detailUrl = url('/medicine/'.$med->id.(!empty(request('shop_id')) ? '?shop_id='.request('shop_id') : ''));
  @endphp
  <div class="cart-item-card catalog-item-row" data-name="{{ strtolower($med->name) }}" data-category="{{ strtolower($med->category) }}" style="border: {{ $qty > 0 ? '2px solid #BFDBFE' : '2px solid transparent' }}; padding: 0 12px 0 0; margin-bottom:12px; height: 114px; display: flex; align-items: center; justify-content: space-between; overflow:hidden; position:relative;">
    <a href="{{ $detailUrl }}" style="display:flex; align-items:center; gap:12px; flex:1; text-decoration:none; text-align:left; color:inherit; height:100%; overflow:hidden;">
      <div style="width:114px; height:114px; display:flex; align-items:center; justify-content:center; font-size:42px; flex-shrink:0; overflow:hidden;">
        @if(!empty($med->images))
          @php
            $isMedAbsolute = strpos($med->images[0], 'http://') === 0 || strpos($med->images[0], 'https://') === 0;
            $medImgUrl = $isMedAbsolute ? $med->images[0] : asset($med->images[0]);
          @endphp
          <img src="{{ $medImgUrl }}" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:cover; display:block;">
        @else
          <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#F8FAFF; border-right:1px solid #E5E7EB;">
            {{ $med->emoji }}
          </div>
        @endif
      </div>
      <div style="flex:1; overflow:hidden; display:flex; flex-direction:column; gap:3px; padding: 10px 4px 10px 0;">
        <div style="font-weight:800; font-size:14.5px; color:#1A1A1A; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $med->name }}</div>
        <div style="font-size:11.5px; color:#888;">{{ $med->category }}</div>
        <div style="display:flex; flex-wrap:wrap; gap:6px; align-items:center; margin-top:2px;">
          <div style="font-size:16px; font-weight:900; color:#1A3C8F; white-space:nowrap;">₹{{ $med->mrp }}</div>
        </div>
      </div>
    </a>
    <div class="cart-controls" data-med-id="{{ $med->id }}">
      <!-- Add Button Form (visible when qty == 0) -->
      <form action="{{ url('/cart/add') }}" method="POST" class="cart-form add-form-el" style="{{ $qty == 0 ? 'display:block;' : 'display:none;' }}">
        @csrf
        <input type="hidden" name="medicine_id" value="{{ $med->id }}">
        <button type="submit" class="btn-blue" style="font-size:12px; padding:8px 14px;">+ Add</button>
      </form>

      <!-- Quantity Update controls (visible when qty > 0) -->
      <div class="qty-control-el" style="{{ $qty > 0 ? 'display:flex;' : 'display:none;' }}; align-items:center; gap:7px;">
        <form action="{{ url('/cart/update') }}" method="POST" class="cart-form dec-form-el">
          @csrf
          <input type="hidden" name="medicine_id" value="{{ $med->id }}">
          <input type="hidden" name="qty" class="qty-input-dec" value="{{ $qty - 1 }}">
          <button type="submit" style="width:28px; height:28px; border-radius:8px; border:2px solid #E5E7EB; background:#fff; font-size:16px; font-weight:900; color:#1A3C8F; cursor:pointer; display:flex; align-items:center; justify-content:center;">−</button>
        </form>
        <div class="qty-display" style="font-weight:900; font-size:14px; color:#1A3C8F; min-width:14px; text-align:center;">{{ $qty }}</div>
        <form action="{{ url('/cart/update') }}" method="POST" class="cart-form inc-form-el">
          @csrf
          <input type="hidden" name="medicine_id" value="{{ $med->id }}">
          <input type="hidden" name="qty" class="qty-input-inc" value="{{ $qty + 1 }}">
          <button type="submit" style="width:28px; height:28px; background:#1A3C8F; border:none; font-size:16px; font-weight:900; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">+</button>
        </form>
      </div>
    </div>
  </div>
@endforeach
