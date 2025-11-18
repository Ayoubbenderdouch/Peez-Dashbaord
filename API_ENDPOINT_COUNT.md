# 📊 PeeZ API - Current Endpoint Count

**Date:** 4. November 2025  
**Total Endpoints:** **30**

---

## Breakdown by Category

### 🔐 Authentication (8 endpoints)
1. POST `/api/v1/auth/register` - User registration
2. POST `/api/v1/auth/login` - User login
3. POST `/api/v1/auth/logout` - User logout
4. GET  `/api/v1/auth/me` - Current user info
5. PUT  `/api/v1/auth/profile` - Update profile
6. POST `/api/v1/auth/fcm-token` - Update FCM token
7. POST `/api/v1/auth/forgot-password` - Password reset
8. POST `/api/v1/auth/vendor/login` - **NEW** Vendor POS login

---

### 🏪 Shops (5 endpoints)
9. GET  `/api/v1/shops` - List all shops
10. GET  `/api/v1/shops/{id}` - Get shop details
11. GET  `/api/v1/shops/nearby` - GPS-based search
12. GET  `/api/v1/shops/neighborhood/{neighborhoodId}` - By neighborhood
13. GET  `/api/v1/shops/category/{categoryId}` - By category

---

### 📍 Public Data (2 endpoints)
14. GET  `/api/v1/neighborhoods` - List neighborhoods
15. GET  `/api/v1/categories` - List categories

---

### 📅 Subscriptions (2 endpoints)
16. GET  `/api/v1/subscriptions/status` - User subscription status
17. GET  `/api/v1/subscriptions/history` - Subscription history
18. POST `/api/v1/subscriptions/activate` - Activate subscription (deprecated - use vendor/activate)

---

### ⭐ Ratings (3 endpoints)
19. POST `/api/v1/ratings` - Rate a shop
20. GET  `/api/v1/ratings` - Get ratings (filter by shop)
21. GET  `/api/v1/ratings/my-ratings` - My ratings

---

### 👤 Users (2 endpoints)
22. GET  `/api/v1/users/{uuid}/card` - **NEW** Membership card with QR
23. POST `/api/v1/users/verify-qr` - **NEW** Verify QR code signature

---

### 💼 Vendor POS (5 endpoints) **NEW**
24. POST `/api/v1/vendor/activate` - **NEW** Activate/extend subscription
25. GET  `/api/v1/vendor/activations` - **NEW** Activation history
26. GET  `/api/v1/vendor/users/{uuid}/status` - **NEW** Quick status check
27. GET  `/api/v1/vendor/me` - **NEW** Vendor info
28. POST `/api/v1/vendor/logout` - **NEW** Vendor logout

---

### 💳 Webhooks (2 endpoints)
29. POST `/api/v1/webhooks/slickpay` - SlickPay payment webhook
30. POST `/api/v1/webhooks/cib` - CIB Bank payment webhook

---

## Progress Overview

```
Target:     40 endpoints
Current:    30 endpoints
Progress:   75% ✅
Remaining:  10 endpoints
```

### Status Breakdown:
- ✅ **Implemented:** 30 endpoints (75%)
- ⚠️  **Partial (Filament UI):** 6 features (Admin CRUD)
- ❌ **Missing:** 4 endpoints (10%)

---

## Missing Endpoints (To reach 40)

### Admin Reports (4 endpoints)
1. GET  `/api/v1/admin/coverage/summary` - Shop coverage matrix
2. GET  `/api/v1/admin/reports/activations` - Monthly revenue report
3. GET  `/api/v1/admin/ratings/summary` - Rating statistics
4. POST `/api/v1/admin/campaigns/push` - Push notification campaign

### Admin CRUD (6 endpoints available via Filament)
5. POST `/api/v1/admin/neighborhoods` - Create neighborhood
6. PUT  `/api/v1/admin/neighborhoods/{id}` - Update neighborhood
7. DELETE `/api/v1/admin/neighborhoods/{id}` - Delete neighborhood

8. POST `/api/v1/admin/categories` - Create category
9. PUT  `/api/v1/admin/categories/{id}` - Update category
10. DELETE `/api/v1/admin/categories/{id}` - Delete category

**Note:** These are currently available through Filament Admin Panel UI

---

## Implementation History

### Original (Nov 3, 2025)
- 22 endpoints

### Phase 1 - Vendor POS (Nov 4, 2025)
- +8 endpoints
- **Total: 30 endpoints**

### Phase 2 - Admin Reports (Pending)
- +4 endpoints
- **Target: 34 endpoints**

### Phase 3 - Admin CRUD APIs (Pending)
- +6 endpoints
- **Target: 40 endpoints** 🎯

---

## API Method Distribution

| Method | Count | Percentage |
|--------|-------|------------|
| GET    | 16    | 53.3%      |
| POST   | 13    | 43.3%      |
| PUT    | 1     | 3.3%       |
| DELETE | 0     | 0%         |
| **Total** | **30** | **100%** |

---

## Authentication Requirements

| Category | Auth Required | Role Required |
|----------|---------------|---------------|
| Public Data | ❌ No | - |
| Authentication | ⚠️ Mixed | - |
| Shops | ❌ No | - |
| Ratings | ✅ Yes | User |
| Subscriptions | ✅ Yes | User |
| Users | ❌ No (UUID-based) | - |
| **Vendor POS** | ✅ Yes | **Vendor** |
| Admin | ✅ Yes | Admin/Manager |
| Webhooks | ❌ No (Signature) | - |

---

**Last Updated:** 4. November 2025, 16:15 UTC
