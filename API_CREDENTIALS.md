# PeeZ API - Test Credentials

## Production Railway URL
```
https://web-production-c1c0c.up.railway.app
```

## Test Users

### Admin
- **Email:** `admin@peez.dz`
- **Password:** `password` (Railway Production) / `password123` (Local)
- **Role:** admin
- **Use for:** Filament Admin Dashboard

### Customer (iOS Client App)
- **Email:** `customer@peez.dz`
- **Password:** `password` (Railway Production) / `password123` (Local)
- **Role:** customer
- **Use for:** Testing iOS Client App Login

### Vendor (iOS Shop App)
- **Email:** `vendor@shop1.dz`
- **Password:** `password` (Railway Production) / `password123` (Local)
- **Role:** vendor
- **Shop:** Fashion Store Oran
- **Use for:** Testing iOS Shop App Login

### Manager
- **Email:** `manager@peez.dz`
- **Password:** `password` (Railway Production) / `password123` (Local)
- **Role:** manager

---

## API Endpoints

### Authentication
```bash
# Customer Login
curl -X POST https://web-production-c1c0c.up.railway.app/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"customer@peez.dz","password":"password"}'

# Vendor Login
curl -X POST https://web-production-c1c0c.up.railway.app/api/v1/auth/vendor/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"vendor@shop1.dz","password":"password"}'
```

### Public Endpoints (No Auth Required)
```bash
# Get Neighborhoods
curl https://web-production-c1c0c.up.railway.app/api/v1/neighborhoods \
  -H "Accept: application/json"

# Get Categories
curl https://web-production-c1c0c.up.railway.app/api/v1/categories \
  -H "Accept: application/json"
```

---

## Important Notes

✅ **ALWAYS include `Accept: application/json` header** in ALL API requests
- Without this header, Laravel will return HTML redirects instead of JSON
- iOS Apps already include this header in APIService

🔒 **Password Changes:**
- Railway Production uses `password` (already seeded)
- Local Development uses `password123` (from UserSeeder.php)
- To update Railway passwords, run manually via Railway CLI:
  ```bash
  railway run php artisan migrate:fresh --seed
  ```

📱 **iOS Apps Configuration:**
- Client App API URL: `https://web-production-c1c0c.up.railway.app/api/v1`
- Shop App API URL: `https://web-production-c1c0c.up.railway.app/api/v1`
- Both already configured in `Utils/Theme.swift`

---

## Admin Dashboard

**URL:** https://web-production-c1c0c.up.railway.app/admin/login

**Credentials:**
- Email: `admin@peez.dz`
- Password: `password`

**Features:**
- Manage Users, Shops, Categories, Neighborhoods
- View Subscriptions & Activations
- Shop Ratings Management
