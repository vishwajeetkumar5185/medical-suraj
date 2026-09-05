@extends('layouts.app')

@section('seo_title', 'Register - Dawalo')
@section('content')

<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="min-height:100vh; background:#F5F7FA;">
  
  <!-- Header -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:16px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url('/login') }}" style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
        <span style="color:#fff; font-size:20px;">←</span>
      </a>
      <div style="flex:1;">
        <h1 style="color:#fff; font-size:20px; font-weight:800; margin:0;">📝 Register</h1>
        <p style="color:rgba(255,255,255,0.85); font-size:12px; margin:0;">Create your Dawalo account</p>
      </div>
    </div>
  </div>

  <!-- Registration Form -->
  <div style="padding:20px;">
    
    @if($errors->any())
      <div style="background:#FEF2F2; border-left:4px solid #EF4444; border-radius:12px; padding:14px 16px; margin-bottom:16px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
        @foreach($errors->all() as $error)
          <div style="font-size:13px; color:#DC2626; font-weight:600; margin-bottom:4px;">⚠️ {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <!-- Registration Form Card -->
    <div style="background:#fff; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <form action="{{ url('/register') }}" method="POST">
        @csrf
        
        <!-- Full Name -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">👤 Full Name</label>
          <input 
            type="text" 
            name="name" 
            value="{{ old('name') }}"
            placeholder="e.g. Rahul Kumar" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0EA5E9';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Email -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📧 Email Address</label>
          <input 
            type="email" 
            name="email" 
            value="{{ old('email') }}"
            placeholder="name@example.com" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0EA5E9';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Phone -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📱 Phone Number</label>
          <input 
            type="text" 
            name="phone" 
            value="{{ old('phone') }}"
            placeholder="9876543210" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0EA5E9';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Password -->
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🔒 Password</label>
          <input 
            type="password" 
            name="password" 
            placeholder="Min. 6 characters" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0EA5E9';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom:20px;">
          <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">🔒 Confirm Password</label>
          <input 
            type="password" 
            name="password_confirmation" 
            placeholder="Retype password" 
            required
            style="width:100%; padding:12px 14px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; color:#1A1A1A; outline:none; transition:all 0.2s; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0EA5E9';"
            onblur="this.style.borderColor='#E5E7EB';"
          >
        </div>

        <!-- Register Button -->
        <button 
          type="submit" 
          style="width:100%; padding:14px; background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:800; cursor:pointer; box-shadow:0 4px 12px rgba(14,165,233,0.3); transition:transform 0.2s;"
          onmouseover="this.style.transform='translateY(-2px)';"
          onmouseout="this.style.transform='translateY(0)';"
        >
          🚀 Create Account
        </button>
      </form>
    </div>

    <!-- Login & Pharmacy Links -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      
      <!-- Login Link -->
      <a href="{{ url('/login') }}" style="display:block; padding:12px 16px; background:#F0F9FF; border:2px solid #0EA5E9; border-radius:10px; margin-bottom:10px; text-decoration:none; transition:all 0.2s;"
         onmouseover="this.style.background='#E0F2FE';"
         onmouseout="this.style.background='#F0F9FF';">
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="font-size:24px;">🔐</span>
          <div style="flex:1;">
            <div style="font-size:14px; font-weight:700; color:#0EA5E9;">Already have an account?</div>
            <div style="font-size:11px; color:#0284C7;">Login here</div>
          </div>
          <span style="color:#0EA5E9; font-size:18px;">→</span>
        </div>
      </a>

      <!-- Pharmacy Partner Link -->
      <a href="{{ url('/register/shop') }}" style="display:block; padding:12px 16px; background:#F0FDF4; border:2px solid #10B981; border-radius:10px; text-decoration:none; transition:all 0.2s;"
         onmouseover="this.style.background='#DCFCE7';"
         onmouseout="this.style.background='#F0FDF4';">
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="font-size:24px;">🏪</span>
          <div style="flex:1;">
            <div style="font-size:14px; font-weight:700; color:#10B981;">Pharmacy Partner</div>
            <div style="font-size:11px; color:#059669;">Register your medical store</div>
          </div>
          <span style="color:#10B981; font-size:18px;">→</span>
        </div>
      </a>
    </div>

    <!-- Help Text -->
    <div style="text-align:center; padding:16px;">
      <p style="font-size:12px; color:#94A3B8; line-height:1.6;">
        By creating an account, you agree to<br>
        <a href="#" style="color:#0EA5E9; text-decoration:none; font-weight:600;">Terms of Service</a> & 
        <a href="#" style="color:#0EA5E9; text-decoration:none; font-weight:600;">Privacy Policy</a>
      </p>
    </div>

  </div>

</div>

@endsection
