@foreach($masterMedicines as $med)
  @php
    $hasInShop = in_array($med->id, $shopInventoryIds);
    $shopPrice = $hasInShop ? ($med->shop_price ?? $med->mrp) : $med->mrp;
    $shopQty = $hasInShop ? ($med->shop_quantity ?? 50) : 50;
  @endphp
  <div class="qs-card catalogue-row-item" 
       data-name="{{ strtolower($med->name) }}" 
       data-generic="{{ strtolower($med->generic_name) }}" 
       data-company="{{ strtolower($med->company) }}" 
       style="border: {{ $hasInShop ? '2px solid #3B82F6' : '2px solid transparent' }}; display:flex; align-items:center; gap:12px; background:#fff; padding:14px; border-radius:16px; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
    
    <!-- Checkbox Select -->
    <div style="flex-shrink:0; display:flex; align-items:center;">
      <input type="checkbox" name="qs_sel[m{{ $med->id }}][has]" value="true" style="width:22px; height:22px; cursor:pointer;" {{ $hasInShop ? 'checked' : '' }} class="qs-toggle">
    </div>

    <!-- Medicine Icon & Details -->
    <div style="width:80px; height:80px; border-radius:12px; flex-shrink:0; background:#F8FAFF; display:flex; align-items:center; justify-content:center; font-size:32px; overflow:hidden; border:1px solid #E5E7EB;">
      @if(!empty($med->images))
        @php
          $img = $med->images[0];
          $src = (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) ? $img : asset($img);
        @endphp
        <img src="{{ $src }}" style="width:100%; height:100%; object-fit:contain;">
      @else
        {{ $med->emoji }}
      @endif
    </div>
    
    <div style="flex:1; min-width:0;">
      <div style="font-weight:800; font-size:14px; color:#1A1A1A; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
        {{ $med->name }}
        <span style="font-size:10px; background:#EFF6FF; color:#1E40AF; padding:2px 8px; border-radius:12px; font-weight:700;">{{ $med->strength }}</span>
      </div>
      <div style="font-size:11.5px; color:#555; margin-top:2px;">
        <strong>Generic:</strong> {{ $med->generic_name }}
      </div>
      <div style="font-size:11px; color:#888; margin-top:1px;">
        <strong>Mfg:</strong> {{ $med->company }} • MRP ₹{{ $med->mrp }}
      </div>
    </div>

    <!-- Price & Stock Level Inputs -->
    <div style="flex-shrink:0; display:flex; flex-direction:column; gap:6px; align-items:flex-end;">
      <div style="display:flex; align-items:center; gap:4px;">
        <span style="font-size:11px; font-weight:700; color:#555;">₹</span>
        <input type="number" step="0.01" name="qs_sel[m{{ $med->id }}][price]" value="{{ $shopPrice }}" style="width:70px; padding:6px; border:1px solid #E5E7EB; border-radius:8px; font-size:12px; font-weight:800; text-align:center; outline:none;" class="qs-price" placeholder="Price">
      </div>
      <div style="display:flex; align-items:center; gap:4px;">
        <span style="font-size:10px; color:#888;">Qty:</span>
        <input type="number" name="qs_sel[m{{ $med->id }}][qty]" value="{{ $shopQty }}" style="width:70px; padding:6px; border:1px solid #E5E7EB; border-radius:8px; font-size:12px; font-weight:700; text-align:center; outline:none;" placeholder="Stock">
      </div>
    </div>

  </div>
@endforeach
