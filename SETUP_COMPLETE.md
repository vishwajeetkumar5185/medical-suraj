# Dawalo - Setup Complete ✅

## Local Development Environment

**Server Running:** http://127.0.0.1:8000

### Database Configuration
- **Type:** SQLite (for local development)
- **Location:** `database/database.sqlite`
- **Records:** 100 medicines, shops, and inventory seeded

### Quick Access URLs

**Public Pages:**
- Home: http://127.0.0.1:8000/
- Login: http://127.0.0.1:8000/login
- Register: http://127.0.0.1:8000/register
- Shop Register: http://127.0.0.1:8000/register/shop
- Search: http://127.0.0.1:8000/search
- Smart Cart: http://127.0.0.1:8000/smartcart

**User Pages:**
- Profile: http://127.0.0.1:8000/profile
- Orders: http://127.0.0.1:8000/profile/orders
- Addresses: http://127.0.0.1:8000/profile/addresses

**Shop Owner:**
- Dashboard: http://127.0.0.1:8000/shop/dashboard
- Inventory: http://127.0.0.1:8000/shop/inventory
- Orders: http://127.0.0.1:8000/shop/orders
- Settings: http://127.0.0.1:8000/shop/settings

**Admin:**
- Dashboard: http://127.0.0.1:8000/admin
- Orders: http://127.0.0.1:8000/admin/orders
- Medicines: http://127.0.0.1:8000/admin/medicines
- Stores: http://127.0.0.1:8000/admin/stores
- Approvals: http://127.0.0.1:8000/admin/approvals
- Coupons: http://127.0.0.1:8000/admin/coupons

### Production Deployment

**When deploying to dawalo.com:**

1. Update `.env` to use MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u403768071_medical
DB_USERNAME=u403768071_medical
DB_PASSWORD=Medical@2026
```

2. Upload files via FTP:
   - Host: ftp://dawalo.com
   - Username: u403768071
   - Upload to: public_html

3. Run migrations on server:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan cache:clear
```

### SSL Configuration
- ✅ SSL Active (Lifetime)
- ✅ HTTPS redirect configured in `.htaccess`
- ✅ APP_URL set to https://dawalo.com

### Server Details
- **IP:** 147.93.109.171
- **Location:** Asia (India)
- **PHP Workers:** 20
- **Disk Space:** 10 GB
