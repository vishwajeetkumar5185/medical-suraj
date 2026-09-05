@extends('layouts.app')

@section('content')
<div class="screen">
  <!-- Shop Header -->
  <div class="hdr-gradient" id="inv-hdr-gradient" style="padding:24px 20px 24px; position:relative; overflow:hidden; flex-shrink:0; border-radius: 20px; margin-bottom:20px; transition: all 0.3s ease-out; max-height:200px; opacity:1;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; position:relative; z-index:1;">
      <a href="{{ url('/shop/dashboard') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <div style="flex:1;">
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">{{ $shop->name }}</h2>
        <p style="color:rgba(255,255,255,0.75); font-size:12px; margin:0;">Inventory stock management</p>
      </div>
    </div>
  </div>

  <!-- Dashboard Navigation Menu Bar -->
  <div id="inv-navigation-menu" style="display:flex; background:#fff; padding:10px 10px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:20px; transition: all 0.3s ease-out; max-height:100px; opacity:1;">
    <a href="{{ url('/shop/dashboard') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📊</span>Overview
    </a>
    <a href="{{ url('/shop/quicksetup') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">⚡</span>Quick Setup
    </a>
    <a href="{{ url('/shop/inventory') }}" class="dash-tab active" style="background:#1A3C8F; color:#fff; flex:1; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
      <span style="font-size:16px;">📦</span>Inventory
    </a>
    <a href="{{ url('/shop/orders') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📋</span>Orders
    </a>
    <a href="{{ url('/shop/settings') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">⚙️</span>Settings
    </a>
  </div>

  <div class="scroll" style="flex:1;">
    
    <!-- Action buttons & toggling manual/bulk add form -->
    <div id="inv-action-buttons" style="display:flex; gap:10px; margin-bottom:14px; transition: all 0.3s ease-out; max-height:100px; opacity:1; overflow:hidden;">
      <a href="{{ url('/shop/inventory?showAddForm=1') }}" class="btn-blue" style="flex:1; text-decoration:none; padding:14px; font-size:13px; font-weight:800; display:block; text-align:center;">➕ Manual Add</a>
      <a href="{{ url('/shop/inventory?showBulkUpload=1') }}" class="btn-outline" style="flex:1; text-decoration:none; padding:14px; font-size:13px; font-weight:800; display:block; text-align:center; background:#EFF6FF; border-color:#93C5FD; color:#1E40AF;">📤 Bulk Excel Upload</a>
    </div>

    <!-- Bulk Excel/CSV Upload Form -->
    @if(request('showBulkUpload'))
      <div style="background:#fff; border-radius:18px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.1); border:2px solid #60A5FA; margin-bottom:14px; max-width:520px; margin-left:auto; margin-right:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <h3 style="font-weight:900; font-size:15px; color:#1E3A8A; margin:0;">📊 Bulk Excel / CSV Upload</h3>
          <a href="{{ url('/shop/inventory/sample-template') }}" style="font-size:11px; font-weight:800; color:#2563EB; text-decoration:none; background:#DBEAFE; padding:4px 8px; border-radius:6px;">📥 Sample CSV</a>
        </div>

        <p style="font-size:11.5px; color:#4B5563; margin-bottom:12px; line-height:1.4;">
          Upload an Excel (<code>.xlsx</code>, <code>.xls</code>) or CSV (<code>.csv</code>) file with columns:<br>
          <strong>Name</strong>, <strong>Selling Price</strong>, <strong>Image</strong>, <strong>Company</strong>, <strong>Composition</strong> (Salt/Formula), <strong>Quantity</strong> (Optional).<br>
          <span style="color:#059669; font-weight:700;">✓ Existing medicine: Updates price, photo, company, composition & stock for your store.<br>
          ✓ New medicine: Creates medicine record & adds to inventory.</span>
        </p>
        
        <form action="{{ url('/shop/inventory/bulk-upload') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div style="margin-bottom:14px;">
            <label class="form-label" style="font-weight:800; font-size:12px;">Select Excel or CSV File *</label>
            <input type="file" name="excel_file" class="form-input" accept=".xlsx, .xls, .csv, .txt" required style="padding:10px;">
          </div>

          <div style="display:flex; gap:10px;">
            <a href="{{ url('/shop/inventory') }}" class="btn-outline" style="flex:1; text-decoration:none; text-align:center; padding:11px; font-size:13px; color:#555; border-color:#E5E7EB;">Cancel</a>
            <button type="submit" class="btn-blue" style="flex:1; padding:11px; font-size:13px; background:linear-gradient(135deg,#2563EB,#1D4ED8);">🚀 Upload & Process</button>
          </div>
        </form>
      </div>
    @endif

    <!-- Manual Add Form -->
    @if(request('showAddForm'))
      <div style="background:#fff; border-radius:18px; padding:16px; box-shadow:0 4px 20px rgba(0,0,0,0.1); border:2px solid #BFDBFE; margin-bottom:14px; max-width:500px; margin-left:auto; margin-right:auto;">
        <h3 style="font-weight:800; font-size:14px; color:#1A1A1A; margin-bottom:12px;">➕ Add Medicine Manually</h3>
        
        <form action="{{ url('/shop/inventory/add') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="shop_id" value="{{ $shop->id }}">

          <div style="margin-bottom:10px; position:relative;">
            <input type="hidden" name="medicine_id" id="selected-medicine-id">
            <input type="text" name="name" id="med-search-input" class="form-input" placeholder="Search medicine (e.g. Paracetamol) *" oninput="debouncedSearchMedicine(this.value)" autocomplete="off" required>
            <div id="med-search-results" style="display:none; position:absolute; left:0; right:0; top:100%; background:#fff; border:1px solid #E5E7EB; border-radius:12px; max-height:200px; overflow-y:auto; z-index:1000; box-shadow:0 10px 20px rgba(0,0,0,0.1); margin-top:4px;"></div>
          </div>
          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <input type="number" step="0.01" name="price" class="form-input" style="flex:1;" placeholder="Price ₹" required>
            <input type="number" name="qty" class="form-input" style="flex:1;" placeholder="Quantity" value="50" required>
          </div>
          <div style="margin-bottom:12px;">
            <label class="form-label">Upload Medicine Images</label>
            <input type="file" name="images[]" multiple class="form-input" accept="image/*">
          </div>
          <div style="margin-bottom:12px;">
            <label class="form-label">Or Image Links (multiple links pipe "|" separated)</label>
            <textarea name="image_links" class="form-input" style="height:70px; resize:vertical; font-size:12.5px;" placeholder="https://example.com/img1.jpg|https://example.com/img2.jpg"></textarea>
          </div>
          <div style="display:flex; gap:10px;">
            <a href="{{ url('/shop/inventory') }}" class="btn-outline" style="flex:1; text-decoration:none; text-align:center; padding:11px; font-size:13px; color:#555; border-color:#E5E7EB;">Cancel</a>
            <button type="submit" class="btn-blue" style="flex:1; padding:11px; font-size:13px;">✅ Add Medicine</button>
          </div>
        </form>
      </div>
    @endif

    <!-- Search & Brand Filter Layout -->
    <div style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); display:flex; flex-direction:column; gap:16px; margin-bottom:16px;">
      <div style="display:flex; flex-direction:column; align-items:stretch; width:100%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
          <label class="form-label" style="font-size:13.5px; font-weight:800; color:#1A3C8F; margin:0;">🔍 search to inventory medicine</label>
          <button type="button" id="inv-cancel-search-btn" onclick="clearInvSearch()" style="display:none; background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; border-radius:8px; padding:3px 10px; font-size:11px; font-weight:800; cursor:pointer;">✕ Cancel Search</button>
        </div>
        <div style="display:flex; gap:10px; width:100%;">
          <input type="text" id="inventory-search" class="form-input" style="padding:15px 16px; font-size:15px; border-radius:14px; flex:1; box-sizing:border-box;" placeholder="Type to search (e.g. Paracetamol)...">
        </div>
      </div>
      
      <div id="inv-company-filter-block" style="display:flex; flex-direction:column; align-items:stretch; width:100%; transition: all 0.3s ease-out; max-height:100px; opacity:1; overflow:hidden;">
        <label class="form-label" style="margin-bottom:6px; font-size:13.5px; font-weight:800; color:#1A3C8F; display:block;">🏭 Filter by Company / Brand</label>
        <select id="inventory-company-filter" class="form-input" style="padding:15px 16px; font-size:15px; border-radius:14px; height:auto; width:100%; box-sizing:border-box;">
          <option value="All">All Companies</option>
          <option value="Cipla Ltd">Cipla Ltd</option>
          <option value="Abbott India">Abbott India</option>
          <option value="Sun Pharma">Sun Pharma</option>
          <option value="Alkem Laboratories">Alkem Laboratories</option>
          <option value="Mankind Pharma">Mankind Pharma</option>
          <option value="Lupin Ltd">Lupin Ltd</option>
        </select>
      </div>
    </div>

    <!-- Category Pills Filter -->
    @php
      $cats = ['All', 'Tablet', 'Liquid', 'Powder', 'Injection', 'Ointment/Cream'];
    @endphp
    <div style="display:flex; gap:8px; margin-bottom:14px; overflow-x:auto; padding-bottom:4px;">
      @foreach($cats as $c)
        <button type="button" onclick="selectInventoryForm('{{ $c }}')" id="pill-{{ Str::slug($c) }}" class="inventory-form-pill" style="flex-shrink:0; border:none; padding:7px 16px; border-radius:20px; background:#F3F4F6; color:#555; font-weight:700; font-size:12px; cursor:pointer; outline:none;">
          {{ $c }}
        </button>
      @endforeach
    </div>

    <!-- Inventory Stock Table/Grid -->
    @if($inventory->isEmpty())
      <div style="text-align:center; padding:40px 20px; color:#888;">
        <div style="font-size:40px; margin-bottom:10px;">📦</div>
        <div style="font-weight:700; font-size:15px;">Koi medicine nahi hai</div>
        <div style="font-size:12px; margin-top:4px;">Quick Setup se medicines tick karein ya manually add karein.</div>
      </div>
    @else
      <div style="display:flex; flex-direction:column; gap:12px;">
        @foreach($inventory as $item)
          <div class="inventory-item" 
               data-name="{{ strtolower($item->medicine ? $item->medicine->name : $item->name) }}" 
               data-generic="{{ strtolower($item->medicine ? $item->medicine->generic_name : '') }}" 
               data-company="{{ strtolower($item->medicine ? $item->medicine->company : '') }}"
               data-form="{{ strtolower($item->medicine ? $item->medicine->product_form : '') }}"
               style="background:#fff; border-radius:16px; padding:12px; display:flex; gap:12px; align-items:center; box-shadow:0 2px 12px rgba(0,0,0,0.05); margin:0;">
            <!-- Image thumbnail box with Camera overlay -->
            <div style="width:80px; height:80px; border-radius:12px; background:#F8FAFF; display:flex; align-items:center; justify-content:center; font-size:32px; flex-shrink:0; overflow:hidden; position:relative; border:1px solid #E5E7EB; cursor:pointer;" onclick="document.getElementById('file-input-{{ $item->id }}').click()" title="📷 Click to add photo via Camera or Gallery">
              @php
                $itemImg = !empty($item->images) ? $item->images[0] : ($item->medicine && !empty($item->medicine->images) ? $item->medicine->images[0] : null);
                $itemImgUrl = $itemImg ? ((strpos($itemImg, 'http://') === 0 || strpos($itemImg, 'https://') === 0) ? $itemImg : asset($itemImg)) : null;
              @endphp
              @if($itemImgUrl)
                <img src="{{ $itemImgUrl }}" referrerpolicy="no-referrer" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" style="width:100%; height:100%; object-fit:contain;">
                <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center; background:#F8FAFF; font-size:32px;">{{ $item->medicine ? ($item->medicine->emoji ?? '💊') : '💊' }}</div>
              @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#F8FAFF; font-size:32px;">{{ $item->medicine ? ($item->medicine->emoji ?? '💊') : '💊' }}</div>
              @endif
              <div style="position:absolute; bottom:2px; right:2px; background:rgba(30,58,138,0.85); color:#fff; font-size:10px; padding:2px 5px; border-radius:6px; font-weight:800; backdrop-filter:blur(4px);">📷 +</div>
            </div>

            <!-- Hidden Form for Camera/Gallery Photo Upload -->
            <form action="{{ url('/shop/inventory/upload-image/'.$item->id) }}" method="POST" enctype="multipart/form-data" id="form-upload-{{ $item->id }}" style="display:none;">
              @csrf
              <input type="file" name="extra_image" id="file-input-{{ $item->id }}" accept="image/*" onchange="this.form.submit()">
            </form>

            <div style="flex:1;">
              <div style="font-weight:800; font-size:14px; color:#1A1A1A;">
                {{ $item->medicine ? $item->medicine->name : $item->name }}
              </div>
              <div style="display:flex; gap:8px; margin-top:2px; align-items:center; flex-wrap:wrap;">
                <div style="font-size:12px; color:#1A3C8F; font-weight:700;">₹{{ $item->price }}</div>
                <div style="font-size:12px; color:#888;">Stock: {{ $item->quantity }}</div>
              </div>
              @php
                $imgList = !empty($item->images) ? $item->images : ($item->medicine && !empty($item->medicine->images) ? $item->medicine->images : []);
              @endphp
              @if(count($imgList) > 1)
                <div style="display:flex; gap:4px; margin-top:6px; flex-wrap:wrap;">
                  @foreach($imgList as $img)
                    @php
                      $subImgUrl = (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) ? $img : asset($img);
                    @endphp
                    <img src="{{ $subImgUrl }}" referrerpolicy="no-referrer" style="width:26px; height:26px; border-radius:4px; object-fit:cover; border:1px solid #ddd;">
                  @endforeach
                </div>
              @endif
            </div>

            <div>
              <form action="{{ url('/shop/inventory/delete/'.$item->id) }}" method="POST" onsubmit="return confirm('Dawai stock se hatayein?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:#FEF2F2; border:none; border-radius:10px; width:34px; height:34px; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center;" title="Delete stock item">
                  🗑️
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
      <div style="margin-top:20px; padding:0 10px;">
        {{ $inventory->links() }}
      </div>
    @endif

  </div>
</div>

<script>
let searchTimeout;
function debouncedSearchMedicine(query) {
  clearTimeout(searchTimeout);
  if (!query || query.length < 2) {
    document.getElementById('med-search-results').style.display = 'none';
    return;
  }
  searchTimeout = setTimeout(() => {
    fetch(`{{ url('/shop/medicines/search') }}?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        const resultsDiv = document.getElementById('med-search-results');
        resultsDiv.innerHTML = '';
        if (data.length === 0) {
          resultsDiv.style.display = 'none';
          return;
        }
        resultsDiv.style.display = 'block';
        data.forEach(item => {
          const div = document.createElement('div');
          div.style.padding = '10px 12px';
          div.style.cursor = 'pointer';
          div.style.borderBottom = '1px solid #F3F4F6';
          div.style.fontSize = '13px';
          div.style.color = '#374151';
          div.style.fontWeight = 'bold';
          div.innerHTML = `<span style="font-size:16px; margin-right:6px;">${item.emoji || '💊'}</span> ${item.name} <span style="font-size:11px; color:#888; margin-left:8px; font-weight:normal;">(${item.category})</span>`;
          div.addEventListener('click', () => {
            selectSearchedMedicine(item);
          });
          resultsDiv.appendChild(div);
        });
      })
      .catch(err => console.error(err));
  }, 300);
}

function selectSearchedMedicine(item) {
  document.getElementById('selected-medicine-id').value = item.id;
  document.getElementById('med-search-input').value = item.name;
  
  const priceInput = document.querySelector('input[name="price"]');
  if (priceInput && item.price) {
    priceInput.value = item.price;
  }

  document.getElementById('med-search-results').style.display = 'none';
}

// Close search dropdown on click outside
document.addEventListener('click', function(e) {
  if (e.target.id !== 'med-search-input') {
    const results = document.getElementById('med-search-results');
    if (results) results.style.display = 'none';
  }
});

// Bind slide-up animation when typing in search box
function shrinkInvHeader() {
  const hdr = document.getElementById('inv-hdr-gradient');
  const menu = document.getElementById('inv-navigation-menu');
  const actions = document.getElementById('inv-action-buttons');
  const companyBlock = document.getElementById('inv-company-filter-block');
  const cancelBtn = document.getElementById('inv-cancel-search-btn');
  const globalNavbar = document.querySelector('.navbar-wrapper');

  if (globalNavbar) {
    globalNavbar.style.display = 'none';
  }

  if (hdr) {
    hdr.style.maxHeight = '0';
    hdr.style.opacity = '0';
    hdr.style.marginBottom = '0';
    hdr.style.paddingTop = '0';
    hdr.style.paddingBottom = '0';
  }
  if (menu) {
    menu.style.maxHeight = '0';
    menu.style.opacity = '0';
    menu.style.marginBottom = '0';
    menu.style.paddingTop = '0';
    menu.style.paddingBottom = '0';
  }
  if (actions) {
    actions.style.maxHeight = '0';
    actions.style.opacity = '0';
    actions.style.marginBottom = '0';
    actions.style.paddingTop = '0';
    actions.style.paddingBottom = '0';
  }
  if (companyBlock) {
    companyBlock.style.maxHeight = '0';
    companyBlock.style.opacity = '0';
    companyBlock.style.marginTop = '0';
    companyBlock.style.padding = '0';
  }
  if (cancelBtn) {
    cancelBtn.style.display = 'inline-block';
  }
}

function restoreInvHeader() {
  const hdr = document.getElementById('inv-hdr-gradient');
  const menu = document.getElementById('inv-navigation-menu');
  const actions = document.getElementById('inv-action-buttons');
  const companyBlock = document.getElementById('inv-company-filter-block');
  const cancelBtn = document.getElementById('inv-cancel-search-btn');
  const globalNavbar = document.querySelector('.navbar-wrapper');

  if (globalNavbar) {
    globalNavbar.style.display = 'block';
  }

  if (hdr) {
    hdr.style.maxHeight = '200px';
    hdr.style.opacity = '1';
    hdr.style.marginBottom = '20px';
    hdr.style.paddingTop = '24px';
    hdr.style.paddingBottom = '24px';
  }
  if (menu) {
    menu.style.maxHeight = '100px';
    menu.style.opacity = '1';
    menu.style.marginBottom = '20px';
    menu.style.paddingTop = '10px';
    menu.style.paddingBottom = '10px';
  }
  if (actions) {
    actions.style.maxHeight = '100px';
    actions.style.opacity = '1';
    actions.style.marginBottom = '14px';
    actions.style.paddingTop = '0';
    actions.style.paddingBottom = '0';
  }
  if (companyBlock) {
    companyBlock.style.maxHeight = '100px';
    companyBlock.style.opacity = '1';
    companyBlock.style.marginTop = '0';
  }
  if (cancelBtn) {
    cancelBtn.style.display = 'none';
  }
}

function clearInvSearch() {
  const searchInput = document.getElementById('inventory-search');
  if (searchInput) {
    searchInput.value = '';
  }
  restoreInvHeader();
  filterInventoryList();
}

// Client-side instant filter inventory list
let activeForm = 'All';

function selectInventoryForm(formVal) {
  activeForm = formVal;
  
  document.querySelectorAll('.inventory-form-pill').forEach(pill => {
    pill.style.background = '#F3F4F6';
    pill.style.color = '#555';
  });
  const selectedPill = document.getElementById('pill-' + formVal.toLowerCase().replace('/', '-'));
  if (selectedPill) {
    selectedPill.style.background = '#1A3C8F';
    selectedPill.style.color = '#fff';
  }

  filterInventoryList();
}

function filterInventoryList() {
  const searchVal = document.getElementById('inventory-search').value.toLowerCase().trim();
  const companyVal = document.getElementById('inventory-company-filter').value.toLowerCase().trim();

  if (searchVal.length > 0) {
    shrinkInvHeader();
  } else {
    restoreInvHeader();
  }

  document.querySelectorAll('.inventory-item').forEach(row => {
    const name = row.getAttribute('data-name');
    const generic = row.getAttribute('data-generic');
    const company = row.getAttribute('data-company');
    const pForm = row.getAttribute('data-form');

    // 1. Matches Search query
    const matchesSearch = !searchVal || 
                          name.includes(searchVal) || 
                          generic.includes(searchVal) || 
                          company.includes(searchVal);

    // 2. Matches Company filter
    const matchesCompany = companyVal === 'all' || company === companyVal;

    // 3. Matches Form pill filter
    let matchesForm = true;
    if (activeForm !== 'All') {
      const formLower = pForm.toLowerCase();
      if (activeForm === 'Tablet') {
        matchesForm = formLower.includes('tablet') || formLower.includes('capsule');
      } else if (activeForm === 'Liquid') {
        matchesForm = formLower.includes('liquid') || formLower.includes('suspension') || formLower.includes('syrup') || formLower.includes('solution') || formLower.includes('drop');
      } else if (activeForm === 'Powder') {
        matchesForm = formLower.includes('powder') || formLower.includes('sachet') || formLower.includes('granule');
      } else if (activeForm === 'Injection') {
        matchesForm = formLower.includes('injection') || formLower.includes('vial') || formLower.includes('ampoule') || formLower.includes('prefilled pen');
      } else if (activeForm === 'Ointment/Cream') {
        matchesForm = formLower.includes('ointment') || formLower.includes('cream') || formLower.includes('gel') || formLower.includes('lotion');
      } else {
        matchesForm = formLower.includes(activeForm.toLowerCase());
      }
    }

    if (matchesSearch && matchesCompany && matchesForm) {
      row.style.display = 'flex';
    } else {
      row.style.display = 'none';
    }
  });
}

// Bind keypress and input events
const searchInput = document.getElementById('inventory-search');
if (searchInput) {
  searchInput.addEventListener('input', filterInventoryList);
}
const companySelect = document.getElementById('inventory-company-filter');
if (companySelect) {
  companySelect.addEventListener('change', filterInventoryList);
}

// Run once to initialize active pill style
selectInventoryForm('All');
</script>
@endsection
