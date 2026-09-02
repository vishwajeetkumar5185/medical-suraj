@extends('layouts.app')

@section('seo_title', 'Search Medicines Online - Verify Real-time Pharmacy Stock | Dawalo')
@section('seo_description', 'Search generic or prescription medicines and instantly matching stock levels at nearby local pharmacies for fast home delivery.')
@section('seo_keywords', 'search medicines online, find pharmacy stock, generic formulas search, dawalo medicines, local pharmacy finder')

@section('content')
<div class="screen">
  <!-- === HEADER === -->
  <div class="hdr-gradient" style="padding-bottom: 30px;">
    <div class="hdr-circle"></div>
    <div class="hdr-circle2"></div>

    <div style="margin-bottom:12px; position:relative; z-index:1; display:flex; align-items:center; gap:12px;">
      <a href="{{ url('/') }}" class="nav-btn" style="background:rgba(255,255,255,0.15); border-radius:12px; width:40px; height:40px; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; padding:0;">←</a>
      <h2 style="color:#fff; font-size:22px; font-weight:800;">Search Results</h2>
    </div>

    <!-- Search Form -->
    <form action="{{ url('/search') }}" method="GET" class="search-box" style="position:relative; z-index:1;">
      @if(request('shop_id'))
        <input type="hidden" name="shop_id" value="{{ request('shop_id') }}">
      @endif
      <input name="q" class="search-input" placeholder="Medicine ka naam likhein..." type="text" value="{{ $query }}" id="search-q">
      <button type="submit" class="search-btn">🔍 Search</button>
    </form>

    <!-- Multiple Category Filters -->
    <div style="margin-top: 14px; position: relative; z-index: 1;">
      <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.7); font-weight: 800; margin-bottom: 6px;">Filter Categories:</div>
      <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;" class="hide-scrollbar">
        @foreach($allCategories as $cat)
          @php
            $isChecked = in_array($cat, $selectedCategories);
          @endphp
          <button type="button" onclick="toggleCategoryFilter('{{ $cat }}')" style="flex-shrink: 0; border: none; border-radius: 20px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;
            {{ $isChecked ? 'background:#fff; color:#1A3C8F; box-shadow: 0 4px 10px rgba(0,0,0,0.15); font-weight:800;' : 'background:rgba(255,255,255,0.15); color:#fff;' }}">
            {{ $cat }}
          </button>
        @endforeach
      </div>
    </div>
  </div>

  <!-- === RESULTS === -->
  <div class="scroll" id="search-scroll-container" style="flex:1; padding-bottom:8px;">
    <div style="background:#fff; border-radius: 18px; padding: 16px 0; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
      <!-- Removed results count header -->

      <div class="responsive-grid" id="search-items-grid" style="background: #fff; padding: 16px 14px 0;">
        @foreach($medicines as $idx => $med)
          @php
            $qty = $cart[$med->id] ?? 0;
            $disc = $med->mrp > 0 ? round((($med->mrp - $med->price) / $med->mrp) * 100) : 0;
            $detailUrl = url('/medicine/'.$med->id.(!empty(request('shop_id')) ? '?shop_id='.request('shop_id') : ''));
          @endphp
          <div class="med-row" style="background:#fff; border: 1px solid #E5E7EB; border-radius: 20px; display:flex; padding: 0 12px 0 0; overflow:hidden; min-height:114px; align-items:stretch; justify-content:space-between; margin-bottom: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); text-decoration:none; color:inherit; width:100%;">
            <a href="{{ $detailUrl }}" style="overflow:hidden; position:relative; display:flex; width:114px; align-self:stretch; flex-shrink:0; align-items:center; justify-content:center; text-decoration:none; border:none; background:none;">
              @if(!empty($med->images))
                @php
                  $isRelAbsolute = strpos($med->images[0], 'http://') === 0 || strpos($med->images[0], 'https://') === 0;
                  $relImgUrl = $isRelAbsolute ? $med->images[0] : asset($med->images[0]);
                @endphp
                <img src="{{ $relImgUrl }}" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:cover; display:block;">
              @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#F8FAFF; border-right:1px solid #E5E7EB;">
                  <div style="font-size:32px;">{{ $med->emoji }}</div>
                </div>
              @endif
              @if($idx < 2)
                <div class="bestseller" style="z-index: 2; top:6px; left:0; border-radius:0 6px 6px 0;">Bestseller ✦</div>
              @endif
            </a>

            <div style="flex:1; padding: 10px 10px 10px 14px; display:flex; flex-direction:column; justify-content:center; overflow:hidden; gap:3px;">
              <a href="{{ $detailUrl }}" style="text-decoration:none; display:block; color:inherit;">
                <div style="font-weight:800; font-size:14.5px; color:#1A1A1A; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $med->name }}</div>
                <div style="font-size:11.5px; color:#888;">{{ $med->category }}</div>
                <div style="display:flex; flex-wrap:wrap; gap:6px; align-items:center; margin-top:2px;">
                  <div style="font-size:16px; font-weight:900; color:#1A3C8F; white-space:nowrap;">₹{{ $med->mrp }}</div>
                </div>
              </a>
            </div>

            <div class="cart-controls" data-med-id="{{ $med->id }}" style="flex-shrink:0; padding-left:4px;">
              @if($qty == 0)
                <form action="{{ url('/cart/add') }}" method="POST" class="cart-form" style="margin:0;">
                  @csrf
                  <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                  <button type="submit" class="btn-blue" style="font-size:13px; padding:8px 16px; font-weight:800; border-radius:12px; background:#1A3C8F; color:#fff; border:none; cursor:pointer;">+ Add</button>
                </form>
              @else
                <div class="qty-row" style="display:flex; align-items:center; border:1.5px solid #1A3C8F; border-radius:10px; overflow:hidden; width:80px; height:32px;">
                  <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1; display:flex;">
                    @csrf
                    <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                    <input type="hidden" name="qty" value="{{ $qty - 1 }}">
                    <button type="submit" class="qty-btn" style="padding:0; font-size:16px; color:#1A3C8F; width:100%; border:none; background:#fff; cursor:pointer;">−</button>
                  </form>
                  <div class="qty-num" style="padding:0; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:900; color:#1A3C8F; background:#EEF2FF; flex:1;">{{ $qty }}</div>
                  <form action="{{ url('/cart/update') }}" method="POST" class="cart-form" style="flex:1; display:flex;">
                    @csrf
                    <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                    <input type="hidden" name="qty" value="{{ $qty + 1 }}">
                    <button type="submit" class="qty-btn" style="padding:0; font-size:16px; color:#1A3C8F; width:100%; border:none; background:#fff; cursor:pointer;">+</button>
                  </form>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>

       <!-- Removed Laravel Paginator links for infinite scroll -->

      @if($medicines->count() == 0)
        <div style="text-align:center; padding:40px 20px; color:#888;">
          <div style="font-size:40px; margin-bottom:10px;">😔</div>
          <div style="font-weight:700; font-size:16px; margin-bottom:6px;">Medicine nahi mili</div>
          <div style="font-size:13px;">Prescription upload karein — hum dhundh denge</div>
        </div>
      @endif
    </div>
  </div>

  <!-- Cart floating bar -->
  @if($cartCount > 0)
    <div class="cart-bar" style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); width: calc(100% - 40px); max-width: 600px; z-index: 9999; margin: 0;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="background:#fff; border-radius:10px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:14px; color:#1A3C8F;">
          {{ $cartCount }}
        </div>
        <div>
          <div style="color:#fff; font-weight:800; font-size:13px;">Cart mein {{ $cartCount }} item{{ $cartCount > 1 ? 's' : '' }}</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">Pharmacy auto-match hogi</div>
        </div>
      </div>
      @php
        $checkoutUrl = url('/smartcart/results');
        if (request('shop_id')) {
            $checkoutUrl .= '?shop_id=' . request('shop_id');
        }
      @endphp
      <a href="{{ $checkoutUrl }}" class="btn-outline" style="background:#fff; color:#1A3C8F; border:none; padding:10px 16px; font-size:13px; text-decoration:none;">Checkout →</a>
    </div>
  @endif
</div>

<script>
  function toggleCategoryFilter(category) {
    const urlParams = new URLSearchParams(window.location.search);
    let categories = urlParams.getAll('categories[]');
    
    if (categories.includes(category)) {
      categories = categories.filter(c => c !== category);
    } else {
      categories.push(category);
    }
    
    urlParams.delete('categories[]');
    categories.forEach(c => urlParams.append('categories[]', c));
    
    window.location.search = urlParams.toString();
  }

  // Infinite scroll logic for loading bunches
  let currentPage = 1;
  let isPageLoading = false;
  let hasMorePages = {{ $medicines->hasMorePages() ? 'true' : 'false' }};
  
  const scrollContainer = document.getElementById('search-scroll-container');
  const itemsGrid = document.getElementById('search-items-grid');

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
    
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'infinite-scroll-loading';
    loadingIndicator.style.width = '100%';
    loadingIndicator.style.textAlign = 'center';
    loadingIndicator.style.padding = '15px';
    loadingIndicator.style.fontSize = '12px';
    loadingIndicator.style.color = '#888';
    loadingIndicator.innerText = '📦 Loading more medicines...';
    itemsGrid.appendChild(loadingIndicator);

    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', currentPage);
    const url = `{{ url('/search') }}?${urlParams.toString()}`;
    
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.text())
      .then(html => {
        const oldLoader = document.getElementById('infinite-scroll-loading');
        if (oldLoader) oldLoader.remove();
        
        if (html && html.trim().length > 0) {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          
          // Select only the matching cards from inner view response to append to grid
          const cards = temp.querySelectorAll('.med-row');
          if (cards.length > 0) {
            cards.forEach(card => {
              itemsGrid.appendChild(card);
            });
            // Rebind cart submit listeners to newly appended nodes
            bindResultsCartForms(itemsGrid);
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
        const oldLoader = document.getElementById('infinite-scroll-loading');
        if (oldLoader) oldLoader.remove();
        isPageLoading = false;
      });
  }

  function bindResultsCartForms(parent) {
    parent.querySelectorAll('.cart-form').forEach(form => {
      if (form.dataset.hasListener) return;
      form.dataset.hasListener = "true";
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const url = this.getAttribute('action');
        const formData = new FormData(this);

        fetch(url, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.reload();
          }
        })
        .catch(error => {
          console.error('Error:', error);
          this.submit();
        });
      });
    });
  }

  // Bind initial loaded list items
  bindResultsCartForms(itemsGrid);
</script>
@endsection
