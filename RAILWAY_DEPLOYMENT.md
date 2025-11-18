# PeeZ Dashboard - Railway Deployment Guide

## 🚀 Deployment Steps

### 1. Create Railway Project
1. Go to https://railway.app
2. Click "Start a New Project"
3. Select "Deploy from GitHub repo"
4. Connect your GitHub account
5. Select your PeeZ repository

### 2. Add MySQL Database
1. In Railway dashboard, click "+ New"
2. Select "Database" → "MySQL"
3. Railway will automatically create the database
4. Copy the database credentials (you'll need these)

### 3. Environment Variables (WICHTIG!)

Gehe zu Project Settings → Variables und füge hinzu:

```env
APP_NAME=PeeZ
APP_ENV=production
APP_KEY=<generiere mit: php artisan key:generate --show>
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database (Railway gibt dir diese automatisch)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# CORS (für iOS Apps)
SANCTUM_STATEFUL_DOMAINS=
SESSION_DOMAIN=

# Wichtig für API
L5_SWAGGER_GENERATE_ALWAYS=false
```

### 4. Build Configuration

Railway erkennt Laravel automatisch und:
- Installiert Composer dependencies
- Führt `composer install --optimize-autoloader --no-dev` aus
- Führt Artisan-Befehle aus

### 5. Nach dem Deployment

**Wichtig! Führe diese Befehle in Railway CLI aus:**

```bash
# Railway CLI installieren (einmalig)
npm i -g @railway/cli

# Login
railway login

# Mit Projekt verbinden
railway link

# Befehle ausführen
railway run php artisan migrate --force --seed
railway run php artisan storage:link
railway run php artisan optimize
```

### 6. Domain Setup

**Option A: Railway Domain (kostenlos)**
- Railway gibt dir: `your-app.up.railway.app`
- SSL automatisch aktiviert ✅

**Option B: Custom Domain**
1. Railway Dashboard → Settings → Domains
2. Add Custom Domain
3. Configure DNS bei deinem Provider:
   - CNAME record: `your-domain.com` → `your-app.up.railway.app`

### 7. iOS Apps updaten

Ändere in beiden Apps die API URL:

**Client App:**
`/Peez iOS/Utils/Theme.swift:111`
```swift
static let apiBaseURL = "https://your-app.up.railway.app/api/v1"
```

**Shop App:**
`/Peez Shop/Utils/Theme.swift:109`
```swift
static let apiBaseURL = "https://your-app.up.railway.app/api/v1"
```

### 8. Testing

```bash
# Test API
curl https://your-app.up.railway.app/api/v1/neighborhoods

# Test Login
curl -X POST https://your-app.up.railway.app/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@peez.dz","password":"password123"}'
```

## 💰 Kosten

**Railway Free Tier:**
- $5 kostenlos pro Monat
- Genug für Development/Testing
- Danach: $0.000463/GB-second

**Geschätzte monatliche Kosten:**
- Kleine App: ~$5-10/Monat
- Mit MySQL: ~$10-15/Monat

## 🔧 Troubleshooting

**Problem: "No application encryption key has been specified"**
```bash
railway run php artisan key:generate --show
# Kopiere den Key und füge ihn als APP_KEY Variable hinzu
```

**Problem: Migration errors**
```bash
railway run php artisan migrate:fresh --force --seed
```

**Problem: Storage errors**
```bash
railway run php artisan storage:link
railway run chmod -R 775 storage bootstrap/cache
```

**Problem: API nicht erreichbar**
- Prüfe APP_URL in .env
- Prüfe ob MySQL läuft
- Check Railway Logs

## 📱 Nach Deployment

1. ✅ Test alle API Endpoints
2. ✅ Update iOS App URLs
3. ✅ Test auf echtem iPhone
4. ✅ Submit zu App Store

## 🎯 Production Checklist

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Database Backup einrichten
- [ ] Error Monitoring (Sentry/Bugsnag)
- [ ] API Rate Limiting aktiviert
- [ ] CORS richtig konfiguriert
- [ ] SSL/HTTPS funktioniert
- [ ] Alle Secrets in Railway Variables
