# 🎯 PeeZ API - Vollständige Endpoint-Übersicht

**API Version**: v1.0  
**Base URL**: `/api/v1`  
**Datum**: 4. November 2025

---

## 📋 Komplette Endpoint-Liste

### Legende:
- ✅ **Implementiert** - Fertig und getestet
- ⚠️ **Teilweise** - Existiert, aber braucht Anpassungen
- ❌ **Fehlt** - Muss noch implementiert werden

---

## 🌍 PUBLIC ENDPOINTS (Keine Auth erforderlich)

### Neighborhoods
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/neighborhoods` | ✅ | Liste aller Stadtteile |

**Aktuell implementiert**: ✅  
**Controller**: `Api\NeighborhoodController@index`

```json
// Response
{
  "data": [
    {
      "id": 1,
      "name": "Bab Ezzouar",
      "city": "Oran"
    }
  ]
}
```

---

### Categories
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/categories` | ✅ | Liste aller Kategorien |

**Aktuell implementiert**: ✅  
**Controller**: `Api\CategoryController@index`

```json
// Response
{
  "data": [
    {
      "id": 1,
      "name": "Grocery",
      "slug": "grocery",
      "shopsCount": 45
    }
  ]
}
```

---

### Shops
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/shops` | ✅ | Shops mit Filtern |
| GET | `/shops/{id}` | ✅ | Einzelner Shop |
| GET | `/shops/nearby` | ✅ | GPS-basierte Suche |
| GET | `/shops/neighborhood/{id}` | ✅ | Shops nach Stadtteil |
| GET | `/shops/category/{id}` | ✅ | Shops nach Kategorie |

**Aktuell implementiert**: ✅  
**Controller**: `Api\ShopController`

```json
// GET /shops?neighborhoodId=1&categoryId=2
{
  "data": [
    {
      "id": 1,
      "name": "Super Marché Oran",
      "discountPercent": 7.5,
      "avgRating": 4.5,
      "ratingsCount": 23,
      "lat": 35.6976,
      "lng": -0.6337,
      "phone": "+213555111222",
      "isActive": true,
      "neighborhood": {
        "id": 1,
        "name": "Bab Ezzouar",
        "city": "Oran"
      },
      "category": {
        "id": 2,
        "name": "Grocery",
        "slug": "grocery"
      }
    }
  ],
  "meta": {
    "currentPage": 1,
    "perPage": 20,
    "total": 85
  }
}
```

---

## 👤 USER ENDPOINTS

### Subscription Status
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/users/{uuid}/subscription` | ⚠️ | Abo-Status eines Users |

**Aktuell**: `GET /subscriptions/status` (braucht User UUID)  
**Anpassung nötig**: Route umbenennen zu `/users/{uuid}/subscription`

```json
// Response
{
  "data": {
    "id": 1,
    "userId": 123,
    "status": "active",
    "startAt": "2025-01-01T00:00:00Z",
    "endAt": "2025-04-01T00:00:00Z",
    "source": "vendor"
  }
}
```

---

### Membership Card
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/users/{uuid}/card` | ❌ | Mitgliedskarte + QR Code |

**Aktuell**: Nicht implementiert  
**Neu erstellen**: `UserController@card`

```json
// Response
{
  "data": {
    "membershipId": "PEEZ-2025-001234",
    "qrPayload": "eyJ1dWlkIjoiNTUwZTg0MDAtZTI5Yi00MWQ0LWE3MTYtNDQ2NjU1NDQwMDAwIiwic3RhdHVzIjoiYWN0aXZlIn0=",
    "status": "active",
    "validUntil": "2025-04-01T00:00:00Z"
  }
}
```

---

## ⭐ RATING ENDPOINTS

| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/ratings` | ✅ | Shop bewerten (1-5 Sterne) |
| GET | `/ratings` | ✅ | Ratings für einen Shop |
| GET | `/ratings/my-ratings` | ✅ | Eigene Ratings |

**Aktuell implementiert**: ✅  
**Controller**: `Api\RatingController`

```json
// POST /ratings
{
  "shopId": 1,
  "stars": 5
}

// Response
{
  "data": {
    "ratingId": 45,
    "shopId": 1,
    "stars": 5,
    "newAverage": 4.6,
    "totalRatings": 24
  }
}
```

---

## 🔐 AUTHENTICATION ENDPOINTS

### General Auth
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/auth/register` | ✅ | User registrieren |
| POST | `/auth/login` | ✅ | User login |
| POST | `/auth/logout` | ✅ | User logout |
| GET | `/auth/me` | ✅ | Aktueller User |
| PUT | `/auth/profile` | ✅ | Profil aktualisieren |
| POST | `/auth/fcm-token` | ✅ | FCM Token update |
| POST | `/auth/forgot-password` | ✅ | Passwort zurücksetzen |

**Aktuell implementiert**: ✅  
**Controller**: `Api\AuthController`

---

### Vendor Auth
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/auth/vendor/login` | ❌ | Vendor POS Login |

**Aktuell**: Verwendet generischen `/auth/login`  
**Neu erstellen**: Spezialisierter Vendor-Login mit shopId

```json
// POST /auth/vendor/login
{
  "phone": "+213555123456",
  "password": "secret"
}

// Response
{
  "token": "1|laravel_sanctum_token",
  "vendorId": 5,
  "shopId": 12
}
```

---

## 🏪 VENDOR POS ENDPOINTS (auth:sanctum)

### Activation
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/vendor/activate` | ❌ | User Abo aktivieren/verlängern |

**Header erforderlich**: `Idempotency-Key: {uuid}`

```json
// POST /vendor/activate
{
  "userUuid": "550e8400-e29b-41d4-a716-446655440000",
  "months": 3,
  "shopId": 12  // optional
}

// Response
{
  "data": {
    "status": "active",
    "startAt": "2025-01-15T00:00:00Z",
    "endAt": "2025-04-15T00:00:00Z",
    "activationId": 123,
    "amountDzd": 900
  }
}
```

**Business Logic**:
- Wenn User hat aktives Abo → `endAt` verlängern um X Monate
- Wenn User hat kein aktives Abo → Neues erstellen ab heute
- Idempotent via `Idempotency-Key` Header
- Activation Log: user_id, shop_id, vendor_id, months, amount (300*months)

---

### Activations List
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/vendor/activations` | ❌ | Vendor's Aktivierungen |

**Query Parameters**: `?month=2025-01`

```json
// GET /vendor/activations?month=2025-01
{
  "data": [
    {
      "id": 123,
      "userId": 456,
      "userName": "Ahmed Mohamed",
      "shopId": 12,
      "vendorId": 5,
      "months": 3,
      "amountDzd": 900,
      "createdAt": "2025-01-15T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 45,
    "count": 20,
    "perPage": 20,
    "currentPage": 1,
    "totalPages": 3,
    "next": "/vendor/activations?month=2025-01&page=2"
  }
}
```

---

### Quick Status Check
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/vendor/users/{uuid}/status` | ❌ | Schnelle Validierung an der Kasse |

```json
// GET /vendor/users/{uuid}/status
{
  "data": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "hasActiveSubscription": true,
    "validUntil": "2025-04-15T00:00:00Z"
  }
}
```

---

## 🛡️ ADMIN CRUD ENDPOINTS (auth:sanctum)

### Neighborhoods Management
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/neighborhoods` | ⚠️ | Liste (über Filament) |
| POST | `/admin/neighborhoods` | ❌ | Erstellen |
| PUT | `/admin/neighborhoods/{id}` | ❌ | Aktualisieren |
| DELETE | `/admin/neighborhoods/{id}` | ❌ | Löschen |

**Aktuell**: CRUD über Filament Panel verfügbar  
**API Endpoints**: Müssen noch erstellt werden

---

### Categories Management
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/categories` | ⚠️ | Liste (über Filament) |
| POST | `/admin/categories` | ❌ | Erstellen |
| PUT | `/admin/categories/{id}` | ❌ | Aktualisieren |
| DELETE | `/admin/categories/{id}` | ❌ | Löschen |

---

### Shops Management
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/shops` | ⚠️ | Liste (über Filament) |
| POST | `/admin/shops` | ❌ | Erstellen (mit Validierung) |
| GET | `/admin/shops/{id}` | ❌ | Details |
| PUT | `/admin/shops/{id}` | ❌ | Aktualisieren |
| DELETE | `/admin/shops/{id}` | ❌ | Löschen |

**Validierung**:
- `discountPercent` muss zwischen 5.0 und 8.0 sein
- UNIQUE constraint: (neighborhood_id, category_id)

---

## 📊 ADMIN REPORTS ENDPOINTS (auth:sanctum)

### Coverage Summary
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/coverage/summary` | ❌ | Welche Stadtteile haben Partner-Shops |

```json
// GET /admin/coverage/summary
{
  "data": {
    "totalNeighborhoods": 10,
    "totalCategories": 12,
    "possibleCombinations": 120,
    "actualShops": 85,
    "coveragePercent": 70.8,
    "byNeighborhood": [
      {
        "neighborhoodId": 1,
        "neighborhoodName": "Bab Ezzouar",
        "coveredCategories": 10,
        "totalCategories": 12,
        "missingCategories": ["Beauty Salon", "Hair Salon"]
      }
    ]
  }
}
```

---

### Activations Report
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/reports/activations` | ❌ | Monatlicher Aktivierungs-Report |

**Query Parameters**: `?month=2025-01`

```json
// GET /admin/reports/activations?month=2025-01
{
  "data": {
    "month": "2025-01",
    "totalActivations": 450,
    "totalRevenueDzd": 135000,
    "byShop": [
      {
        "shopId": 1,
        "shopName": "Super Marché Oran",
        "activationsCount": 45,
        "revenueDzd": 13500,  // activationsCount * 300
        "avgStars": 4.5,
        "neighborhood": "Bab Ezzouar",
        "category": "Grocery"
      }
    ]
  }
}
```

**Calculation**: `revenueDzd = activationsCount * 300`

---

### Ratings Summary
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| GET | `/admin/ratings/summary` | ❌ | Rating-Statistiken |

**Query Parameters**: `?shopId=1`

```json
// GET /admin/ratings/summary?shopId=1
{
  "data": {
    "shopId": 1,
    "shopName": "Super Marché Oran",
    "totalRatings": 23,
    "avgStars": 4.5,
    "distribution": {
      "1": 0,
      "2": 1,
      "3": 2,
      "4": 8,
      "5": 12
    }
  }
}
```

---

### Push Campaign
| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/admin/campaigns/push` | ❌ | Push-Benachrichtigung senden |

```json
// POST /admin/campaigns/push
{
  "segment": {
    "neighborhoodId": 1,
    "categoryId": 2,
    "shopId": null
  },
  "title": "Neuer Partner Shop!",
  "body": "Schau dir unseren neuen Grocery-Partner in Bab Ezzouar an"
}

// Response
{
  "data": {
    "campaignId": 789,
    "recipientsCount": 1234,
    "sentAt": "2025-01-15T14:30:00Z"
  }
}
```

---

## 🔔 NOTIFICATION ENDPOINTS

| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/notifications/push` | ❌ | Push-Benachrichtigung (intern) |

```json
// POST /notifications/push
{
  "userUuid": "550e8400-e29b-41d4-a716-446655440000",
  "title": "Abo verlängert",
  "body": "Dein Abo wurde um 3 Monate verlängert"
}
```

---

## 💳 PAYMENT WEBHOOKS

| Method | Endpoint | Status | Beschreibung |
|--------|----------|--------|--------------|
| POST | `/webhooks/slickpay` | ⚠️ | SlickPay Webhook |
| POST | `/webhooks/cib` | ⚠️ | CIB Webhook |

**Aktuell**: Placeholder Routes existieren  
**Business Logic**: Muss implementiert werden

```json
// POST /webhooks/slickpay
{
  "transactionId": "SLICK-2025-123456",
  "status": "completed",
  "amount": 900,
  "metadata": {
    "userUuid": "550e8400-e29b-41d4-a716-446655440000",
    "months": 3
  }
}
```

---

## 📊 ENDPOINT SUMMARY

### Status Übersicht:

```
✅ Implementiert:        22 Endpoints
⚠️ Teilweise:            6 Endpoints (CRUD über Filament)
❌ Fehlt komplett:       12 Endpoints
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
TOTAL:                   40 Endpoints
```

### Nach Kategorie:

| Kategorie | Implementiert | Teilweise | Fehlt | Total |
|-----------|--------------|-----------|-------|-------|
| Public | 7 | 0 | 0 | **7** |
| User | 0 | 1 | 1 | **2** |
| Auth | 7 | 0 | 1 | **8** |
| Ratings | 3 | 0 | 0 | **3** |
| Vendor POS | 0 | 0 | 3 | **3** |
| Admin CRUD | 0 | 6 | 3 | **9** |
| Admin Reports | 0 | 0 | 4 | **4** |
| Notifications | 0 | 0 | 1 | **1** |
| Webhooks | 0 | 2 | 0 | **2** |
| **TOTAL** | **22** | **6** | **12** | **40** |

---

## 🎯 Prioritäten für fehlende Endpoints

### 🔴 HIGH Priority (Business Critical)
1. `POST /vendor/activate` - Kernfunktionalität
2. `GET /vendor/activations` - Vendor Dashboard
3. `GET /vendor/users/{uuid}/status` - POS Validierung
4. `GET /users/{uuid}/card` - Mitgliedskarte

### 🟡 MEDIUM Priority (Wichtig)
5. `GET /admin/coverage/summary` - Business Intelligence
6. `GET /admin/reports/activations` - Umsatz-Tracking
7. `POST /admin/campaigns/push` - Marketing
8. Admin CRUD APIs (9 Endpoints)

### 🟢 LOW Priority (Future)
9. `POST /webhooks/slickpay` - Online-Zahlungen
10. `GET /admin/ratings/summary` - Analytics
11. `POST /notifications/push` - Intern

---

## 🔧 Technische Anforderungen

### JSON Format
- **Response**: camelCase
- **Database**: snake_case
- **Transformation**: Automatisch via Middleware/Resources

### Error Format (RFC7807)
```json
{
  "type": "https://api.peez.dz/problems/validation-error",
  "title": "Validation Error",
  "status": 422,
  "detail": "The stars field must be between 1 and 5",
  "code": "VALIDATION_ERROR",
  "key": "stars"
}
```

### Pagination
```json
{
  "data": [...],
  "pagination": {
    "total": 100,
    "count": 20,
    "perPage": 20,
    "currentPage": 1,
    "totalPages": 5,
    "next": "/endpoint?page=2"
  }
}
```

### Rate Limiting
- **60 requests per minute** per token
- Header: `X-RateLimit-Remaining: 45`

### Idempotency
- **Header**: `Idempotency-Key: {uuid}`
- **Gilt für**: POST /vendor/activate

---

## 📚 Dokumentation

| Datei | Beschreibung |
|-------|--------------|
| `docs/openapi.yaml` | ✅ OpenAPI 3.1 Spec (vollständig) |
| `API_DOCUMENTATION.md` | ⚠️ Markdown Docs (alt) |
| `POSTMAN_COLLECTION.json` | ✅ 22 existierende Endpoints |

---

## 🚀 Implementation Reihenfolge

### Phase 1: Vendor POS (2-3 Tage)
```
1. VendorAuthController::login()
2. VendorController::activate() + Idempotency
3. VendorController::activations()
4. VendorController::userStatus()
5. Migration: add idempotency_key to activations
```

### Phase 2: User Card (1 Tag)
```
6. UserController::card()
7. QR Code Generation
```

### Phase 3: Admin Reports (2-3 Tage)
```
8. AdminReportController::coverage()
9. AdminReportController::activations()
10. AdminReportController::ratingSummary()
11. AdminCampaignController::sendPush()
```

### Phase 4: Admin CRUD APIs (1-2 Tage)
```
12. AdminNeighborhoodController (3 methods)
13. AdminCategoryController (3 methods)
14. AdminShopController (3 methods)
```

### Phase 5: Webhooks (1 Tag)
```
15. WebhookController::slickpay()
16. WebhookController::cib()
```

---

**Total Entwicklungszeit**: 7-11 Tage für alle fehlenden Features

**Aktueller Status**: ✅ **22/40 Endpoints (55%) implementiert**

**OpenAPI Spec**: ✅ **100% dokumentiert**
