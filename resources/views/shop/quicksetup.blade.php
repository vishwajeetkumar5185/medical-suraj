@extends('layouts.app')

@section('content')
<div class="screen">
  <!-- Shop Header -->
  <div class="hdr-gradient" id="qs-hdr-gradient" style="padding:24px 20px 24px; position:relative; overflow:hidden; flex-shrink:0; border-radius: 20px; margin-bottom:20px; transition: all 0.3s ease-out; max-height:200px; opacity:1;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; position:relative; z-index:1;">
      <a href="{{ url('/shop/dashboard') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <div style="flex:1;">
        <h2 style="color:#fff; font-weight:900; font-size:17px; margin:0;">{{ $shop->name }}</h2>
        <p style="color:rgba(255,255,255,0.75); font-size:12px; margin:0;">Quick Setup Inventory Setup</p>
      </div>
    </div>
  </div>

  <!-- Dashboard Navigation Menu Bar -->
  <div id="qs-navigation-menu" style="display:flex; background:#fff; padding:10px 10px; gap:6px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; flex-shrink:0; border-radius:14px; margin-bottom:20px; transition: all 0.3s ease-out; max-height:100px; opacity:1;">
    <a href="{{ url('/shop/dashboard') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📊</span>Overview
    </a>
    <a href="{{ url('/shop/quicksetup') }}" class="dash-tab active" style="background:#1A3C8F; color:#fff; flex:1; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
      <span style="font-size:16px;">⚡</span>Quick Setup
    </a>
    <a href="{{ url('/shop/inventory') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📦</span>Inventory
    </a>
    <a href="{{ url('/shop/orders') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">📋</span>Orders
    </a>
    <a href="{{ url('/shop/settings') }}" class="dash-tab" style="background:#F3F4F6; color:#888; flex:1;">
      <span style="font-size:16px;">⚙️</span>Settings
    </a>
  </div>

  <div class="scroll" id="quicksetup-scroll-container" style="flex:1;">
    <div id="qs-selection-banner" style="background:linear-gradient(135deg,#1A3C8F,#2563EB); border-radius:12px; padding:10px 14px; margin-bottom:12px; color:#fff; display:flex; align-items:center; gap:8px; transition: all 0.3s ease-out; max-height:100px; opacity:1; overflow:hidden;">
      <span style="font-size:18px;">🏪</span>
      <div>
        <div style="font-weight:900; font-size:13px;">Medicine Catalogue Selection</div>
        <div style="font-size:10.5px; opacity:0.85; margin-top:2px;">Select medicines, set price/stock, and save.</div>
      </div>
    </div>

    <!-- Search & Brand Filter Layout -->
    <form method="GET" action="{{ url('/shop/quicksetup') }}" id="search-filter-form" style="margin-bottom:16px; transition: all 0.3s ease;" onsubmit="event.preventDefault();">
      <input type="hidden" name="category" value="{{ $category }}">
      
      <div style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); display:flex; flex-direction:column; gap:16px;">
        <div style="display:flex; flex-direction:column; align-items:stretch; width:100%;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-size:13.5px; font-weight:800; color:#1A3C8F; margin:0;">🔍 search to add medicine inventory</label>
            <button type="button" id="qs-cancel-search-btn" onclick="clearQSSearch()" style="display:none; background:#FEE2E2; color:#DC2626; border:1px solid #FCA5A5; border-radius:8px; padding:3px 10px; font-size:11px; font-weight:800; cursor:pointer;">✕ Cancel Search</button>
          </div>
          <div style="display:flex; gap:10px; width:100%;">
            <input type="text" id="catalogue-search" name="q" value="{{ $search }}" class="form-input" style="padding:15px 16px; font-size:15px; border-radius:14px; flex:1; box-sizing:border-box;" placeholder="Type to search (e.g. Paracetamol)...">
            <button type="button" onclick="filterCatalogueList()" class="btn-blue" style="border-radius:14px; padding:15px 24px; font-weight:900; font-size:14px; border:none; cursor:pointer; color:#fff;">Search</button>
          </div>
        </div>
        
        <div id="qs-company-filter-block" style="display:flex; flex-direction:column; align-items:stretch; width:100%; transition: all 0.3s ease-out; max-height:100px; opacity:1; overflow:hidden;">
          <label class="form-label" style="margin-bottom:6px; font-size:13.5px; font-weight:800; color:#1A3C8F; display:block;">🏭 Filter by Company / Brand</label>
          <select id="company-filter" name="company" class="form-input" style="padding:15px 16px; font-size:15px; border-radius:14px; height:auto; width:100%; box-sizing:border-box;" onchange="filterCatalogueList()">
            <option value="All" {{ $company === 'All' ? 'selected' : '' }}>All Companies</option>
            @foreach($allCompanies as $comp)
              <option value="{{ $comp }}" {{ $company === $comp ? 'selected' : '' }}>{{ $comp }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </form>

    <!-- Category Pills Filter -->
    @php
      $cats = ['All', 'Tablet', 'Liquid', 'Powder', 'Injection', 'Ointment/Cream'];
    @endphp
    <div style="display:flex; gap:8px; margin-bottom:14px; overflow-x:auto; padding-bottom:4px;" id="qs-category-pills">
      @foreach($cats as $c)
        <button type="button" onclick="selectQSCategory('{{ $c }}', this)" class="qs-cat-pill" style="flex-shrink:0; padding:7px 16px; border-radius:20px; background:{{ $category === $c ? '#1A3C8F' : '#F3F4F6' }}; color:{{ $category === $c ? '#fff' : '#555' }}; font-weight:700; font-size:12px; border:none; cursor:pointer; outline:none; transition: all 0.2s;">
          {{ $c }}
        </button>
      @endforeach
    </div>
    <script>
      function selectQSCategory(cat, btn) {
        document.querySelector('input[name="category"]').value = cat;
        document.querySelectorAll('.qs-cat-pill').forEach(b => {
          b.style.background = '#F3F4F6';
          b.style.color = '#555';
        });
        btn.style.background = '#1A3C8F';
        btn.style.color = '#fff';
        
        filterCatalogueList();
      }
    </script>

    <!-- Form for Quick Setup inventory list -->
    <form action="{{ url('/shop/quicksetup') }}" method="POST" id="qs-save-form" onsubmit="syncSelectedStateAndHiddenInputs()">
      @csrf
      <input type="hidden" name="shop_id" value="{{ $shop->id }}">
      
      <!-- Container for persistent hidden inputs for checked items across searches -->
      <div id="persistent-selected-inputs" style="display:none;"></div>

      <div class="responsive-grid" id="catalogue-grid" style="display:flex; flex-direction:column; gap:12px;">
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
      </div>

      <!-- Infinite scroll loading wrapper -->
      <div id="quicksetup-scroll-loading" style="display:none; text-align:center; padding:15px; font-size:12px; color:#888; width:100%;">
        📦 Loading more medicines...
      </div>

      <!-- Extra spacer so bottom content isn't covered by the sticky bar -->
      <div style="height:100px;"></div>

      <!-- Floating Sticky Save Bar -->
      <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: calc(100% - 32px); max-width: 500px; z-index: 9999; margin: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 12px 16px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); border: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; box-sizing: border-box;">
        <div style="font-size: 13px; color: #4B5563; font-weight: 800;">
          Selected: <span style="color: #1A3C8F; font-size: 16px; font-weight: 900;" id="selected-qty-badge">0</span> items
        </div>
        <button type="submit" class="btn-blue" style="border-radius: 12px; padding: 12px 24px; font-weight: 900; font-size: 13.5px; border: none; cursor: pointer; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.2); margin:0;">
          💾 Save Selected
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Persistent state store for selected items across all searches & filters
  let selectedItemsState = {};

  function syncSelectedStateAndHiddenInputs() {
    const container = document.getElementById('persistent-selected-inputs');
    if (!container) return;

    let html = '';
    let count = 0;

    for (const [medKey, item] of Object.entries(selectedItemsState)) {
      if (item && item.has) {
        count++;
        // Generate hidden inputs for items selected across previous searches
        html += `<input type="hidden" name="qs_sel[${medKey}][has]" value="true">`;
        html += `<input type="hidden" name="qs_sel[${medKey}][price]" value="${item.price}">`;
        html += `<input type="hidden" name="qs_sel[${medKey}][qty]" value="${item.qty}">`;
      }
    }

    container.innerHTML = html;

    const badge = document.getElementById('selected-qty-badge');
    if (badge) {
      badge.textContent = count;
    }
  }

  function restoreCardStatesFromSelectedState(parent) {
    (parent || document).querySelectorAll('.qs-card').forEach(card => {
      const checkbox = card.querySelector('.qs-toggle');
      if (!checkbox) return;

      const nameAttr = checkbox.name;
      const match = nameAttr.match(/m\d+/);
      if (!match) return;
      const medKey = match[0];

      const priceInput = card.querySelector('.qs-price');
      const qtyInput = card.querySelector('input[name*="[qty]"]');

      if (selectedItemsState[medKey] && selectedItemsState[medKey].has) {
        checkbox.checked = true;
        card.style.borderColor = '#3B82F6';
        if (priceInput && selectedItemsState[medKey].price !== undefined) {
          priceInput.value = selectedItemsState[medKey].price;
        }
        if (qtyInput && selectedItemsState[medKey].qty !== undefined) {
          qtyInput.value = selectedItemsState[medKey].qty;
        }
      } else if (selectedItemsState[medKey] === undefined) {
        // Initial load check
        if (checkbox.checked) {
          selectedItemsState[medKey] = {
            has: true,
            price: priceInput ? priceInput.value : '0',
            qty: qtyInput ? qtyInput.value : '50'
          };
          card.style.borderColor = '#3B82F6';
        } else {
          card.style.borderColor = 'transparent';
        }
      } else {
        checkbox.checked = false;
        card.style.borderColor = 'transparent';
      }
    });

    syncSelectedStateAndHiddenInputs();
  }

  function bindCardListeners(parent) {
    (parent || document).querySelectorAll('.qs-card').forEach(card => {
      const checkbox = card.querySelector('.qs-toggle');
      if (!checkbox) return;

      const nameAttr = checkbox.name;
      const match = nameAttr.match(/m\d+/);
      if (!match) return;
      const medKey = match[0];

      const priceInput = card.querySelector('.qs-price');
      const qtyInput = card.querySelector('input[name*="[qty]"]');

      if (!checkbox.dataset.hasListener) {
        checkbox.dataset.hasListener = "true";

        checkbox.addEventListener('change', function() {
          if (this.checked) {
            card.style.borderColor = '#3B82F6';
            selectedItemsState[medKey] = {
              has: true,
              price: priceInput ? priceInput.value : '0',
              qty: qtyInput ? qtyInput.value : '50'
            };
          } else {
            card.style.borderColor = 'transparent';
            delete selectedItemsState[medKey];
          }
          syncSelectedStateAndHiddenInputs();
        });
      }

      if (priceInput && !priceInput.dataset.hasListener) {
        priceInput.dataset.hasListener = "true";
        priceInput.addEventListener('input', function() {
          if (checkbox.checked && selectedItemsState[medKey]) {
            selectedItemsState[medKey].price = this.value;
            syncSelectedStateAndHiddenInputs();
          }
        });
      }

      if (qtyInput && !qtyInput.dataset.hasListener) {
        qtyInput.dataset.hasListener = "true";
        qtyInput.addEventListener('input', function() {
          if (checkbox.checked && selectedItemsState[medKey]) {
            selectedItemsState[medKey].qty = this.value;
            syncSelectedStateAndHiddenInputs();
          }
        });
      }
    });

    restoreCardStatesFromSelectedState(parent);
  }

  // Run on initial load
  window.addEventListener('DOMContentLoaded', () => {
    bindCardListeners(document);
  });

  // Infinite Scroll logic for Quick Setup
  let currentPage = 1;
  let isPageLoading = false;
  let hasMorePages = {{ $masterMedicines->hasMorePages() ? 'true' : 'false' }};
  
  const scrollContainer = document.getElementById('quicksetup-scroll-container');
  const catalogueGrid = document.getElementById('catalogue-grid');
  const loadingIndicator = document.getElementById('quicksetup-scroll-loading');

  function handleScroll() {
    if (isPageLoading || !hasMorePages) return;
    
    const threshold = 250;
    const windowScrollPosition = document.documentElement.offsetHeight - window.innerHeight - window.scrollY;
    const containerScrollPosition = scrollContainer.scrollHeight - scrollContainer.scrollTop - scrollContainer.clientHeight;
    
    if (windowScrollPosition < threshold || containerScrollPosition < threshold) {
      loadNextBundle();
    }
  }

  window.addEventListener('scroll', handleScroll);
  scrollContainer.addEventListener('scroll', handleScroll);

  function loadNextBundle() {
    isPageLoading = true;
    currentPage++;
    loadingIndicator.style.display = 'block';

    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', currentPage);
    const url = `{{ url('/shop/quicksetup') }}?${urlParams.toString()}`;
    
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.text())
      .then(html => {
        loadingIndicator.style.display = 'none';
        
        if (html && html.trim().length > 0) {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          
          const cards = temp.querySelectorAll('.catalogue-row-item');
          if (cards.length > 0) {
            cards.forEach(card => {
              catalogueGrid.appendChild(card);
            });
            bindCardListeners(catalogueGrid);
          } else {
            hasMorePages = false;
          }
        } else {
          hasMorePages = false;
        }
        isPageLoading = false;
      })
      .catch(err => {
        console.error(err);
        loadingIndicator.style.display = 'none';
        isPageLoading = false;
      });
  }

  // Bind slide-up animation when typing in search box
  function shrinkQSHeader() {
    const hdr = document.getElementById('qs-hdr-gradient');
    const menu = document.getElementById('qs-navigation-menu');
    const banner = document.getElementById('qs-selection-banner');
    const companyBlock = document.getElementById('qs-company-filter-block');
    const cancelBtn = document.getElementById('qs-cancel-search-btn');
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
    if (banner) {
      banner.style.maxHeight = '0';
      banner.style.opacity = '0';
      banner.style.marginBottom = '0';
      banner.style.paddingTop = '0';
      banner.style.paddingBottom = '0';
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

  function restoreQSHeader() {
    const hdr = document.getElementById('qs-hdr-gradient');
    const menu = document.getElementById('qs-navigation-menu');
    const banner = document.getElementById('qs-selection-banner');
    const companyBlock = document.getElementById('qs-company-filter-block');
    const cancelBtn = document.getElementById('qs-cancel-search-btn');
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
    if (banner) {
      banner.style.maxHeight = '100px';
      banner.style.opacity = '1';
      banner.style.marginBottom = '12px';
      banner.style.paddingTop = '10px';
      banner.style.paddingBottom = '10px';
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

  function clearQSSearch() {
    const searchInput = document.getElementById('catalogue-search');
    if (searchInput) {
      searchInput.value = '';
    }
    restoreQSHeader();
    filterCatalogueList();
  }

  let searchTimeoutQS;
  // Client-side instant filter catalogue list as user types
  function filterCatalogueList() {
    const searchVal = document.getElementById('catalogue-search').value.toLowerCase().trim();
    const companyVal = document.getElementById('company-filter').value.toLowerCase().trim();

    if (searchVal.length > 0) {
      shrinkQSHeader();
    } else {
      restoreQSHeader();
    }

    // Trigger instant client-side show/hide matching for existing batch rows
    document.querySelectorAll('.catalogue-row-item').forEach(row => {
      const name = row.getAttribute('data-name') || '';
      const generic = row.getAttribute('data-generic') || '';
      const company = row.getAttribute('data-company') || '';

      let matchesSearch = true;
      if (searchVal) {
        const wordsName = name.split(/\s+/);
        const wordsGeneric = generic.split(/\s+/);
        const wordsCompany = company.split(/\s+/);
        
        const nameMatch = wordsName.some(w => w.startsWith(searchVal));
        const genericMatch = wordsGeneric.some(w => w.startsWith(searchVal));
        const companyMatch = wordsCompany.some(w => w.startsWith(searchVal));
        
        matchesSearch = nameMatch || genericMatch || companyMatch;
      }

      const matchesCompany = companyVal === 'all' || company === companyVal;

      if (matchesSearch && matchesCompany) {
        row.style.display = 'flex';
      } else {
        row.style.display = 'none';
      }
    });

    // Background server query
    clearTimeout(searchTimeoutQS);
    if (searchVal.length >= 1) {
      searchTimeoutQS = setTimeout(() => {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('q', searchVal);
        urlParams.set('page', 1);
        
        const activeCategory = document.querySelector('input[name="category"]').value;
        const activeCompany = document.getElementById('company-filter').value;
        urlParams.set('category', activeCategory);
        urlParams.set('company', activeCompany);
        
        const url = `{{ url('/shop/quicksetup') }}?${urlParams.toString()}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(res => res.text())
          .then(html => {
            if (html && html.trim().length > 0) {
              const temp = document.createElement('div');
              temp.innerHTML = html;
              const cards = temp.querySelectorAll('.catalogue-row-item');
              
              const currentGrid = document.getElementById('catalogue-grid');
              currentGrid.innerHTML = '';
              cards.forEach(card => {
                currentGrid.appendChild(card);
              });
              
              bindCardListeners(currentGrid);
            }
          })
          .catch(err => console.error(err));
      }, 300);
    }
  }

  // Bind the client-side search instantly as the user types
  const searchInput = document.getElementById('catalogue-search');
  if (searchInput) {
    searchInput.addEventListener('input', filterCatalogueList);
  }
</script>
@endsection
