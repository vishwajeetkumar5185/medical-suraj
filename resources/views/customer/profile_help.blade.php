@extends('layouts.app')

@section('content')
<style>
  .navbar-wrapper { display: none !important; }
  .footer-wrapper { display: none !important; }
  #app { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
  body { background: #F5F7FA !important; }
</style>

<div style="background:#F5F7FA; min-height:100vh; display:block !important;">
  
  <!-- === HEADER === -->
  <div style="background:linear-gradient(180deg, #0EA5E9 0%, #0284C7 100%); padding:12px 16px 20px; border-radius:0 0 20px 20px; position:relative;">
    
    <!-- Header Row -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ url('/profile') }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none;">
          <span style="color:#fff; font-size:18px;">←</span>
        </a>
        <div>
          <div style="color:#fff; font-size:16px; font-weight:800;">❓ Help & Support</div>
          <div style="color:rgba(255,255,255,0.7); font-size:11px;">Get assistance & contact us</div>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">💬</span>
        </div>
        <div style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">🔔</span>
        </div>
        <a href="{{ url('/profile') }}" style="width:36px; height:36px; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:18px;">👤</span>
        </a>
      </div>
    </div>

    <!-- Search Box -->
    <div style="margin-bottom:14px;">
      <div onclick="window.location.href='{{ url('/search') }}'" style="background:#fff; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); cursor:pointer; transition:all 0.2s ease;">
        <span style="font-size:20px; color:#3B82F6;">🔍</span>
        <span style="flex:1; font-size:14px; color:#94A3B8; font-weight:500;">Medicine ya lab test search karein...</span>
        <span style="font-size:16px; color:#3B82F6;">›</span>
      </div>
    </div>

    <!-- Category Pills -->
    <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:2px;">
      <a href="{{ url('/search?q=Bukhar') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🤒</span> Bukhar
      </a>
      <a href="{{ url('/search?q=Diabetes') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>🩸</span> Diabetes
      </a>
      <a href="{{ url('/search?q=Skin') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💧</span> Skin Care
      </a>
      <a href="{{ url('/search?q=Pain') }}" style="background:rgba(255,255,255,0.25); backdrop-filter:blur(10px); color:#fff; padding:8px 14px; border-radius:18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,0.3);">
        <span>💊</span> Pain
      </a>
    </div>
  </div>

  <!-- === MAIN CONTENT === -->
  <div style="padding:16px; padding-bottom:100px;">
    
    <!-- Quick Help Actions -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0 0 16px 0;">🚀 Quick Help</h3>
      
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <!-- Live Chat -->
        <div onclick="alert('Live chat coming soon!')" style="background:#F0F9FF; border:2px solid #BFDBFE; border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          <div style="font-size:32px; margin-bottom:8px;">💬</div>
          <div style="font-weight:700; font-size:13px; color:#0EA5E9;">Live Chat</div>
          <div style="font-size:11px; color:#64748B; margin-top:4px;">Instant help</div>
        </div>

        <!-- Call Support -->
        <div onclick="window.open('tel:+919939717283')" style="background:#F0FDF4; border:2px solid #BBF7D0; border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          <div style="font-size:32px; margin-bottom:8px;">📞</div>
          <div style="font-weight:700; font-size:13px; color:#10B981;">Call Now</div>
          <div style="font-size:11px; color:#64748B; margin-top:4px;">24/7 support</div>
        </div>

        <!-- WhatsApp -->
        <div onclick="window.open('https://wa.me/919939717283?text=Hi, I need help with Dawalo')" style="background:#F0FDF4; border:2px solid #BBF7D0; border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          <div style="font-size:32px; margin-bottom:8px;">📱</div>
          <div style="font-weight:700; font-size:13px; color:#10B981;">WhatsApp</div>
          <div style="font-size:11px; color:#64748B; margin-top:4px;">Quick message</div>
        </div>

        <!-- Email Support -->
        <div onclick="window.open('mailto:support@dawalo.com')" style="background:#FEF3F2; border:2px solid #FECACA; border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          <div style="font-size:32px; margin-bottom:8px;">📧</div>
          <div style="font-weight:700; font-size:13px; color:#EF4444;">Email Us</div>
          <div style="font-size:11px; color:#64748B; margin-top:4px;">Detailed query</div>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0 0 16px 0;">❓ Frequently Asked Questions</h3>
      
      <div style="display:flex; flex-direction:column; gap:12px;">
        <!-- FAQ Item 1 -->
        <div style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
          <div onclick="toggleFaq(1)" style="background:#F8FAFC; padding:16px; cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-weight:700; font-size:14px; color:#374151;">🏥 Order kaise place karein?</div>
              <div style="font-size:11px; color:#64748B; margin-top:2px;">Medicine order process guide</div>
            </div>
            <span style="font-size:18px; color:#64748B;" id="faq-icon-1">+</span>
          </div>
          <div id="faq-content-1" style="display:none; padding:16px; background:#fff; font-size:13px; color:#374151; line-height:1.5;">
            1. Medicine search karें<br>
            2. Cart mein add karें<br>
            3. Delivery address add karें<br>
            4. Order confirm karें<br>
            5. Pharmacy accept karega aur deliver karega
          </div>
        </div>

        <!-- FAQ Item 2 -->
        <div style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
          <div onclick="toggleFaq(2)" style="background:#F8FAFC; padding:16px; cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-weight:700; font-size:14px; color:#374151;">💰 Payment options kya hain?</div>
              <div style="font-size:11px; color:#64748B; margin-top:2px;">Available payment methods</div>
            </div>
            <span style="font-size:18px; color:#64748B;" id="faq-icon-2">+</span>
          </div>
          <div id="faq-content-2" style="display:none; padding:16px; background:#fff; font-size:13px; color:#374151; line-height:1.5;">
            • Cash on Delivery (COD)<br>
            • UPI Payment<br>
            • Credit/Debit Cards<br>
            • Net Banking<br>
            • Digital Wallets
          </div>
        </div>

        <!-- FAQ Item 3 -->
        <div style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
          <div onclick="toggleFaq(3)" style="background:#F8FAFC; padding:16px; cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-weight:700; font-size:14px; color:#374151;">🚚 Delivery kitni der mein hoti hai?</div>
              <div style="font-size:11px; color:#64748B; margin-top:2px;">Delivery timeframes</div>
            </div>
            <span style="font-size:18px; color:#64748B;" id="faq-icon-3">+</span>
          </div>
          <div id="faq-content-3" style="display:none; padding:16px; background:#fff; font-size:13px; color:#374151; line-height:1.5;">
            • Same day delivery: 2-4 hours<br>
            • Express delivery: 30-60 minutes<br>
            • Standard delivery: Next day<br>
            • Emergency: 15-30 minutes (premium)
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Information -->
    <div style="background:#fff; border-radius:16px; padding:20px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
      <h3 style="font-weight:800; font-size:16px; color:#1A1A1A; margin:0 0 16px 0;">📞 Contact Information</h3>
      
      <div style="display:flex; flex-direction:column; gap:16px;">
        <!-- Phone -->
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:40px; height:40px; background:#F0F9FF; border-radius:10px; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:18px;">📞</span>
          </div>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">Customer Support</div>
            <a href="tel:+919939717283" style="font-size:13px; color:#0EA5E9; font-weight:600; text-decoration:none;">+91 99397 17283</a>
          </div>
        </div>

        <!-- Email -->
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:40px; height:40px; background:#FEF3F2; border-radius:10px; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:18px;">📧</span>
          </div>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">Email Support</div>
            <a href="mailto:support@dawalo.com" style="font-size:13px; color:#0EA5E9; font-weight:600; text-decoration:none;">support@dawalo.com</a>
          </div>
        </div>

        <!-- WhatsApp -->
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:40px; height:40px; background:#F0FDF4; border-radius:10px; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:18px;">💬</span>
          </div>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">WhatsApp Support</div>
            <a href="https://wa.me/919939717283" style="font-size:13px; color:#0EA5E9; font-weight:600; text-decoration:none;">Chat with us</a>
          </div>
        </div>

        <!-- Business Hours -->
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:40px; height:40px; background:#FEF3C7; border-radius:10px; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:18px;">🕒</span>
          </div>
          <div>
            <div style="font-weight:700; font-size:14px; color:#374151;">Business Hours</div>
            <div style="font-size:13px; color:#64748B;">24/7 Available (Medicine delivery)</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Bottom Navigation -->
  <div style="position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:600px; background:#fff; border-top:1px solid #E5E7EB; padding:8px 20px 12px; display:flex; justify-content:space-around; align-items:center; z-index:1000;">
    <a href="{{ url('/') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🏠</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Home</span>
    </a>
    <a href="{{ url('/smartcart') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
        <span style="font-size:22px;">🛒</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#64748B;">Cart</span>
    </a>
    <a href="{{ url('/profile') }}" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
      <div style="width:48px; height:48px; background:#0EA5E9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:4px; box-shadow:0 2px 8px rgba(14,165,233,0.3);">
        <span style="font-size:22px;">👤</span>
      </div>
      <span style="font-size:11px; font-weight:700; color:#0EA5E9;">Profile</span>
    </a>
  </div>

</div>

<script>
function toggleFaq(faqNumber) {
  const content = document.getElementById('faq-content-' + faqNumber);
  const icon = document.getElementById('faq-icon-' + faqNumber);
  
  if (content.style.display === 'none' || content.style.display === '') {
    content.style.display = 'block';
    icon.textContent = '−';
  } else {
    content.style.display = 'none';
    icon.textContent = '+';
  }
}
</script>
@endsection
