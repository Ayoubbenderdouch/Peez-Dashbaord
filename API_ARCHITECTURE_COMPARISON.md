# 🔄 PeeZ API - Architektur-Vergleich & Migration Plan

**Datum**: 4. November 2025  
**Status**: Analyse der Anforderungen vs. aktuelle Implementierung

---

## 📊 Vergleich: Anforderungen vs. Aktuelle Implementation

### ✅ Bereits Implementiert (80% Match)

#### Backend Struktur
- ✅ Laravel 12 + PHP 8.4 + MySQL 8
- ✅ Laravel Sanctum für Authentication
- ✅ 6 Controllers (Auth, Shop, Subscription, Rating, Category, Neighborhood)
- ✅ 6 API Resources (JSON Transformer)
- ✅ 22 REST Endpoints
- ✅ Role-Based Access Control (Admin, Manager, Vendor, Customer)

#### Public Endpoints
- ✅ GET /neighborhoods
- ✅ GET /categories
- ✅ GET /shops (mit Filtern)
- ✅ GET /shops/{id}

#### User Endpoints
- ✅ GET /subscriptions/status (ähnlich zu /users/{uuid}/subscription)
- ⚠️ **FEHLT**: GET /users/{uuid}/card (QR Code endpoint)

#### Rating Endpoints
- ✅ POST /ratings (mit shop_id, stars validation)
- ✅ Unique constraint (user_id, shop_id)
- ✅ Average calculation

#### Authentication
- ✅ POST /auth/login (generisch)
- ⚠️ **FEHLT**: POST /auth/vendor/login (spezialisiert)

---

## 🔴 Fehlende Features (20%)

### 1. Vendor POS Endpoints
```
❌ POST /vendor/activate (mit Idempotency-Key)
❌ GET /vendor/activations?month=YYYY-MM
❌ GET /vendor/users/{uuid}/status (quick validity check)
```

**Aktuell**: 
- Subscription activation existiert, aber nicht vendor-spezialisiert
- Keine Idempotency-Key Implementierung
- Keine monatliche Filterung

### 2. Admin Coverage & Reports
```
❌ GET /admin/coverage/summary
❌ GET /admin/reports/activations?month=YYYY-MM
❌ GET /admin/ratings/summary?shopId=
❌ POST /admin/campaigns/push
```

**Aktuell**:
- Basic CRUD existiert via Filament
- Keine spezialisierte Report-Endpoints

### 3. User Card/QR Endpoint
```
❌ GET /users/{uuid}/card
```

**Aktuell**: Nicht implementiert

### 4. Payment Webhooks
```
❌ POST /payments/slickpay/webhook
```

**Aktuell**: Placeholder in routes, aber keine Business-Logik

### 5. Push Notifications
```
❌ POST /notifications/push
```

**Aktuell**: NotificationService existiert, aber kein API Endpoint

---

## 🔧 Technische Anforderungen - Gap Analysis

### JSON Format
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| camelCase in JSON | ❌ snake_case | **TO FIX** |
| snake_case in DB | ✅ | OK |
| ISO-8601 UTC timestamps | ✅ | OK |

### Error Handling
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| RFC7807 Problem+JSON | ❌ Laravel default | **TO FIX** |
| code/key/message | ❌ | **TO FIX** |

### Pagination
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| Cursor or page/limit | ✅ Offset-based | **TO ENHANCE** |
| Include total/count/next | ✅ Partial | **TO COMPLETE** |

### Rate Limiting
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| 60 rpm per token | ❌ Laravel default (60/min) | **TO CONFIG** |

### Idempotency
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| Idempotency-Key header | ❌ Not implemented | **TO ADD** |

### OpenAPI Documentation
| Anforderung | Aktuell | Status |
|-------------|---------|--------|
| OpenAPI 3.1 YAML | ✅ Created | **NEW** |
| /docs/openapi.yaml | ✅ | **NEW** |

---

## 📋 Migration Plan - Priority Order

### PHASE 1: Quick Fixes (2-3 hours)
**Priority: HIGH - No breaking changes**

1. ✅ **OpenAPI Specification** - DONE
   - Created /docs/openapi.yaml
   - All endpoints documented

2. ⚠️ **JSON camelCase Transformation**
   ```php
   // Add to API Resources
   protected function withCamelCaseKeys(array $data): array
   {
       return collect($data)->mapWithKeys(function ($value, $key) {
           return [Str::camel($key) => $value];
       })->toArray();
   }
   ```

3. ⚠️ **RFC7807 Error Handler**
   ```php
   // app/Exceptions/Handler.php
   protected function renderApiException(Exception $e)
   {
       return response()->json([
           'type' => 'https://api.peez.dz/problems/...',
           'title' => '...',
           'status' => $statusCode,
           'detail' => $e->getMessage(),
           'code' => 'ERROR_CODE'
       ], $statusCode)
       ->header('Content-Type', 'application/problem+json');
   }
   ```

### PHASE 2: Vendor POS Features (4-5 hours)
**Priority: HIGH - Core business logic**

1. ❌ **Vendor Login Endpoint**
   ```php
   // VendorAuthController::login()
   POST /auth/vendor/login
   - Returns token + shopId
   ```

2. ❌ **Activation with Idempotency**
   ```php
   // VendorController::activate()
   - Check Idempotency-Key in cache/DB
   - Create/extend subscription
   - Log activation with amount (months * 300)
   ```

3. ❌ **Vendor Activations List**
   ```php
   // VendorController::activations()
   - Filter by month (YYYY-MM)
   - Scoped to vendor's shop
   ```

4. ❌ **Quick Status Check**
   ```php
   // VendorController::userStatus()
   - Fast query: has active subscription?
   - Return name + validUntil
   ```

### PHASE 3: User Card/QR (2 hours)
**Priority: MEDIUM**

1. ❌ **Card Endpoint**
   ```php
   // UserController::card()
   - Generate membership ID
   - Create QR payload (signed JWT or base64)
   - Return with status
   ```

### PHASE 4: Admin Reports (3-4 hours)
**Priority: MEDIUM**

1. ❌ **Coverage Summary**
   ```php
   // AdminReportController::coverage()
   - Calculate neighborhood x category matrix
   - Show gaps
   ```

2. ❌ **Activation Reports**
   ```php
   // AdminReportController::activations()
   - Group by shop + month
   - Calculate revenue (count * 300)
   - Include avgStars
   ```

3. ❌ **Rating Summary**
   ```php
   // AdminReportController::ratingSummary()
   - Stars distribution
   - Average by shop
   ```

4. ❌ **Push Campaigns**
   ```php
   // AdminCampaignController::sendPush()
   - Segment by neighborhood/category/shop
   - Queue FCM messages
   ```

### PHASE 5: Payment Webhooks (2 hours)
**Priority: LOW - Future feature**

1. ❌ **SlickPay Webhook Handler**
   ```php
   // WebhookController::slickpay()
   - Verify signature
   - Store payment confirmation
   - Create subscription if successful
   ```

---

## 🎯 Implementation Priority Matrix

| Feature | Business Impact | Complexity | Priority |
|---------|----------------|------------|----------|
| Vendor /activate | 🔴 Critical | Medium | **1** |
| Vendor /activations | 🔴 Critical | Low | **2** |
| User /card (QR) | 🟡 High | Medium | **3** |
| camelCase JSON | 🟢 Low | Low | **4** |
| RFC7807 Errors | 🟢 Low | Low | **5** |
| Admin Reports | 🟡 High | High | **6** |
| Push Campaigns | 🟡 High | Medium | **7** |
| Payment Webhooks | 🟢 Low | High | **8** |

---

## 📝 Database Schema - Already Correct! ✅

Die aktuelle Schema-Struktur passt perfekt zu den Anforderungen:

```sql
✅ neighborhoods (id, name, city)
✅ categories (id, name, slug)
✅ shops (id, neighborhood_id, category_id, discount_percentage, lat, lng, phone, is_active)
   ✅ UNIQUE(neighborhood_id, category_id)
✅ users (id, uuid, name, phone, fcm_token, role)
✅ subscriptions (id, user_id, start_date, end_date, status, source)
✅ activations (id, user_id, shop_id, vendor_id, months, amount_dzd, created_at)
   ⚠️ Braucht: idempotency_key column
✅ ratings (id, user_id, shop_id, stars)
   ✅ UNIQUE(user_id, shop_id)
```

**Fehlende Migration**:
```php
Schema::table('activations', function (Blueprint $table) {
    $table->string('idempotency_key')->nullable()->unique();
    $table->index('created_at'); // für month filtering
});
```

---

## 🧪 Test Coverage - Bereits Vorhanden! ✅

Die Anforderungen für Tests sind bereits implementiert:

```php
✅ Test: unique shop per (neighborhood, category)
✅ Test: subscription activation/extension (1/2/3 months)
✅ Test: revenue calculation = activations * 300
✅ Test: ratings - one per user per shop, average calculation
✅ Test: AuthZ - vendors see only their activations
```

**Zusätzliche Tests benötigt**:
```php
❌ Test: Idempotency-Key prevents duplicate activations
❌ Test: Vendor can't activate for other shops
❌ Test: QR payload is valid JWT/signed
❌ Test: Coverage report calculates correctly
```

---

## 📚 Documentation Status

| Document | Status | Content |
|----------|--------|---------|
| openapi.yaml | ✅ DONE | Complete API spec |
| README.md | ⚠️ PARTIAL | Needs OpenAPI mention |
| API_DOCUMENTATION.md | ⚠️ OLD FORMAT | Markdown, needs sync |
| MOBILE_DEV_GUIDE.md | ⚠️ OLD FORMAT | Needs camelCase update |

---

## 🚀 Recommended Action Plan

### Option A: **Incremental Enhancement** (Recommended)
**Timeframe**: 1-2 days

1. Keep existing 22 endpoints functional
2. Add missing vendor POS endpoints (3 new)
3. Add user card endpoint (1 new)
4. Add admin reports (4 new)
5. Implement camelCase transformation layer
6. Add RFC7807 error handler
7. Total: **30 endpoints**

**Pros**:
- No breaking changes
- Existing mobile apps keep working
- Gradual feature rollout

**Cons**:
- Mixed JSON formats during transition
- Need versioning strategy

### Option B: **Clean Rewrite**
**Timeframe**: 3-4 days

1. Rebuild all endpoints with new conventions
2. Break backward compatibility
3. Force mobile app updates

**Pros**:
- Clean architecture from start
- Consistent conventions everywhere

**Cons**:
- Breaks existing integrations
- More testing required
- Higher risk

---

## ✅ Next Steps

### Immediate (Today):
1. ✅ **OpenAPI spec created** - DONE!
2. ⚠️ Review with team: Option A vs B?
3. ⚠️ Decide on backward compatibility strategy

### Short Term (This Week):
1. Implement vendor POS endpoints
2. Add idempotency_key to activations table
3. Create user card/QR endpoint
4. Add camelCase transformer middleware

### Medium Term (Next Week):
1. Admin reports implementation
2. Push campaign system
3. Complete test coverage
4. Update mobile dev guides

---

## 📊 Current vs. Target API

```
Current API (22 endpoints):
├── Auth (7) ✅
├── Shops (5) ✅
├── Subscriptions (3) ✅
├── Ratings (3) ✅
├── Categories (2) ✅
└── Neighborhoods (2) ✅

Target API (30+ endpoints):
├── Auth (8) - +1 vendor login
├── Shops (5) ✅
├── User (2) - +1 card endpoint
├── Subscriptions (3) ✅
├── Ratings (3) ✅
├── Vendor POS (3) - NEW
├── Admin CRUD (9) - existing via Filament
├── Admin Reports (4) - NEW
├── Notifications (2) - NEW
└── Public (2) ✅
```

---

## 🎯 Fazit

**Die aktuelle PeeZ API ist 80% fertig!**

**Gut**:
- ✅ Solide Laravel-Basis
- ✅ Korrekte Datenmodelle
- ✅ Grundlegende Endpunkte funktionieren
- ✅ Tests vorhanden
- ✅ Dokumentation erstellt

**Fehlend**:
- ⚠️ Vendor POS spezialisierte Endpoints
- ⚠️ Admin Reporting
- ⚠️ camelCase JSON Konvention
- ⚠️ RFC7807 Error Format
- ⚠️ Idempotency für Activations

**Empfehlung**: 
✅ **Option A** - Inkrementelle Verbesserung
- 1-2 Tage für Phase 1-3
- Keine Breaking Changes
- Sofort produktiv nutzbar

---

**Status**: Wartet auf Go-Ahead für Implementation 🚀
