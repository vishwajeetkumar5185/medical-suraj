# 💊 Dawalo - Medicine Delivery Platform

**Live URL:** https://dawalo.com  
**Status:** Production Ready ✅

## 📌 About

Dawalo is a multi-vendor medicine delivery platform connecting customers with local pharmacies. Built with Laravel 12, it provides real-time inventory management, prescription uploads, location-based pharmacy discovery, and WhatsApp integration.

## ✨ Features

### 🛒 Customer Features
- **Smart Search** - Find medicines across multiple pharmacies
- **Location-Based Discovery** - Nearest pharmacies with distance calculation
- **Prescription Upload** - Order via prescription image
- **Smart Cart** - Multi-item ordering with intelligent shop selection
- **Order Tracking** - Real-time status updates
- **Web Push Notifications** - Stay updated on orders

### 🏪 Shop Owner Features
- **Dashboard** - Sales overview and order management
- **Inventory Management** - Add/update medicine stock with images
- **Bulk Upload** - Excel import for quick inventory setup
- **Order Processing** - Accept, prepare, and deliver orders
- **Shop Settings** - Timings, delivery radius, and preferences

### 👨‍💼 Admin Features
- **Platform Management** - Approve/block shops and manage orders
- **Medicine Master** - Global medicine database
- **Order Assignment** - Manual shop assignment if needed
- **Coupon Management** - Create and manage discount codes
- **Analytics** - Revenue tracking and commission management

## 🛠️ Tech Stack

**Backend:**
- Laravel 12 (PHP 8.2+)
- SQLite (Development) / MySQL (Production)
- Queue System for background jobs

**Frontend:**
- Blade Templates (Server-side rendering)
- TailwindCSS 4.0
- Vanilla JavaScript
- Vite Build System

**External Services:**
- Gmail SMTP (Email delivery)
- Web Push API (Notifications)
- WhatsApp Business API (Order updates)

## 🚀 Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (for local) or MySQL (for production)

### Local Setup

```bash
# Clone repository
git clone https://github.com/vishwajeetkumar5185/medical-suraj.git
cd medical-suraj

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

Visit: http://127.0.0.1:8000

### Quick Setup Script

```bash
composer run setup
```

This runs: install, env setup, key generation, migration, and asset building.

## 📁 Project Structure

```
dawalo/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php      # Customer pages
│   │   ├── ShopController.php      # Shop owner dashboard
│   │   ├── AdminController.php     # Admin panel
│   │   └── OrderController.php     # Order management
│   ├── Models/
│   │   ├── User.php
│   │   ├── Shop.php
│   │   ├── Medicine.php
│   │   ├── Order.php
│   │   └── Inventory.php
│   └── Mail/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── customer/               # Customer-facing views
│       ├── shop/                   # Shop owner views
│       └── admin/                  # Admin panel views
├── routes/
│   └── web.php                     # Application routes
└── public/
    └── uploads/                    # User-uploaded images
```

## 🔧 Configuration

### Environment Variables

Key variables in `.env`:

```env
APP_NAME=Dawalo
APP_URL=https://dawalo.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl

# Web Push Notifications
VAPID_PUBLIC_KEY=your-public-key
VAPID_PRIVATE_KEY=your-private-key
VAPID_SUBJECT=mailto:support@dawalo.com
```

### Production Deployment

For production with MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_ENV=production
APP_DEBUG=false
```

Then run:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan optimize
```

## 🎯 Key Features Implementation

### Location-Based Distance Calculation

Uses Haversine formula to calculate real distance between user and pharmacies:

```php
// Sorts pharmacies by distance from user location
$shops->sortBy('distance')
```

Distance displayed on UI: **"🚗 2.3 km away"**

### Smart Cart System

Automatically selects optimal pharmacy based on:
- Stock availability
- Distance from user
- Delivery availability
- Shop rating

### WhatsApp Integration

Order notifications sent via WhatsApp with quick action links:
- Accept order
- Reject order
- Mark ready for delivery

## 📱 API Routes

### Public Routes
- `GET /` - Homepage
- `GET /search` - Medicine search
- `GET /popular-medicines` - Popular medicines page
- `GET /nearby-pharmacies` - Nearby pharmacies with distance
- `POST /cart/add` - Add to cart
- `POST /cart/apply-coupon` - Apply discount coupon

### Authenticated Routes
- `POST /order` - Place order
- `GET /order/{id}/status` - Order status
- `POST /prescription/upload` - Upload prescription
- `GET /profile` - User profile
- `GET /profile/orders` - Order history

### Shop Owner Routes
- `GET /shop/dashboard` - Dashboard
- `GET /shop/inventory` - Inventory management
- `POST /shop/inventory/add` - Add medicine
- `POST /shop/inventory/bulk-upload` - Excel upload
- `POST /shop/order/status` - Update order status

### Admin Routes
- `GET /admin` - Admin dashboard
- `GET /admin/stores` - Manage shops
- `POST /admin/stores/status` - Approve/block shops
- `GET /admin/medicines` - Medicine master
- `GET /admin/orders` - All orders
- `POST /admin/coupons/add` - Create coupon

## 🧪 Testing

### Manual Testing

Test helper pages:
- http://127.0.0.1:8000/test-features.html
- http://127.0.0.1:8000/set-test-location.html

### Run Tests

```bash
composer test
# or
php artisan test
```

## 📚 Documentation

- `WHAT_CHANGED.md` - Recent improvements summary
- `IMPROVEMENTS_MADE.md` - Technical changes documentation
- `PROJECT_ANALYSIS.md` - Complete project analysis
- `FINAL_TEST_GUIDE.md` - Testing instructions
- `README_TESTING.md` - Quick testing guide (Hindi)
- `QUICK_REFERENCE.md` - Commands and tips

## 🐛 Troubleshooting

### Clear Cache

```bash
php artisan optimize:clear
```

### Reset Database

```bash
php artisan migrate:fresh --seed
```

### Fix Permissions

```bash
chmod -R 777 storage bootstrap/cache
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

## 🔐 Security

- CSRF protection enabled
- Password hashing (bcrypt)
- SQL injection protection (Eloquent ORM)
- XSS protection
- HTTPS enforced in production

**Todo:**
- [ ] API rate limiting
- [ ] 2FA for admin accounts
- [ ] File upload malware scanning

## 📈 Performance

- Database indexes on frequently queried columns
- Session driver: database (for distributed systems)
- Queue system for emails/notifications
- TailwindCSS purged in production
- Vite for optimized asset bundling

## 🤝 Contributing

This is a private project. For access or contributions, contact the development team.

## 📞 Support

**Email:** dawaloofficial@gmail.com  
**Website:** https://dawalo.com  
**Hosting:** Hostinger (India)  
**Server IP:** 147.93.109.171

## 📄 License

Proprietary - All rights reserved

## 👥 Team

**Developer:** Vishwajeet Kumar  
**GitHub:** https://github.com/vishwajeetkumar5185

---

**Version:** 1.0  
**Last Updated:** September 2, 2026  
**Status:** ✅ Production Ready

---

### Recent Updates (September 2, 2026)

- ✅ Added location-based distance calculation
- ✅ Created Popular Medicines page
- ✅ Created Nearby Pharmacies page
- ✅ Improved homepage with distance badges
- ✅ Added Haversine formula for accurate distance
- ✅ Created comprehensive testing documentation

For detailed changes, see `WHAT_CHANGED.md`
