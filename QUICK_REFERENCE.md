# Dawalo - Quick Reference Guide 🚀

## 📌 One-Minute Overview

**What is Dawalo?**  
Medicine delivery platform connecting customers with local pharmacies (like Swiggy but for medicines)

**Tech Stack:**  
Laravel 12 + SQLite/MySQL + TailwindCSS + Web Push + WhatsApp

**User Types:**  
👤 Customers → 🏪 Shop Owners → 👨‍💼 Admin

---

## 🔑 Quick Access Commands

### Start Development Server
```bash
php artisan serve
# Opens at: http://127.0.0.1:8000
```

### Run With Queue & Vite
```bash
composer run dev
```

### Database Operations
```bash
# Reset & Seed Database
php artisan migrate:fresh --seed

# Just Run Migrations
php artisan migrate

# Clear All Caches
php artisan optimize:clear
```

### Switch to Production MySQL
1. Edit `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=u403768071_medical
   DB_USERNAME=u403768071_medical
   DB_PASSWORD=Medical@2026
   ```
2. Run: `php artisan migrate --force`

---

## 🌐 Important URLs (Local)

### Customer
- 🏠 Home: `http://127.0.0.1:8000/`
- 🔍 Search: `http://127.0.0.1:8000/search`
- 🛒 Smart Cart: `http://127.0.0.1:8000/smartcart`
- 👤 Profile: `http://127.0.0.1:8000/profile`
- 📋 Orders: `http://127.0.0.1:8000/profile/orders`
- 💊 Prescription: `http://127.0.0.1:8000/prescription/upload`

### Shop Owner
- 📊 Dashboard: `http://127.0.0.1:8000/shop/dashboard`
- 📦 Inventory: `http://127.0.0.1:8000/shop/inventory`
- 📝 Orders: `http://127.0.0.1:8000/shop/orders`
- ⚙️ Settings: `http://127.0.0.1:8000/shop/settings`

### Admin
- 🎛️ Dashboard: `http://127.0.0.1:8000/admin`
- 🏪 Stores: `http://127.0.0.1:8000/admin/stores`
- 💊 Medicines: `http://127.0.0.1:8000/admin/medicines`
- 📦 Orders: `http://127.0.0.1:8000/admin/orders`
- 🎟️ Coupons: `http://127.0.0.1:8000/admin/coupons`

### Auth
- 🔐 Login: `http://127.0.0.1:8000/login`
- 📝 Register: `http://127.0.0.1:8000/register`
- 🏪 Shop Register: `http://127.0.0.1:8000/register/shop`

---

## 📂 Key Files & Their Purpose

| File | Purpose |
|------|---------|
| `.env` | Configuration (DB, Mail, API Keys) |
| `routes/web.php` | All URL routes |
| `app/Http/Controllers/HomeController.php` | Customer homepage & search |
| `app/Http/Controllers/ShopController.php` | Shop dashboard & inventory |
| `app/Http/Controllers/AdminController.php` | Admin panel |
| `app/Http/Controllers/OrderController.php` | Order processing |
| `app/Models/Order.php` | Order database model |
| `app/Models/Inventory.php` | Medicine inventory model |
| `resources/views/customer/home.blade.php` | Homepage UI |
| `database/seeders/MedicineSeeder.php` | 100 medicines data |
| `database/database.sqlite` | Local database file |

---

## 🗂️ Database Tables Cheatsheet

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | All users | email, password, role, phone |
| `shops` | Pharmacy stores | name, address, approved, online |
| `medicines` | Medicine master | name, description |
| `inventories` | Shop medicine stock | shop_id, medicine_id, price, stock |
| `orders` | Customer orders | user_id, shop_id, status, total_amount |
| `prescriptions` | Uploaded prescriptions | user_id, images, status |
| `coupons` | Discount codes | code, type, value, min_order |
| `wallets` | Shop earnings | shop_id, balance |
| `settings` | App configuration | key, value |

---

## 🔄 Order Status Flow

```
pending → processing → out_for_delivery → delivered
   ↓           ↓              ↓              ↓
Customer   Shop        Delivery Boy     Customer
 Places    Accepts      Out            Receives
 Order     Order      for Delivery      Medicine
```

**Status Values:**
- `pending` - Waiting for shop acceptance
- `processing` - Shop is preparing order
- `out_for_delivery` - Order shipped
- `delivered` - Order completed
- `cancelled` - Order cancelled

---

## 👥 User Roles & Access

| Role | Access | Default Login |
|------|--------|---------------|
| `customer` | Browse, Order, Profile | Register at `/register` |
| `shop_owner` | Inventory, Orders, Settings | Register shop at `/register/shop` |
| `admin` | Full platform control | Manually assign role in DB |

**Change User Role (via Tinker):**
```bash
php artisan tinker
User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

---

## 📧 Email Configuration

**Current Setup (Gmail SMTP):**
- Email: dawaloofficial@gmail.com
- Password: App-specific password (in `.env`)
- Port: 465 (SSL)

**Test Email:**
```bash
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

---

## 🔔 Push Notifications Setup

**VAPID Keys (Already Generated):**
- Public Key: In `.env` as `VAPID_PUBLIC_KEY`
- Private Key: In `.env` as `VAPID_PRIVATE_KEY`

**Test Notification:**
Visit: `http://127.0.0.1:8000/subscribe/test`

---

## 📱 WhatsApp Integration

**Quick Action URLs:**
```
/order/{id}/action/accept    - Accept order
/order/{id}/action/reject    - Reject order
/order/{id}/action/ready     - Mark ready for delivery
```

These URLs are sent via WhatsApp with secure tokens.

---

## 🐛 Common Issues & Fixes

### Issue: SQLite database not found
**Fix:**
```bash
touch database/database.sqlite
php artisan migrate
```

### Issue: 500 Error on production
**Fix:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
chmod -R 777 storage bootstrap/cache
```

### Issue: Images not uploading
**Fix:**
```bash
chmod -R 777 public/uploads
# Create directory if missing:
mkdir public/uploads/prescriptions
mkdir public/uploads/medicines
mkdir public/uploads/shops
```

### Issue: Emails not sending
**Check:**
1. `.env` has correct Gmail credentials
2. Gmail "Less secure apps" enabled OR use App Password
3. Port 465 not blocked by firewall

### Issue: Vite not building assets
**Fix:**
```bash
npm install
npm run build
```

---

## 📊 Database Queries (Quick Reference)

### Count Orders
```sql
SELECT COUNT(*) FROM orders;
```

### Find All Pending Orders
```sql
SELECT * FROM orders WHERE status = 'pending';
```

### List All Shop Owners
```sql
SELECT * FROM users WHERE role = 'shop_owner';
```

### Check Inventory Stock
```sql
SELECT m.name, i.stock, s.name as shop_name
FROM inventories i
JOIN medicines m ON i.medicine_id = m.id
JOIN shops s ON i.shop_id = s.id
WHERE i.stock < 10;
```

---

## 🎨 Frontend Assets

**Tailwind Classes (Most Used):**
- `bg-blue-500` - Blue background
- `text-white` - White text
- `rounded-lg` - Rounded corners
- `shadow-md` - Box shadow
- `p-4` - Padding (1rem)
- `mt-4` - Margin top (1rem)
- `flex items-center justify-between` - Flexbox

**Build Assets:**
```bash
npm run build      # Production build
npm run dev        # Watch mode
```

---

## 🚀 Deployment Checklist

### Before Uploading to Hostinger
- [ ] Update `.env` → MySQL settings
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build`
- [ ] Run `php artisan optimize`

### After Upload
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan cache:clear
chmod -R 777 storage bootstrap/cache
```

### FTP Details
- Host: `ftp://dawalo.com`
- Username: `u403768071`
- Upload to: `public_html`

---

## 🔐 Security Best Practices

**Before Production:**
1. Change `APP_KEY` → `php artisan key:generate`
2. Update all passwords in `.env`
3. Set `APP_DEBUG=false`
4. Enable HTTPS redirect (already in `.htaccess`)
5. Add rate limiting to API routes
6. Implement 2FA for admin

---

## 📞 Support Contacts

**Email:** dawaloofficial@gmail.com  
**Domain:** https://dawalo.com  
**Hosting:** Hostinger (India)  
**Server IP:** 147.93.109.171  

---

## 🎯 Quick Tips

1. **Reset Everything:**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Check Routes:**
   ```bash
   php artisan route:list
   ```

3. **Clear Cache:**
   ```bash
   php artisan optimize:clear
   ```

4. **View Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

5. **Database Console:**
   ```bash
   php artisan tinker
   # Then: User::all(), Order::count(), etc.
   ```

---

**Last Updated:** September 2, 2026  
**Version:** 1.0  
**Status:** ✅ Production Ready
