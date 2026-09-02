<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
<title>@yield('seo_title', 'Dawalo 💊 - Online Medicine Aggregator & 45 Mins Express Delivery')</title>
<meta name="description" content="@yield('seo_description', 'Dawalo is Bihar\'s primary online medicine aggregator. Search and check live inventory of local pharmacies within 5 km. Order online for express home delivery under 45 mins.')">
<meta name="keywords" content="@yield('seo_keywords', 'dawalo, online medicine store, pharmacy aggregator, express medicine delivery, bihar online pharmacy, find local medicine, generic medicines order')">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Progressive Web App (PWA) manifest and styling -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e3a8a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Dawalo">
<link rel="apple-touch-icon" href="{{ asset('assets/icon-192.png') }}">

<!-- Leaflet Map CSS and JS integrations -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;}
body{background:#F0F4FF;display:flex;flex-direction:column;min-height:100vh;font-family:'Segoe UI','Nunito',sans-serif;color:#1A1A1A;}

/* Navbar and Footer Wrappers (Full Width Backgrounds) */
/* Clean Custom Laravel Pagination Styling */
nav[role="navigation"] {
  display: flex !important;
  flex-wrap: wrap !important;
  justify-content: space-between !important;
  align-items: center !important;
  gap: 12px !important;
  background: #ffffff !important;
  padding: 12px 20px !important;
  border-radius: 16px !important;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05) !important;
  border: 1px solid #E5E7EB !important;
  font-size: 13px !important;
  color: #4B5563 !important;
  margin-top: 16px !important;
  width: 100% !important;
}
nav[role="navigation"] > div {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}
nav[role="navigation"] p {
  margin: 0 !important;
  font-size: 13px !important;
  color: #4B5563 !important;
  font-weight: 600 !important;
}
nav[role="navigation"] p span {
  font-weight: 800 !important;
  color: #111827 !important;
}
nav[role="navigation"] a, nav[role="navigation"] span {
  text-decoration: none !important;
}
nav[role="navigation"] span[aria-current="page"] > span {
  background: #1A3C8F !important;
  color: #ffffff !important;
  border-radius: 8px !important;
  padding: 6px 12px !important;
  font-weight: 800 !important;
  box-shadow: 0 4px 12px rgba(26,60,143,0.3) !important;
  display: inline-block !important;
}
nav[role="navigation"] a {
  background: #F3F4F6 !important;
  color: #1F2937 !important;
  border-radius: 8px !important;
  padding: 6px 12px !important;
  font-weight: 700 !important;
  transition: all 0.2s !important;
  display: inline-block !important;
}
nav[role="navigation"] a:hover {
  background: #2563EB !important;
  color: #ffffff !important;
}
nav[role="navigation"] span[aria-disabled="true"] > span {
  background: #F9FAFB !important;
  color: #9CA3AF !important;
  border-radius: 8px !important;
  padding: 6px 12px !important;
  opacity: 0.6 !important;
  display: inline-block !important;
}
svg.w-5.h-5 {
  width: 16px !important;
  height: 16px !important;
  display: inline-block;
  vertical-align: middle;
}
.navbar-wrapper {
  background: linear-gradient(135deg,#1A3C8F,#2563EB);
  width: 100%;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 20px rgba(37,99,235,0.15);
}

/* Modal Overlay & Sheet */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(4px);
  z-index: 99999;
  display: none;
  justify-content: center;
  align-items: center;
}
.sheet {
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  max-width: 420px;
  width: 90%;
  position: relative;
  overflow: hidden;
  animation: modalPop 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes modalPop {
  from { opacity: 0; transform: scale(0.9) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.navbar-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 14px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
}
.brand-logo {
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  cursor: pointer;
  text-decoration: none;
}
.brand-logo span {
  color: #60A5FA;
}
.nav-links {
  display: none;
  gap: 24px;
  align-items: center;
}
.nav-link {
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: color 0.2s;
}
.nav-link:hover, .nav-link.active {
  color: #fff;
}
.nav-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

/* App Container (Centered Content Width) */
#app{
  width:100%;
  max-width:1400px;
  min-height:75vh;
  margin: 0 auto;
  position:relative;
  display:flex;
  flex-direction:column;
  padding: 24px 20px;
}
.screen{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.scroll{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;}
.scroll::-webkit-scrollbar{width:0;}

/* Header */
.hdr-gradient{background:linear-gradient(135deg,#1A3C8F,#2563EB,#3B82F6,#60A5FA);padding:30px 24px 70px;position:relative;overflow:hidden;border-radius: 20px;margin-bottom: 24px;box-shadow: 0 4px 20px rgba(37,99,235,0.08);}
.hdr-circle{position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.08);}
.hdr-circle2{position:absolute;bottom:10px;left:-50px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.06);}

/* Responsive grids */
.responsive-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
@media (min-width: 768px) {
  .responsive-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
  }
  .nav-links {
    display: flex;
  }
}

/* Cards */
.card{background:#fff;border-radius:18px;box-shadow:0 2px 16px rgba(0,0,0,0.07);}
.stat-grid{display:flex;gap:10px;padding:0 14px;margin-top:-50px;position:relative;z-index:10;}
.stat-card{flex:1;background:#fff;border-radius:14px;padding:13px 8px;text-align:center;box-shadow:0 6px 24px rgba(37,99,235,0.12);border:1px solid #F0F4FF;}

/* Buttons */
.btn-blue{background:#1A3C8F;border:none;border-radius:12px;color:#fff;font-weight:800;cursor:pointer;font-family:inherit;padding: 10px 16px;text-decoration:none;display:inline-block;text-align:center;}
.btn-outline{background:#fff;border:1.5px solid #1A3C8F;border-radius:12px;color:#1A3C8F;font-weight:800;cursor:pointer;font-family:inherit;padding: 10px 16px;text-decoration:none;display:inline-block;text-align:center;}
.btn-green{background:linear-gradient(135deg,#059669,#10B981);border:none;border-radius:12px;color:#fff;font-weight:800;cursor:pointer;font-family:inherit;padding: 10px 16px;text-decoration:none;display:inline-block;text-align:center;}
.btn-danger{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;color:#DC2626;font-weight:800;cursor:pointer;font-family:inherit;padding: 10px 16px;text-decoration:none;display:inline-block;text-align:center;}

/* Bottom Nav */
.bottom-nav{background:#fff;border-top:1px solid #E5E7EB;display:flex;justify-content:space-around;padding:10px 0 14px;flex-shrink:0;box-shadow:0 -4px 20px rgba(0,0,0,0.08);margin-top: 20px;border-radius: 12px;}
.nav-btn{display:flex;flex-direction:column;align-items:center;gap:3px;background:none;border:none;cursor:pointer;font-family:inherit;padding:4px 16px;text-decoration:none;}
.nav-dot{width:4px;height:4px;border-radius:50%;background:#1A3C8F;}

/* Search */
.search-box{background:#fff;border-radius:14px;display:flex;align-items:center;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.15);max-width: 600px;}
.search-input{flex:1;border:none;outline:none;padding:13px 14px;font-size:14px;color:#1A1A1A;background:transparent;font-family:inherit;}
.search-btn{background:#1A3C8F;border:none;padding:10px 18px;margin:4px;border-radius:10px;cursor:pointer;color:#fff;font-weight:700;font-size:14px;font-family:inherit;}

/* Pills */
.pill-row{display:flex;gap:8px;margin-top:12px;overflow-x:auto;padding-bottom:4px;}
.pill-row::-webkit-scrollbar{height:0;}
.pill{font-size:12px;font-weight:700;padding:6px 14px;border-radius:20px;white-space:nowrap;cursor:pointer;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.18);color:#fff;display:inline-block;text-decoration:none;}
.pill.active{background:#fff;color:#1A3C8F;}

/* Banner */
.banner-wrap{padding:16px 0 0;}
.banner-card{position:relative;border-radius:20px;overflow:hidden;height:110px;transition:background 0.6s;}
.banner-circle{position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.15);}
.banner-emoji{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:56px;opacity:0.9;}
.banner-text{position:absolute;left:18px;top:50%;transform:translateY(-50%);max-width:70%;}
.banner-dots{position:absolute;bottom:10px;left:18px;display:flex;gap:6px;}

/* Category */
.cat-row{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;}
.cat-row::-webkit-scrollbar{height:0;}
.cat-item{display:flex;flex-direction:column;align-items:center;gap:6px;background:#fff;border-radius:16px;padding:13px 14px;min-width:68px;box-shadow:0 2px 12px rgba(0,0,0,0.06);cursor:pointer;text-decoration:none;}

/* Shop card */
.shop-card{background:#fff;border-radius:20px;padding:16px;box-shadow:0 2px 16px rgba(0,0,0,0.07);margin-bottom:0;}

/* Medicine list (search results) */
.med-row{display:flex;border-bottom:1px solid #F3F4F6;padding: 0 12px 0 0 !important;background:#fff;text-decoration:none;color:inherit;overflow:hidden;}
.med-img{width:120px;height:120px;border-radius:0 !important;background:none;border:none;display:flex;align-items:center;justify-content:center;font-size:52px;flex-shrink:0;position:relative;overflow:hidden;}
.bestseller{position:absolute;top:6px;left:0;background:#FEF3C7;color:#D97706;font-size:9px;font-weight:800;padding:2px 7px;border-radius:0 6px 6px 0;}
.add-btn{width:100%;padding:11px 0;background:#fff;border:1.5px solid #1A3C8F;border-radius:10px;color:#1A3C8F;font-weight:900;font-size:15px;cursor:pointer;font-family:inherit;letter-spacing:1px;text-align:center;display:block;text-decoration:none;}
.qty-row{display:flex;align-items:center;border:1.5px solid #1A3C8F;border-radius:10px;overflow:hidden;}
.qty-btn{flex:1;padding:11px 0;background:#fff;border:none;font-size:20px;font-weight:900;color:#1A3C8F;cursor:pointer;display:inline-block;text-align:center;text-decoration:none;}
.qty-num{flex:1;text-align:center;font-weight:900;font-size:16px;color:#1A3C8F;background:#EEF2FF;padding:11px 0;}

/* Cart bar */
.cart-bar{background:linear-gradient(135deg,#1A3C8F,#2563EB);border-radius:18px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 8px 28px rgba(37,99,235,0.45);margin:8px 0;}

/* Order modal */
.overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);z-index:500;display:flex;flex-direction:column;justify-content:center;align-items:center;padding: 20px;}
.sheet{background:#fff;border-radius:20px;padding:24px;max-width:500px;width:100%;max-height:90%;overflow-y:auto;box-shadow: 0 10px 40px rgba(0,0,0,0.25);}
.handle{width:40px;height:4px;background:#E5E7EB;border-radius:4px;margin:0 auto 18px;display:none;}

/* Smart Cart */
.cart-item-card{background:#fff;border-radius:16px;padding:12px;box-shadow:0 2px 12px rgba(0,0,0,0.07);margin-bottom:9px;display:flex;gap:11px;align-items:center;}

/* Dashboard */
.dash-tab{flex-shrink:0;padding:10px 10px;border:none;border-radius:14px;cursor:pointer;font-weight:800;font-size:11px;font-family:inherit;display:flex;flex-direction:column;align-items:center;gap:3px;text-decoration:none;}

/* Admin */
.admin-tab{flex-shrink:0;padding:8px 10px;border:none;border-radius:12px;cursor:pointer;font-weight:800;font-size:10px;font-family:inherit;display:flex;flex-direction:column;align-items:center;gap:2px;position:relative;text-decoration:none;}

/* Quick setup */
.qs-card{background:#fff;border-radius:14px;padding:11px 12px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:0;display:flex;align-items:center;gap:12px;}
.qs-check{width:26px;height:26px;border-radius:7px;flex-shrink:0;border:2px solid #D1D5DB;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;}
.qs-check.checked{background:#16A34A;border:none;color:#fff;font-weight:900;font-size:14px;}

/* Form inputs */
.form-input{width:100%;padding:12px 14px;border:1.5px solid #E5E7EB;border-radius:12px;font-size:14px;outline:none;color:#1A1A1A;font-family:inherit;background:#fff;transition:border 0.2s;}
.form-input:focus{border-color:#1A3C8F;}
.form-label{display:block;font-size:12px;font-weight:700;color:#666;margin-bottom:6px;}

/* Toggle switch */
.toggle-wrap{width:50px;height:28px;border-radius:99px;position:relative;cursor:pointer;transition:background 0.3s;flex-shrink:0;}
.toggle-dot{width:22px;height:22px;background:#fff;border-radius:50%;position:absolute;top:3px;transition:left 0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.2);}

/* Delivery bar */
.due-bar-track{height:6px;background:#F3F4F6;border-radius:99px;overflow:hidden;margin-top:7px;}
.due-bar-fill{height:100%;border-radius:99px;}

/* Footer */
.footer-wrapper {
  background: #1F2937;
  color: #9CA3AF;
  width: 100%;
  margin-top: auto;
  border-top: 1px solid #374151;
}
.footer-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 20px;
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
}
@media (min-width: 768px) {
  .footer-content {
    grid-template-columns: 2fr 1fr 1fr;
  }
}
.footer-col h3 {
  color: #fff;
  font-size: 16px;
  font-weight: 800;
  margin-bottom: 16px;
}
.footer-col p {
  font-size: 13.5px;
  line-height: 1.6;
}
.footer-col ul {
  list-style: none;
}
.footer-col ul li {
  margin-bottom: 10px;
}
.footer-col ul li a {
  color: #9CA3AF;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
}
.footer-col ul li a:hover {
  color: #fff;
}
.footer-bottom {
  border-top: 1px solid #374151;
  text-align: center;
  padding: 20px;
  font-size: 12px;
  color: #6B7280;
  margin-top: 20px;
}

/* Transitions */
.screen{animation:fadeIn 0.15s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}
::-webkit-scrollbar{width:0;height:0;}
.badge{font-size:10px;font-weight:800;padding:3px 10px;border-radius:8px;display:inline-block;}

/* Hide mobile bottom nav on desktop screens */
@media (min-width: 768px) {
  .bottom-nav {
    display: none;
  }
}

/* Fix mobile bottom nav on mobile screens */
@media (max-width: 767px) {
  .bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    margin: 0;
    border-radius: 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
  }
  body {
    padding-bottom: 80px;
  }
}
</style>
</head>
<body>

<!-- Global Navbar (Full Width) -->
<div class="navbar-wrapper">
  <div class="navbar-content">
    <a href="{{ url('/') }}" class="brand-logo" style="display:flex; align-items:center; height:42px;">
      <img src="{{ asset('assets/logo.png') }}" alt="Dawalo Logo" style="height:100%; object-fit:contain;">
    </a>
    <div class="nav-links">
      <a href="{{ url('/') }}" class="nav-link {{ Request::is('/') || Request::is('search') ? 'active' : '' }}">Home</a>
      @if(Auth::check() && Auth::user()->shop)
        <a href="{{ url('/shop/dashboard') }}" class="nav-link {{ Request::is('shop*') ? 'active' : '' }}">My Store</a>
      @elseif(Auth::check() && Auth::user()->role === 'shop_owner')
        <a href="{{ url('/profile') }}?modal=shopForm" class="nav-link">+ List Shop</a>
      @endif
      <a href="{{ url('/profile') }}" class="nav-link {{ Request::is('profile') ? 'active' : '' }}">Profile</a>
      @if(Auth::check() && Auth::user()->role === 'admin')
        <a href="{{ url('/admin') }}" class="nav-link {{ Request::is('admin*') ? 'active' : '' }}">Admin</a>
      @endif
      @if(Auth::check())
        <form action="{{ url('/logout') }}" method="POST" style="display:inline; margin:0; padding:0;">
          @csrf
          <button type="submit" class="nav-link" style="background:none; border:none; font-family:inherit; font-size:14px; font-weight:700; cursor:pointer; color: rgba(255,255,255,0.85); transition: color 0.2s;">Logout ({{ Auth::user()->name }})</button>
        </form>
      @else
        <a href="{{ url('/login') }}" class="nav-link {{ Request::is('login') ? 'active' : '' }}">Login</a>
      @endif
    </div>
    <div class="nav-right" style="display:flex; align-items:center; gap:8px;">
      <span style="font-size:13px; font-weight:700; color:#60A5FA; cursor:pointer;" onclick="openGlobalLocationModal()">📍 {{ session('user_location', 'Muzaffarpur') }}</span>
      <div style="background:rgba(255,255,255,0.15); border-radius:12px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:16px; cursor:pointer;">🔔</div>
    </div>
  </div>
</div>

<!-- Main App Screen Container (Centered Grid Bounds) -->
<div id="app">


  @yield('content')
</div>

<!-- Global Footer (Full Width) -->
<div class="footer-wrapper">
  <div class="footer-content">
    <!-- Col 1: About -->
    <div class="footer-col">
      <h3>Dawalo 💊</h3>
      <p>{{ session('user_location', 'Muzaffarpur') }}'s primary online medicine aggregator. Finding and checking live pharmacy stock in 5 km coverage. Order directly and get deliveries in under 45 mins. Technology powered by <a href="https://techomission.com/" target="_blank" rel="noopener noreferrer" style="color:#60A5FA; text-decoration:none; font-weight:800;">Techomission</a>.</p>
    </div>
    <!-- Col 2: Navigation -->
    <div class="footer-col">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="{{ url('/') }}">Home Search</a></li>
        <li><a href="{{ url('/smartcart/results') }}">Checkout & Auto Match</a></li>
        <li><a href="{{ url('/profile') }}?modal=shopForm">Store Dashboard Registration</a></li>
        <li><a href="{{ url('/admin') }}">Admin Operations Panel</a></li>
      </ul>
    </div>
    <!-- Col 3: Support -->
    <div class="footer-col">
      <h3>Get In Touch</h3>
      <ul>
        <li><span style="font-size:13.5px;">📧 support@dawalo.in</span></li>
        <li><span style="font-size:13.5px;">📞 +91 9939717283</span></li>
        <li><span style="font-size:13.5px;">📍 {{ session('user_location', 'Muzaffarpur') }}, Bihar</span></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px;">
    <div>© 2026 Dawalo App. All rights reserved.</div>
    <div style="font-weight:700; color:rgba(255,255,255,0.75);">
      Made with ❤️ by <a href="https://techomission.com/" target="_blank" rel="noopener noreferrer" style="color:#60A5FA; text-decoration:none; font-weight:900;">Techomission</a>
    </div>
  </div>
</div>

<!-- Global Location Selection Modal -->
<div id="global-location-modal" class="overlay" style="display:none; justify-content:center; align-items:center; transition: all 0.3s ease;">
  <div class="sheet" id="location-modal-sheet" style="max-width:400px; width:100%; text-align:center; transition: all 0.3s ease; position:relative; display:flex; flex-direction:column; padding:20px;">
    <!-- Map Control Header Action Buttons -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
      <h3 style="font-weight:900; font-size:16px; color:#1A202C; margin:0;">Select Delivery Location</h3>
      <div style="display:flex; gap:6px;">
        <button type="button" onclick="toggleMapSatelliteView()" id="btn-toggle-satellite" style="background:#4A5568; border:none; color:#fff; font-size:10px; font-weight:800; padding:6px 10px; border-radius:6px; cursor:pointer;">🛰️ Satellite</button>
        <button type="button" onclick="toggleMapFullScreen()" id="btn-toggle-fullscreen" style="background:#1A3C8F; border:none; color:#fff; font-size:10px; font-weight:800; padding:6px 10px; border-radius:6px; cursor:pointer;">🗖 Full Screen</button>
      </div>
    </div>
    
    <div style="display:flex; flex-direction:column; gap:8px; flex:1;">
      <!-- Interactive Leaflet Map Div container -->
      <div id="leaflet-location-map" style="width:100%; height:200px; border-radius:12px; border:1px solid #CBD5E0; position:relative; transition: height 0.3s ease;"></div>
      
      <div style="font-size:11.5px; color:#4A5568; margin-top:2px; text-align:left; font-weight:600; line-height:1.4;">
        📍 Selected Location: <span id="map-address-preview" style="color:#1A3C8F; font-weight:800;">Loading map position...</span>
      </div>
      
      <button onclick="confirmMapLocationSelected()" class="btn-green" style="width:100%; border:none; padding:12px; font-weight:800; font-size:13.5px; border-radius:10px; cursor:pointer; margin-top:2px;">
        ✓ Confirm Location
      </button>
    </div>
    <button onclick="closeGlobalLocationModal()" class="btn-danger" style="margin-top:10px; width:100%; border:none; padding:10px; font-size:13px; cursor:pointer; border-radius:10px;">Cancel</button>
  </div>
</div>

<script>
window.onerror = function(message, source, lineno, colno, error) {
    alert("JS Error: " + message + " on line " + lineno);
    return false;
};

let userLocationMap;
let mapMarker;
let mapSelectedAddress = "{{ session('user_location', 'Muzaffarpur') }}";

// Map Layer Instances
let streetLayer;
let satelliteLayer;
let activeLayerName = 'street';
let isMapFullScreen = false;

function openGlobalLocationModal() {
  document.getElementById('global-location-modal').style.display = 'flex';
  
  // Initialize Leaflet Map once modal container is rendered
  setTimeout(() => {
    if (!userLocationMap) {
      // Default to Jaipur/Muzaffarpur center coordinates
      userLocationMap = L.map('leaflet-location-map', { zoomControl: true }).setView([26.9124, 75.7873], 13);
      
      // Define Layers
      streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
      });
      
      satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
      });
      
      // Load Default
      streetLayer.addTo(userLocationMap);
      
      // Default Marker
      mapMarker = L.marker([26.9124, 75.7873], { draggable: true }).addTo(userLocationMap);
      
      // Event handler for marker drag end
      mapMarker.on('dragend', function (e) {
        const position = mapMarker.getLatLng();
        updateAddressFromCoordinates(position.lat, position.lng);
      });
      
      // Event handler for click on map
      userLocationMap.on('click', function(e) {
        mapMarker.setLatLng(e.latlng);
        updateAddressFromCoordinates(e.latlng.lat, e.latlng.lng);
      });

      // Try browser coordinates if allowed
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          userLocationMap.setView([lat, lng], 16);
          mapMarker.setLatLng([lat, lng]);
          updateAddressFromCoordinates(lat, lng);
        });
      }
    } else {
      userLocationMap.invalidateSize();
    }
  }, 300);
}

function toggleMapSatelliteView() {
  if (!userLocationMap) return;
  const btn = document.getElementById('btn-toggle-satellite');
  
  if (activeLayerName === 'street') {
    userLocationMap.removeLayer(streetLayer);
    satelliteLayer.addTo(userLocationMap);
    activeLayerName = 'satellite';
    btn.innerHTML = '🗺️ Street View';
    btn.style.background = '#3182CE';
  } else {
    userLocationMap.removeLayer(satelliteLayer);
    streetLayer.addTo(userLocationMap);
    activeLayerName = 'street';
    btn.innerHTML = '🛰️ Satellite';
    btn.style.background = '#4A5568';
  }
}

function toggleMapFullScreen() {
  const modalSheet = document.getElementById('location-modal-sheet');
  const mapDiv = document.getElementById('leaflet-location-map');
  const btn = document.getElementById('btn-toggle-fullscreen');
  
  if (!isMapFullScreen) {
    // Enable Full Screen styling overlay properties
    modalSheet.style.maxWidth = '100vw';
    modalSheet.style.width = '100vw';
    modalSheet.style.height = '100vh';
    modalSheet.style.borderRadius = '0';
    mapDiv.style.height = '68vh';
    btn.innerHTML = '🗗 Minimize';
    btn.style.background = '#E53E3E';
    isMapFullScreen = true;
  } else {
    // Restore default sizing overlay properties
    modalSheet.style.maxWidth = '400px';
    modalSheet.style.width = '100%';
    modalSheet.style.height = 'auto';
    modalSheet.style.borderRadius = '20px';
    mapDiv.style.height = '200px';
    btn.innerHTML = '🗖 Full Screen';
    btn.style.background = '#1A3C8F';
    isMapFullScreen = false;
  }
  
  setTimeout(() => {
    if (userLocationMap) {
      userLocationMap.invalidateSize();
    }
  }, 300);
}

function updateAddressFromCoordinates(lat, lng) {
  document.getElementById('map-address-preview').innerText = "Fetching address details...";
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
    headers: { 'Accept-Language': 'en' }
  })
  .then(res => res.json())
  .then(data => {
    let addr = data.address || {};
    let city = '';
    if (addr.district) {
        city = addr.district;
    } else if (addr.city_district) {
        city = addr.city_district;
    } else if (addr.state_district) {
        city = addr.state_district;
    } else if (addr.county) {
        city = addr.county;
    } else if (addr.city) {
        city = addr.city;
    } else if (addr.town) {
        city = addr.town;
    } else if (addr.village) {
        city = addr.village;
    } else {
        city = 'Jaipur';
    }
    
    city = city.replace(/\s+(District|Division|City|Tehsil)/i, '').trim();

    let displayLoc = '';
    
    if (data.display_name) {
        let parts = data.display_name.split(',').map(p => p.trim());
        let filteredParts = parts.filter(p => {
            let lower = p.toLowerCase();
            return !lower.includes('india') && 
                   !lower.includes('rajasthan') && 
                   !lower.match(/^\d{6}$/) && 
                   lower !== city.toLowerCase();
        });
        if (filteredParts.length > 0) {
            displayLoc = filteredParts.slice(0, 2).join(', ') + ', ' + city;
        }
    }

    if (!displayLoc) {
        let landmark = data.name || addr.amenity || addr.shop || addr.building || addr.commercial || addr.office || '';
        let road = addr.road || addr.suburb || addr.neighbourhood || '';
        if (landmark) displayLoc += landmark.trim();
        if (road) displayLoc += (displayLoc ? ', ' : '') + road.trim();
        if (!displayLoc) {
            displayLoc = city;
        } else {
            displayLoc += ', ' + city;
        }
    }
    
    mapSelectedAddress = displayLoc;
    document.getElementById('map-address-preview').innerText = displayLoc;
    
    // Store selected coordinates to redirect variables
    window.mapSelectedLat = lat;
    window.mapSelectedLng = lng;
  })
  .catch(err => {
    console.error(err);
    document.getElementById('map-address-preview').innerText = "Lookup failed, confirm to retry.";
  });
}

function confirmMapLocationSelected() {
  if (mapSelectedAddress) {
    let url = `/set-location?city=${encodeURIComponent(mapSelectedAddress)}`;
    if (window.mapSelectedLat && window.mapSelectedLng) {
      url += `&lat=${window.mapSelectedLat}&lng=${window.mapSelectedLng}`;
    }
    window.location.href = url;
  }
}

function closeGlobalLocationModal() {
  document.getElementById('global-location-modal').style.display = 'none';
}
function autoDetectCity() {
  const btn = document.getElementById('btn-auto-detect-city');
  const originalHTML = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '🕒 Detecting Location...';

  if (!navigator.geolocation) {
    alert("Geolocation is not supported by your browser.");
    btn.disabled = false;
    btn.innerHTML = originalHTML;
    return;
  }

  navigator.geolocation.getCurrentPosition(
    function(position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
        headers: {
          'Accept-Language': 'en'
        }
      })
      .then(res => res.json())
      .then(data => {
        let city = '';
        const addr = data.address || {};
        if (addr.district) {
            city = addr.district;
        } else if (addr.city_district) {
            city = addr.city_district;
        } else if (addr.state_district) {
            city = addr.state_district;
        } else if (addr.county) {
            city = addr.county;
        } else if (addr.city) {
            city = addr.city;
        } else if (addr.town) {
            city = addr.town;
        } else if (addr.village) {
            city = addr.village;
        } else {
            city = 'Jaipur';
        }
        
        city = city.replace(/\s+(District|Division|City|Tehsil)/i, '').trim();

        // If data.name is missing or identical to city/suburb, parse display_name segments directly for precise mapping
        let displayLoc = '';
        if (data.display_name) {
            let parts = data.display_name.split(',').map(p => p.trim());
            // Filter out country, state, pincode, and district names from the display segments
            let filteredParts = parts.filter(p => {
                let lower = p.toLowerCase();
                return !lower.includes('india') && 
                       !lower.includes('rajasthan') && 
                       !lower.match(/^\d{6}$/) && // Pincode
                       lower !== city.toLowerCase();
            });
            if (filteredParts.length > 0) {
                // Take the 2 most precise local landmark/road segments
                displayLoc = filteredParts.slice(0, 2).join(', ') + ', ' + city;
            }
        }

        if (!displayLoc) {
            let landmark = data.name || addr.amenity || addr.shop || addr.building || addr.commercial || addr.office || '';
            let road = addr.road || addr.suburb || addr.neighbourhood || '';
            if (landmark) displayLoc += landmark.trim();
            if (road) displayLoc += (displayLoc ? ', ' : '') + road.trim();
            if (!displayLoc) {
                displayLoc = city;
            } else {
                displayLoc += ', ' + city;
            }
        }

        window.location.href = `/set-location?city=${encodeURIComponent(displayLoc)}&lat=${lat}&lng=${lng}`;
      })
      .catch(err => {
        console.error(err);
        alert("Location match failed. Setting city to Muzaffarpur.");
        window.location.href = `/set-location?city=Muzaffarpur&lat=26.1209&lng=85.3647`;
      });
    },
    function(error) {
      console.warn("GPS failed, trying IP fallback...", error);
      btn.innerHTML = '🕒 Trying IP Lookup...';
      
      // Fetch location via a more reliable IP Geolocation provider: ipinfo.io
      fetch('https://ipinfo.io/json')
      .then(res => res.json())
      .then(data => {
        let city = data.city || 'Muzaffarpur';
        city = city.replace(/\s+(District|Division|City)/i, '').trim();
        
        let region = data.region || '';
        let fullLoc = city;
        if (region && region !== city) {
            fullLoc = region + ', ' + city;
        }
        window.location.href = `/set-location?city=${encodeURIComponent(fullLoc)}`;
      })
      .catch(err => {
        console.error("IP Geolocation fallback failed:", err);
        // If everything fails, request user browser again
        alert("Detecting location failed. Please select your city manually.");
        btn.disabled = false;
        btn.innerHTML = originalHTML;
      });
    },
    { enableHighAccuracy: true, timeout: 5000 }
  );
}

// Instant Complete Detailed Location System
(function() {
  function formatDetailedLocationFromNominatim(data) {
    if (!data) return 'Jaipur';
    let city = '';
    const addr = data.address || {};
    if (addr.district) {
        city = addr.district;
    } else if (addr.city_district) {
        city = addr.city_district;
    } else if (addr.state_district) {
        city = addr.state_district;
    } else if (addr.county) {
        city = addr.county;
    } else if (addr.city) {
        city = addr.city;
    } else if (addr.town) {
        city = addr.town;
    } else if (addr.village) {
        city = addr.village;
    } else {
        city = 'Jaipur';
    }
    
    city = city.replace(/\s+(District|Division|City|Tehsil)/i, '').trim();

    let displayLoc = '';
    if (data.display_name) {
        let parts = data.display_name.split(',').map(p => p.trim());
        let filteredParts = parts.filter(p => {
            let lower = p.toLowerCase();
            return !lower.includes('india') && 
                   !lower.includes('rajasthan') && 
                   !lower.match(/^\d{6}$/) &&
                   lower !== city.toLowerCase();
        });
        if (filteredParts.length > 0) {
            displayLoc = filteredParts.slice(0, 2).join(', ') + ', ' + city;
        }
    }

    if (!displayLoc) {
        let landmark = data.name || addr.amenity || addr.shop || addr.building || addr.commercial || addr.office || '';
        let road = addr.road || addr.suburb || addr.neighbourhood || '';
        if (landmark) displayLoc += landmark.trim();
        if (road) displayLoc += (displayLoc ? ', ' : '') + road.trim();
        if (!displayLoc) {
            displayLoc = city;
        } else {
            displayLoc += ', ' + city;
        }
    }

    return displayLoc;
  }

  // 1. Instant UI restore from LocalStorage ONLY if it is a detailed address
  const cachedCity = localStorage.getItem('dawalo_city');
  if (cachedCity && cachedCity.includes(',')) {
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.nav-right span, .footer-col span').forEach(el => {
        if (el.textContent.includes('📍')) {
          el.textContent = '📍 ' + cachedCity;
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    function updateLocationInBackground(city, lat, lng) {
      if (!city) return;
      localStorage.setItem('dawalo_city', city);
      if (lat) localStorage.setItem('dawalo_lat', lat);
      if (lng) localStorage.setItem('dawalo_lng', lng);

      document.querySelectorAll('.nav-right span, .footer-col span').forEach(el => {
        if (el.textContent.includes('📍')) {
          el.textContent = '📍 ' + city;
        }
      });

      let url = `/set-location?city=${encodeURIComponent(city)}`;
      if (lat && lng) url += `&lat=${lat}&lng=${lng}`;
      
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).catch(() => {});
    }

    function fallbackIpLocation() {
      if (localStorage.getItem('dawalo_city') && localStorage.getItem('dawalo_city').includes(',')) {
        return;
      }
      fetch('https://ipinfo.io/json')
        .then(res => res.json())
        .then(data => {
          let city = data.city || 'Jaipur';
          city = city.replace(/\s+(District|Division|City)/i, '').trim();
          let lat = null, lng = null;
          if (data.loc) {
            let coords = data.loc.split(',');
            lat = coords[0];
            lng = coords[1];
          }
          if (!localStorage.getItem('dawalo_city')) {
            updateLocationInBackground(city, lat, lng);
          }
        })
        .catch(err => console.error("IP location failed:", err));
    }

    function fetchFullGPSLocation() {
      if (!navigator.geolocation) {
        fallbackIpLocation();
        return;
      }

      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;

          // DIRECT COMPLETE LOCATION: Reverse geocode to exact street address
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': 'en' }
          })
          .then(res => res.json())
          .then(data => {
            let fullAddr = formatDetailedLocationFromNominatim(data);
            updateLocationInBackground(fullAddr, lat, lng);
          })
          .catch(err => {
            console.warn("Nominatim failed, using BigDataCloud fallback...", err);
            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
              .then(r => r.json())
              .then(bdData => {
                let locality = bdData.locality || bdData.city || 'Jaipur';
                let principal = bdData.principalSubdivision || '';
                let display = locality;
                if (principal && principal !== locality) display += ', ' + principal;
                updateLocationInBackground(display, lat, lng);
              })
              .catch(() => fallbackIpLocation());
          });
        },
        (error) => {
          console.warn("GPS failed or blocked, IP fallback...", error);
          fallbackIpLocation();
        },
        { enableHighAccuracy: false, timeout: 2000, maximumAge: 300000 }
      );
    }

    // Fire GPS fetch DIRECTLY for full complete detailed address
    fetchFullGPSLocation();

    // Listen to live permission changes (when user unblocks or changes permission)
    if (navigator.permissions && navigator.permissions.query) {
      navigator.permissions.query({ name: 'geolocation' }).then(status => {
        status.onchange = () => {
          if (status.state === 'granted') {
            fetchFullGPSLocation();
          }
        };
      }).catch(() => {});
    }
  window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
      if (document.body.innerText.trim().startsWith('{') && document.body.innerText.includes('"html"')) {
        window.location.reload();
      }
    }
  });
});
})();
</script>

<!-- Custom PWA Install Banner -->
<div id="pwa-install-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 450px; background: rgba(30, 58, 138, 0.96); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); color: #ffffff; padding: 14px 18px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25); z-index: 999999; border: 1px solid rgba(255, 255, 255, 0.15); flex-direction: row; justify-content: space-between; align-items: center; gap: 12px; transition: opacity 0.3s ease;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <img src="{{ asset('assets/icon-192.png') }}" style="width: 44px; height: 44px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);" alt="Dawalo Logo">
        <div>
            <h4 style="font-size: 14px; font-weight: 700; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dawalo Install Karein</h4>
            <p style="font-size: 11px; color: #E0E7FF; margin: 2px 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Check pharmacy medicine inventory live</p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <button id="pwa-install-btn" style="background: #ffffff; color: #1e3a8a; border: none; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; cursor: pointer; transition: transform 0.2s; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Install</button>
        <button id="pwa-close-btn" style="background: transparent; color: #ffffff; border: none; font-size: 16px; cursor: pointer; padding: 4px 8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">✕</button>
    </div>
</div>

<!-- iOS PWA Install Banner -->
<div id="pwa-ios-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 450px; background: rgba(255, 255, 255, 0.98); color: #1f2937; padding: 14px 18px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); z-index: 999999; border: 1px solid #E5E7EB; flex-direction: row; justify-content: space-between; align-items: center; gap: 12px; transition: opacity 0.3s ease;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <img src="{{ asset('assets/icon-192.png') }}" style="width: 44px; height: 44px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);" alt="Dawalo Logo">
        <div>
            <h4 style="font-size: 13.5px; font-weight: 700; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Add to Home Screen</h4>
            <p style="font-size: 11px; color: #4B5563; margin: 3px 0 0; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Tap the <span style="font-weight: 700;">Share button 📤</span> and select <span style="font-weight: 700;">Add to Home Screen</span> to install Dawalo on iOS.</p>
        </div>
    </div>
    <button id="pwa-ios-close-btn" style="background: transparent; color: #9CA3AF; border: none; font-size: 16px; cursor: pointer; padding: 4px 8px; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">✕</button>
</div>

<script>
    // ==========================================
    // Progressive Web App (PWA) Handler
    // ==========================================
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then((reg) => {
                    console.log('[Service Worker] Registered successfully', reg.scope);
                    reg.update(); // Check for updates on every page load
                })
                .catch((err) => console.warn('[Service Worker] Registration failed', err));
        });
    }

    let deferredPrompt;
    const installBanner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const closeBtn = document.getElementById('pwa-close-btn');

    // Android/Chrome Custom Prompt
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default browser prompt
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        // Show our custom banner
        if (installBanner) {
            installBanner.style.display = 'flex';
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', () => {
            if (!deferredPrompt) return;
            // Show the native installation prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] User accepted the install prompt');
                } else {
                    console.log('[PWA] User dismissed the install prompt');
                }
                deferredPrompt = null;
                // Hide banner
                if (installBanner) {
                    installBanner.style.display = 'none';
                }
            });
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            if (installBanner) {
                installBanner.style.display = 'none';
            }
        });
    }

    // iOS/Safari Custom Fallback Banner
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const isInStandaloneMode = ('standalone' in window.navigator) && (window.navigator.standalone);

    const iosBanner = document.getElementById('pwa-ios-banner');
    const iosCloseBtn = document.getElementById('pwa-ios-close-btn');

    if (isIOS && isSafari && !isInStandaloneMode) {
        // Check if user has already dismissed it in this session
        if (!sessionStorage.getItem('pwa-ios-dismissed')) {
            if (iosBanner) {
                iosBanner.style.display = 'flex';
            }
        }
    }

    if (iosCloseBtn) {
        iosCloseBtn.addEventListener('click', () => {
            if (iosBanner) {
                iosBanner.style.display = 'none';
            }
            sessionStorage.setItem('pwa-ios-dismissed', 'true');
        });
    }

    // Hide banners if app is installed successfully
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App installed successfully!');
        if (installBanner) installBanner.style.display = 'none';
        if (iosBanner) iosBanner.style.display = 'none';
    });

    // ==========================================
    // Web Push Notification Helper
    // ==========================================
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    const VAPID_PUBLIC_KEY = "{{ config('services.webpush.public_key') }}";

    // ── Push Notification Banner Toggle ──────────────────────────────────────
    function showPushBanner(isEnabled) {
        const enableBanner  = document.getElementById('push-notification-banner');
        const enabledBanner = document.getElementById('push-enabled-banner');
        if (!enableBanner || !enabledBanner) return; // not on dashboard page
        if (isEnabled) {
            enableBanner.style.display  = 'none';
            enabledBanner.style.display = 'flex';
        } else {
            enableBanner.style.display  = 'flex';
            enabledBanner.style.display = 'none';
        }
    }

    // ── Enable Push Notifications ─────────────────────────────────────────────
    window.requestPushSubscription = function() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Push notifications are not supported by this browser.');
            return;
        }

        Notification.requestPermission().then((permission) => {
            if (permission !== 'granted') {
                alert('Notification permission denied. Please allow notifications in browser settings.');
                return;
            }

            navigator.serviceWorker.ready.then((registration) => {
                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                });
            }).then((subscription) => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                return fetch('/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(subscription)
                });
            }).then(r => r.json())
            .then((data) => {
                if (data.success) {
                    showPushBanner(true);
                } else {
                    alert('Failed to enable push notifications: ' + data.message);
                }
            }).catch((err) => {
                console.error('Push subscription error:', err);
                alert('Error: Could not subscribe to notifications. Please try again.');
            });
        });
    };

    // ── Disable Push Notifications ────────────────────────────────────────────
    window.disablePushSubscription = function() {
        if (!('serviceWorker' in navigator)) return;
        navigator.serviceWorker.ready.then((registration) => {
            return registration.pushManager.getSubscription();
        }).then((subscription) => {
            if (!subscription) { showPushBanner(false); return; }
            return subscription.unsubscribe();
        }).then(() => {
            showPushBanner(false);
        }).catch((err) => {
            console.error('Unsubscribe error:', err);
        });
    };

    // ── Auto-detect state on page load ────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const enableBanner  = document.getElementById('push-notification-banner');
        const enabledBanner = document.getElementById('push-enabled-banner');
        if (!enableBanner || !enabledBanner) return; // Only run on shop dashboard

        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            // Browser doesn't support push, show enable banner anyway (will show error on click)
            showPushBanner(false);
            return;
        }

        navigator.serviceWorker.ready.then((reg) => {
            return reg.pushManager.getSubscription();
        }).then((subscription) => {
            const isActive = subscription !== null && Notification.permission === 'granted';
            showPushBanner(isActive);
        }).catch(() => {
            showPushBanner(false);
        });
    });
</script>

</body>
</html>
