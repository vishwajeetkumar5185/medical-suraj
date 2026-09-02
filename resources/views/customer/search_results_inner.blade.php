<div style="background:#fff; border-radius: 0; padding: 0; box-shadow: none;">
  <div class="responsive-grid" style="background: #fff; padding: 12px 14px 0;">
    @foreach($medicines as $idx => $med)
      @php
        $qty = $cart[$med->id] ?? 0;
        $disc = $med->mrp > 0 ? round((($med->mrp - $med->price) / $med->mrp) * 100) : 0;
        $detailUrl = url('/medicine/'.$med->id.(!empty(request('shop_id')) ? '?shop_id='.request('shop_id') : ''));
      @endphp
      <div class="med-row" style="background:#fff; border: 1px solid #E2E8F0; border-radius: 16px; display:flex; padding: 10px; overflow:hidden; align-items:center; justify-content:space-between; margin-bottom: 10px; box-shadow: 0 1px 6px rgba(0,0,0,0.03); text-decoration:none; color:inherit; width:100%; height:96px;">
        <a href="{{ $detailUrl }}" style="overflow:hidden; position:relative; display:flex; width:76px; height:76px; border-radius:12px; flex-shrink:0; align-items:center; justify-content:center; text-decoration:none; border:none; background:#F8FAFF;">
          @if(!empty($med->images))
            @php
              $isRelAbsolute = strpos($med->images[0], 'http://') === 0 || strpos($med->images[0], 'https://') === 0;
              $relImgUrl = $isRelAbsolute ? $med->images[0] : asset($med->images[0]);
            @endphp
            <img src="{{ $relImgUrl }}" referrerpolicy="no-referrer" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" style="width:100%; height:100%; object-fit:contain; display:block; border-radius:12px;">
            <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center; background:#F8FAFF; border-radius:12px;">
              <div style="font-size:26px;">{{ $med->emoji ?? '💊' }}</div>
            </div>
          @else
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#F8FAFF; border-radius:12px;">
              <div style="font-size:26px;">{{ $med->emoji ?? '💊' }}</div>
            </div>
          @endif
          @if($idx < 2)
            <div class="bestseller" style="z-index: 2; top:4px; left:0; font-size:7.5px; border-radius:0 4px 4px 0; padding:1px 4px;">Bestseller ✦</div>
          @endif
        </a>

        <div style="flex:1; padding: 0 12px; display:flex; flex-direction:column; justify-content:center; overflow:hidden; gap:2px;">
          <a href="{{ $detailUrl }}" style="text-decoration:none; display:block; color:inherit;">
            <div style="font-weight:800; font-size:13.5px; color:#1A1A1A; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $med->name }}</div>
            <div style="font-size:10.5px; color:#718096; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $med->category }}</div>
            <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center; margin-top:2px;">
              <div style="font-size:14px; font-weight:800; color:#1A3C8F; white-space:nowrap;">₹{{ $med->mrp }}</div>
            </div>
          </a>
        </div>

        <div class="cart-controls" data-med-id="{{ $med->id }}" style="flex-shrink:0; display:flex; align-items:center; justify-content:center; align-self:center; margin-left:auto;">
          @if($qty == 0)
            <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0; display:flex; align-items:center;">
              @csrf
              <input type="hidden" name="medicine_id" value="{{ $med->id }}">
              <button type="submit" class="btn-blue" style="font-size:13px; padding:11px 16px; font-weight:800; border-radius:12px; background:#1A3C8F; color:#fff; border:none; cursor:pointer;">+ Add</button>
            </form>
          @else
            <div class="qty-row" style="display:flex; align-items:center; border:1.5px solid #1A3C8F; border-radius:18px; overflow:hidden; width:80px; height:32px; background:#fff;">
              <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1; display:flex; height:100%; align-items:center;">
                @csrf
                <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                <input type="hidden" name="qty" value="{{ $qty - 1 }}">
                <button type="submit" class="qty-btn" style="padding:0; font-size:15px; color:#1A3C8F; width:100%; border:none; background:#fff; cursor:pointer; height:100%; display:flex; align-items:center; justify-content:center;">−</button>
              </form>
              <div class="qty-num" style="padding:0; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:900; color:#1A3C8F; background:#EEF2FF; flex:1; height:100%;">{{ $qty }}</div>
              <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1; display:flex; height:100%; align-items:center;">
                @csrf
                <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                <input type="hidden" name="qty" value="{{ $qty + 1 }}">
                <button type="submit" class="qty-btn" style="padding:0; font-size:15px; color:#1A3C8F; width:100%; border:none; background:#fff; cursor:pointer; height:100%; display:flex; align-items:center; justify-content:center;">+</button>
              </form>
            </div>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  @if($medicines->count() == 0)
    <div style="text-align:center; padding:40px 20px; color:#888;">
      <div style="font-size:40px; margin-bottom:10px;">😔</div>
      <div style="font-weight:700; font-size:16px; margin-bottom:6px;">Medicine nahi mili</div>
      <div style="font-size:13px;">Prescription upload karein — hum dhundh denge</div>
    </div>
  @endif
</div>
