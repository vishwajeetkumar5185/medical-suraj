# Testing Guide - Dawalo Improvements 🧪

**Date:** September 2, 2026

## 🚀 Quick Start

### 1. Start the Development Server

```bash
cd c:\xampp\htdocs\public_html (8)
php artisan serve
```

Server will start at: **http://127.0.0.1:8000**

---

## ✅ Test Cases

### Test 1: Location-Based Pharmacy Sorting

**Steps:**
1. Open browser: `http://127.0.0.1:8000`
2. Browser will ask for location permission → **Click "Allow"**
3. Wait 2-3 seconds for location to be detected
4. Scroll to "Nearby Pharmacies" section

**Expected Results:**
- ✅ Location name updates in header (e.g., "Muzaffarpur, Bihar")
- ✅ Each pharmacy card shows distance badge: "🚗 X.X km away"
- ✅ Pharmacies are sorted from nearest to farthest
- ✅ First pharmacy should be closest

**Screenshot Check:**
- Distance shown in blue badge
- Format: "🚗 2.3 km away" (or similar)

---

### Test 2: Popular Medicines - View All

**Steps:**
1. On homepage, find "Popular Dawaiyan" section
2. Click **"View All ›"** link (top-right of section)

**Expected Results:**
- ✅ Redirects to: `http://127.0.0.1:8000/popular-medicines`
- ✅ Shows grid of medicines (2 columns)
- ✅ Each medicine shows:
  - Medicine name
  - Category
  - Price (with discount if applicable)
  - "Add to Cart" button
- ✅ Top 3 medicines have "HOT 🔥" badge
- ✅ Pagination buttons at bottom

**Actions to Test:**
1. Click "Add to Cart" on any medicine
   - Button should change to "Adding..."
   - Then show "✓ Added" with green background
   - After 1.5 seconds, return to normal
2. Click "Next →" for pagination
   - Should load next page of medicines
3. Click "← Previous" to go back
   - Should load previous page

---

### Test 3: Nearby Pharmacies - View All

**Steps:**
1. On homepage, find "Nearby Pharmacies" section
2. Click **"View All ›"** link (top-right of section)

**Expected Results:**
- ✅ Redirects to: `http://127.0.0.1:8000/nearby-pharmacies`
- ✅ Header shows "📍 Sorted by distance" (if location enabled)
- ✅ Shows all pharmacies in list format
- ✅ Each pharmacy card shows:
  - Shop name and image
  - Distance: "🚗 X.X km away"
  - Rating (stars)
  - Open/Closed status (🟢 Open Now / 🔴 Closed)
  - Delivery availability (🛵 Delivery)
  - Shop timings (e.g., "🕐 9:00 AM - 9:00 PM")
- ✅ Top 3 pharmacies have "TOP 1", "TOP 2", "TOP 3" badges
- ✅ Sorted by distance (nearest first)

**If Location Disabled:**
- ✅ Yellow warning box at top: "Enable Location"
- ✅ Click "Enable Now" button
- ✅ Browser asks for permission
- ✅ After allowing, page reloads with distances

---

### Test 4: Shop by Category

**Steps:**
1. On homepage, find "Shop by Category" section
2. Click any category card (e.g., "Cold & Cough")

**Expected Results:**
- ✅ Redirects to search page with category filter
- ✅ URL: `http://127.0.0.1:8000/search?q=Cold` (or respective category)
- ✅ Shows medicines matching that category
- ✅ Search bar pre-filled with category name

**Categories to Test:**
- 🤧 Cold & Cough → `/search?q=Cold`
- 🌡️ Fever & Pain → `/search?q=Fever`
- 💊 Pain Relief → `/search?q=Pain`
- ❤️ Heart Care → `/search?q=Heart`
- 🩸 Diabetic → `/search?q=Diabetes`
- 🩺 Blood Pressure → `/search?q=Blood Pressure`

---

### Test 5: Distance Accuracy (Optional)

**To verify distance calculation is correct:**

1. Find a shop's actual address (from database or UI)
2. Get your current location from browser
3. Use Google Maps to check real distance
4. Compare with Dawalo's displayed distance
5. Should be within ±0.5 km tolerance

**Example:**
- Your location: 25.8756° N, 85.1823° E (Muzaffarpur)
- Shop location: 25.8800° N, 85.1900° E
- Expected distance: ~1.2 km
- Dawalo shows: 1.1-1.3 km ✅

---

## 🐛 Common Issues & Solutions

### Issue 1: Location Not Detected

**Symptoms:**
- No distance shown on pharmacy cards
- Location still shows "Detecting..."

**Solutions:**
1. Check browser location permission (should be "Allow")
2. Try different browser (Chrome/Edge recommended)
3. Make sure you're on HTTPS or localhost
4. Check browser console for errors (F12)
5. Manually reload page after allowing permission

---

### Issue 2: Distance Shows "9999 km"

**Cause:** Shop doesn't have latitude/longitude in database

**Solution:**
- Admin needs to update shop location
- Or shop owner can update from shop settings

**Temporary Workaround:**
- These shops will appear at bottom of list
- Feature still works for other shops

---

### Issue 3: "View All" Shows Empty Page

**Cause:** No medicines in database with those categories

**Solution:**
```bash
php artisan migrate:fresh --seed
```
This will reset database and add 100 sample medicines.

---

### Issue 4: Add to Cart Not Working

**Check:**
1. Browser console for errors (F12)
2. CSRF token present in form
3. Route exists: `php artisan route:list | grep cart`

**Quick Fix:**
```bash
php artisan optimize:clear
```

---

## 📊 Database Verification

### Check if shops have locations:

```bash
php artisan tinker
```

Then run:
```php
Shop::all(['id', 'name', 'latitude', 'longitude']);
```

**Expected Output:**
```
[
  {
    "id": 1,
    "name": "Sharma Medical Store",
    "latitude": "25.8756",
    "longitude": "85.1823"
  },
  ...
]
```

**If latitude/longitude are NULL:**
- Update them manually in database
- Or via admin panel
- Or via shop settings

---

## 🎯 Success Criteria

**All features working if:**

1. ✅ Distance shown on pharmacy cards (when location enabled)
2. ✅ Pharmacies sorted by distance (nearest first)
3. ✅ "View All" links work for both sections
4. ✅ Popular medicines page loads with grid layout
5. ✅ Nearby pharmacies page shows all shops sorted
6. ✅ Shop by category redirects to search
7. ✅ Add to cart works with loading state
8. ✅ Pagination works on popular medicines page
9. ✅ Distance updates when location changes
10. ✅ No console errors in browser (F12)

---

## 🔧 Manual Testing Checklist

Print this and check off as you test:

**Homepage:**
- [ ] Location detected and shown in header
- [ ] Popular Dawaiyan section visible
- [ ] Shop by Category section visible
- [ ] Nearby Pharmacies section visible
- [ ] Distance badges on pharmacy cards
- [ ] All "View All" links present

**Popular Medicines Page:**
- [ ] Grid layout (2 columns mobile)
- [ ] Medicine images or emojis
- [ ] Prices with discount %
- [ ] "Add to Cart" button works
- [ ] "HOT 🔥" badges on top 3
- [ ] Pagination controls
- [ ] Back button works

**Nearby Pharmacies Page:**
- [ ] All pharmacies listed
- [ ] Distance badges shown
- [ ] "TOP" badges on first 3
- [ ] Open/Closed status correct
- [ ] Shop timings displayed
- [ ] Delivery badges shown
- [ ] Sorted by distance
- [ ] "Enable Location" prompt (if needed)
- [ ] Back button works

**Search/Category:**
- [ ] Category click → search page
- [ ] Correct medicines shown
- [ ] Search bar pre-filled

---

## 📱 Mobile Testing

**Test on different screen sizes:**

1. **Small Mobile (360px)**
   - Open browser DevTools (F12)
   - Toggle device toolbar
   - Select "Galaxy S8+" or similar
   - Test all pages

2. **Large Mobile (428px)**
   - Select "iPhone 12 Pro Max"
   - Test all pages

3. **Tablet (768px)**
   - Select "iPad"
   - Verify layout still looks good

**Check:**
- ✅ No horizontal scroll
- ✅ Text readable (not too small)
- ✅ Buttons easily tappable (48px minimum)
- ✅ Images not distorted
- ✅ Grid adapts to screen size

---

## 🚀 Performance Testing

### Load Time Test:

1. Open browser DevTools (F12)
2. Go to "Network" tab
3. Reload homepage
4. Check "Load" time at bottom

**Target:**
- Homepage: < 1 second
- Popular Medicines: < 1.5 seconds
- Nearby Pharmacies: < 1 second

**If slow:**
- Clear cache: `php artisan optimize:clear`
- Check internet connection
- Verify database not too large

---

## 📞 Need Help?

**If something doesn't work:**

1. Check console errors (F12)
2. Clear cache: `php artisan optimize:clear`
3. Check routes: `php artisan route:list`
4. Verify database: `php artisan tinker`
5. Restart server

**Still stuck?**
- Read `IMPROVEMENTS_MADE.md` for technical details
- Check `PROJECT_ANALYSIS.md` for architecture
- Review error logs: `storage/logs/laravel.log`

---

**Last Updated:** September 2, 2026  
**Status:** Ready for Testing ✅  
**Estimated Test Time:** 15-20 minutes
