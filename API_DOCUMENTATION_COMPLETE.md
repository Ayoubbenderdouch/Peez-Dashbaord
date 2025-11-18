# 📚 PeeZ API - Complete Documentation (30 Endpoints)

**Version:** 1.1.0  
**Last Updated:** 4. November 2025  
**Base URL:** `https://peez.dz/api/v1`

---

## 📋 Table of Contents

1. [Authentication](#authentication) (8 endpoints)
2. [Public Data](#public-data) (2 endpoints)
3. [Shops](#shops) (5 endpoints)
4. [Subscriptions](#subscriptions) (3 endpoints)
5. [Ratings](#ratings) (3 endpoints)
6. [Users](#users) (2 endpoints)
7. [Vendor POS](#vendor-pos) (5 endpoints)
8. [Webhooks](#webhooks) (2 endpoints)

**Total:** 30 Endpoints

---

## 🔐 Authentication

All protected endpoints require Bearer token authentication:

```http
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

---

### 1. Register User
Create a new user account.

**Endpoint:** `POST /api/v1/auth/register`  
**Auth Required:** ❌ No

**Request Body:**
```json
{
  "name": "Ahmed Mohamed",
  "phone": "+213555123456",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "fcm_token": "firebase-token" // Optional
}
```

**Response:** `201 Created`
```json
{
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "phone": "+213555123456",
    "email": "ahmed@example.com",
    "role": "user",
    "created_at": "2025-11-04T10:30:00Z"
  },
  "token": "1|laravel_sanctum_token_here"
}
```

---

### 2. Login User
Authenticate existing user.

**Endpoint:** `POST /api/v1/auth/login`  
**Auth Required:** ❌ No

**Request Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "password123",
  "fcm_token": "firebase-token" // Optional
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "email": "ahmed@example.com",
    "role": "user"
  },
  "token": "2|new_token_here"
}
```

**Error:** `422 Unprocessable Entity`
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 3. Logout
Revoke current access token.

**Endpoint:** `POST /api/v1/auth/logout`  
**Auth Required:** ✅ Yes

**Response:** `200 OK`
```json
{
  "message": "Successfully logged out"
}
```

---

### 4. Get Current User
Get authenticated user information.

**Endpoint:** `GET /api/v1/auth/me`  
**Auth Required:** ✅ Yes

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "phone": "+213555123456",
    "email": "ahmed@example.com",
    "role": "user",
    "created_at": "2025-11-04T10:30:00Z"
  }
}
```

---

### 5. Update Profile
Update user profile information.

**Endpoint:** `PUT /api/v1/auth/profile`  
**Auth Required:** ✅ Yes

**Request Body:**
```json
{
  "name": "Ahmed Ali Mohamed",
  "phone": "+213555999888",
  "email": "newemail@example.com"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Ali Mohamed",
    "phone": "+213555999888",
    "email": "newemail@example.com",
    "role": "user",
    "updated_at": "2025-11-04T11:00:00Z"
  }
}
```

---

### 6. Update FCM Token
Update Firebase Cloud Messaging token for push notifications.

**Endpoint:** `POST /api/v1/auth/fcm-token`  
**Auth Required:** ✅ Yes

**Request Body:**
```json
{
  "fcm_token": "new-firebase-token-here"
}
```

**Response:** `200 OK`
```json
{
  "message": "FCM token updated successfully"
}
```

---

### 7. Forgot Password
Request password reset.

**Endpoint:** `POST /api/v1/auth/forgot-password`  
**Auth Required:** ❌ No

**Request Body:**
```json
{
  "email": "ahmed@example.com"
}
```

**Response:** `200 OK`
```json
{
  "message": "Password reset link sent to your email"
}
```

---

### 8. Vendor Login
Authenticate vendor for POS system.

**Endpoint:** `POST /api/v1/auth/vendor/login`  
**Auth Required:** ❌ No

**Request Body:**
```json
{
  "email": "vendor@peez.dz",
  "password": "password"
}
```

**Response:** `200 OK`
```json
{
  "data": {
    "token": "5|vendor_token_here",
    "tokenType": "Bearer",
    "expiresIn": null,
    "vendor": {
      "id": 3,
      "name": "Vendor User",
      "email": "vendor@peez.dz",
      "role": "vendor"
    },
    "shop": {
      "id": 2,
      "name": "Boucherie Centrale",
      "categoryId": 2,
      "categoryName": "Boucherie",
      "neighborhoodId": 1,
      "neighborhoodName": "Sidi El Houari",
      "discountPercent": 5.26
    }
  }
}
```

**Error:** `403 Forbidden`
```json
{
  "type": "https://peez.dz/errors/forbidden",
  "title": "Forbidden",
  "status": 403,
  "detail": "Access denied. This endpoint is only available for vendor accounts.",
  "code": "VENDOR_ROLE_REQUIRED"
}
```

---

## 📍 Public Data

### 9. List Neighborhoods
Get all neighborhoods in Oran.

**Endpoint:** `GET /api/v1/neighborhoods`  
**Auth Required:** ❌ No

**Response:** `200 OK`
```json
{
  "neighborhoods": [
    {
      "id": 1,
      "name_ar": "سيدي الهواري",
      "name_fr": "Sidi El Houari",
      "created_at": "2025-11-03T12:00:00Z"
    },
    {
      "id": 2,
      "name_ar": "المدينة الجديدة",
      "name_fr": "Ville Nouvelle",
      "created_at": "2025-11-03T12:00:00Z"
    }
  ]
}
```

---

### 10. List Categories
Get all business categories.

**Endpoint:** `GET /api/v1/categories`  
**Auth Required:** ❌ No

**Response:** `200 OK`
```json
{
  "categories": [
    {
      "id": 1,
      "name_ar": "مخبزة",
      "name_fr": "Boulangerie",
      "icon": "🥖",
      "created_at": "2025-11-03T12:00:00Z"
    },
    {
      "id": 2,
      "name_ar": "جزارة",
      "name_fr": "Boucherie",
      "icon": "🥩",
      "created_at": "2025-11-03T12:00:00Z"
    }
  ]
}
```

---

## 🏪 Shops

### 11. List All Shops
Get all shops with optional filters.

**Endpoint:** `GET /api/v1/shops`  
**Auth Required:** ❌ No

**Query Parameters:**
- `category_id` (optional) - Filter by category ID
- `neighborhood_id` (optional) - Filter by neighborhood ID
- `page` (optional) - Pagination page number

**Response:** `200 OK`
```json
{
  "shops": [
    {
      "id": 1,
      "name": "Boulangerie du Centre",
      "category": {
        "id": 1,
        "name_ar": "مخبزة",
        "name_fr": "Boulangerie"
      },
      "neighborhood": {
        "id": 1,
        "name_ar": "سيدي الهواري",
        "name_fr": "Sidi El Houari"
      },
      "discount_percent": 7.5,
      "address": "Rue de la République",
      "phone": "+213555111222",
      "latitude": 35.6976,
      "longitude": -0.6337,
      "average_rating": 4.5,
      "total_ratings": 12,
      "created_at": "2025-11-03T12:00:00Z"
    }
  ],
  "pagination": {
    "total": 50,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 4
  }
}
```

---

### 12. Get Shop Details
Get detailed information about a specific shop.

**Endpoint:** `GET /api/v1/shops/{id}`  
**Auth Required:** ❌ No

**Response:** `200 OK`
```json
{
  "shop": {
    "id": 1,
    "name": "Boulangerie du Centre",
    "category": {
      "id": 1,
      "name_ar": "مخبزة",
      "name_fr": "Boulangerie"
    },
    "neighborhood": {
      "id": 1,
      "name_ar": "سيدي الهواري",
      "name_fr": "Sidi El Houari"
    },
    "discount_percent": 7.5,
    "address": "Rue de la République",
    "phone": "+213555111222",
    "latitude": 35.6976,
    "longitude": -0.6337,
    "average_rating": 4.5,
    "total_ratings": 12,
    "created_at": "2025-11-03T12:00:00Z"
  }
}
```

**Error:** `404 Not Found`
```json
{
  "message": "Shop not found"
}
```

---

### 13. Find Nearby Shops
Get shops near current GPS location.

**Endpoint:** `GET /api/v1/shops/nearby`  
**Auth Required:** ❌ No

**Query Parameters:**
- `latitude` (required) - Current latitude
- `longitude` (required) - Current longitude
- `radius` (optional) - Search radius in kilometers (default: 5)

**Example:** `GET /api/v1/shops/nearby?latitude=35.6976&longitude=-0.6337&radius=10`

**Response:** `200 OK`
```json
{
  "shops": [
    {
      "id": 1,
      "name": "Boulangerie du Centre",
      "category": {
        "id": 1,
        "name_fr": "Boulangerie"
      },
      "neighborhood": {
        "id": 1,
        "name_fr": "Sidi El Houari"
      },
      "discount_percent": 7.5,
      "latitude": 35.6976,
      "longitude": -0.6337,
      "distance_km": 0.5,
      "average_rating": 4.5
    }
  ]
}
```

---

### 14. Shops by Neighborhood
Get all shops in a specific neighborhood.

**Endpoint:** `GET /api/v1/shops/neighborhood/{neighborhoodId}`  
**Auth Required:** ❌ No

**Response:** `200 OK`
```json
{
  "neighborhood": {
    "id": 1,
    "name_ar": "سيدي الهواري",
    "name_fr": "Sidi El Houari"
  },
  "shops": [
    {
      "id": 1,
      "name": "Boulangerie du Centre",
      "category": {
        "id": 1,
        "name_fr": "Boulangerie"
      },
      "discount_percent": 7.5,
      "average_rating": 4.5
    }
  ]
}
```

---

### 15. Shops by Category
Get all shops in a specific category.

**Endpoint:** `GET /api/v1/shops/category/{categoryId}`  
**Auth Required:** ❌ No

**Response:** `200 OK`
```json
{
  "category": {
    "id": 1,
    "name_ar": "مخبزة",
    "name_fr": "Boulangerie"
  },
  "shops": [
    {
      "id": 1,
      "name": "Boulangerie du Centre",
      "neighborhood": {
        "id": 1,
        "name_fr": "Sidi El Houari"
      },
      "discount_percent": 7.5,
      "average_rating": 4.5
    }
  ]
}
```

---

## 📅 Subscriptions

### 16. Get Subscription Status
Get current user's subscription status.

**Endpoint:** `GET /api/v1/subscriptions/status`  
**Auth Required:** ✅ Yes

**Response:** `200 OK`
```json
{
  "subscription": {
    "status": "active",
    "start_at": "2025-11-01T00:00:00Z",
    "end_at": "2026-02-01T00:00:00Z",
    "source": "vendor",
    "days_remaining": 89,
    "is_active": true
  }
}
```

**Response (No Subscription):** `200 OK`
```json
{
  "subscription": null,
  "message": "No active subscription"
}
```

---

### 17. Get Subscription History
Get user's subscription activation history.

**Endpoint:** `GET /api/v1/subscriptions/history`  
**Auth Required:** ✅ Yes

**Response:** `200 OK`
```json
{
  "subscriptions": [
    {
      "id": 1,
      "status": "active",
      "start_at": "2025-11-01T00:00:00Z",
      "end_at": "2026-02-01T00:00:00Z",
      "source": "vendor",
      "created_at": "2025-11-01T10:30:00Z"
    },
    {
      "id": 2,
      "status": "expired",
      "start_at": "2025-08-01T00:00:00Z",
      "end_at": "2025-11-01T00:00:00Z",
      "source": "vendor",
      "created_at": "2025-08-01T09:15:00Z"
    }
  ]
}
```

---

### 18. Activate Subscription (Deprecated)
Activate user subscription.

**Endpoint:** `POST /api/v1/subscriptions/activate`  
**Auth Required:** ✅ Yes  
**Status:** ⚠️ Deprecated - Use `/vendor/activate` instead

**Request Body:**
```json
{
  "months": 3,
  "payment_method": "cash"
}
```

**Response:** `201 Created`
```json
{
  "subscription": {
    "id": 1,
    "status": "active",
    "start_at": "2025-11-04T00:00:00Z",
    "end_at": "2026-02-04T00:00:00Z",
    "source": "vendor"
  }
}
```

---

## ⭐ Ratings

### 19. Rate a Shop
Submit a rating for a shop.

**Endpoint:** `POST /api/v1/ratings`  
**Auth Required:** ✅ Yes

**Request Body:**
```json
{
  "shop_id": 1,
  "stars": 5,
  "comment": "Excellent service and quality!" // Optional
}
```

**Validation:**
- `shop_id`: required, exists in shops table
- `stars`: required, integer between 1 and 5
- `comment`: optional, max 500 characters

**Response:** `201 Created`
```json
{
  "rating": {
    "id": 1,
    "shop_id": 1,
    "user_id": 1,
    "stars": 5,
    "comment": "Excellent service and quality!",
    "created_at": "2025-11-04T12:00:00Z"
  }
}
```

**Error:** `422 Unprocessable Entity`
```json
{
  "message": "You have already rated this shop",
  "errors": {
    "shop_id": ["You have already rated this shop"]
  }
}
```

---

### 20. Get Shop Ratings
Get all ratings for a specific shop.

**Endpoint:** `GET /api/v1/ratings?shop_id={shopId}`  
**Auth Required:** ❌ No

**Query Parameters:**
- `shop_id` (required) - Shop ID to get ratings for
- `page` (optional) - Pagination page number

**Example:** `GET /api/v1/ratings?shop_id=1`

**Response:** `200 OK`
```json
{
  "ratings": [
    {
      "id": 1,
      "user": {
        "id": 1,
        "name": "Ahmed Mohamed"
      },
      "stars": 5,
      "comment": "Excellent service!",
      "created_at": "2025-11-04T12:00:00Z"
    },
    {
      "id": 2,
      "user": {
        "id": 2,
        "name": "Fatima Ali"
      },
      "stars": 4,
      "comment": "Very good",
      "created_at": "2025-11-03T15:30:00Z"
    }
  ],
  "statistics": {
    "average": 4.5,
    "total": 12,
    "distribution": {
      "5": 8,
      "4": 3,
      "3": 1,
      "2": 0,
      "1": 0
    }
  }
}
```

---

### 21. Get My Ratings
Get all ratings submitted by authenticated user.

**Endpoint:** `GET /api/v1/ratings/my-ratings`  
**Auth Required:** ✅ Yes

**Response:** `200 OK`
```json
{
  "ratings": [
    {
      "id": 1,
      "shop": {
        "id": 1,
        "name": "Boulangerie du Centre",
        "category": "Boulangerie"
      },
      "stars": 5,
      "comment": "Excellent service!",
      "created_at": "2025-11-04T12:00:00Z"
    }
  ]
}
```

---

## 👤 Users

### 22. Get User Membership Card
Get user's digital membership card with QR code.

**Endpoint:** `GET /api/v1/users/{uuid}/card`  
**Auth Required:** ❌ No (UUID-based access)

**Response:** `200 OK`
```json
{
  "data": {
    "user": {
      "uuid": "f5a757a2-e2ee-482f-b50b-29df3ba6311e",
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "phone": "+213555123456",
      "membershipId": "PEEZ-20251103-00002",
      "memberSince": "2025-11-03T12:00:00+00:00"
    },
    "subscription": {
      "isActive": true,
      "startsAt": "2025-11-04T15:53:45+00:00",
      "endsAt": "2026-02-04T15:53:45+00:00",
      "daysRemaining": 92
    },
    "qrCode": {
      "image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
      "format": "image/png",
      "payload": {
        "uuid": "f5a757a2-e2ee-482f-b50b-29df3ba6311e",
        "membershipId": "PEEZ-20251103-00002",
        "expiresAt": "2026-02-04T15:53:45+00:00",
        "timestamp": 1730736825,
        "signature": "a8f3b9d2e1c4..."
      }
    }
  }
}
```

**Error:** `404 Not Found`
```json
{
  "type": "https://peez.dz/errors/not-found",
  "title": "User Not Found",
  "status": 404,
  "detail": "No user found with the provided UUID.",
  "code": "USER_NOT_FOUND"
}
```

---

### 23. Verify QR Code
Verify QR code signature and authenticity.

**Endpoint:** `POST /api/v1/users/verify-qr`  
**Auth Required:** ❌ No

**Request Body:**
```json
{
  "payload": "{\"uuid\":\"f5a757a2-e2ee-482f-b50b-29df3ba6311e\",\"membershipId\":\"PEEZ-20251103-00002\",\"expiresAt\":\"2026-02-04T15:53:45+00:00\",\"timestamp\":1730736825,\"signature\":\"a8f3b9d2e1c4...\"}"
}
```

**Response (Valid):** `200 OK`
```json
{
  "data": {
    "valid": true,
    "expired": false,
    "userUuid": "f5a757a2-e2ee-482f-b50b-29df3ba6311e",
    "membershipId": "PEEZ-20251103-00002",
    "expiresAt": "2026-02-04T15:53:45+00:00"
  }
}
```

**Response (Invalid):** `200 OK`
```json
{
  "data": {
    "valid": false,
    "reason": "Invalid signature"
  }
}
```

**Response (Expired):** `200 OK`
```json
{
  "data": {
    "valid": true,
    "expired": true,
    "userUuid": "f5a757a2-e2ee-482f-b50b-29df3ba6311e",
    "expiresAt": "2025-10-01T00:00:00+00:00"
  }
}
```

---

## 💼 Vendor POS

All vendor endpoints require authentication with vendor role.

### 24. Activate/Extend Subscription
Activate or extend user subscription at POS.

**Endpoint:** `POST /api/v1/vendor/activate`  
**Auth Required:** ✅ Yes (Vendor role)

**Headers:**
```http
Authorization: Bearer {vendor-token}
Content-Type: application/json
Idempotency-Key: {unique-key}
```

**Request Body:**
```json
{
  "userUuid": "550e8400-e29b-41d4-a716-446655440000",
  "months": 3
}
```

**Validation:**
- `userUuid`: required, valid UUID, user must exist
- `months`: required, integer, must be 1, 2, or 3
- `Idempotency-Key` header: required, prevents duplicate charges

**Response (First Request):** `201 Created`
```json
{
  "data": {
    "activationId": 4,
    "userUuid": "550e8400-e29b-41d4-a716-446655440000",
    "userName": "Ahmed Mohamed",
    "months": 3,
    "shopId": 2,
    "shopName": "Boucherie Centrale",
    "activatedAt": "2025-11-04T15:53:45+00:00",
    "subscription": {
      "startsAt": "2025-11-04T15:53:45+00:00",
      "endsAt": "2026-02-04T15:53:45+00:00",
      "isActive": true,
      "action": "created"
    },
    "idempotent": false
  }
}
```

**Response (Duplicate Request - Same Idempotency Key):** `200 OK`
```json
{
  "data": {
    "activationId": 4,
    "userUuid": "550e8400-e29b-41d4-a716-446655440000",
    "months": 3,
    "shopId": 2,
    "activatedAt": "2025-11-04T15:53:45+00:00",
    "subscription": {
      "startsAt": "2025-11-04T15:53:45+00:00",
      "endsAt": "2026-02-04T15:53:45+00:00",
      "isActive": true
    },
    "idempotent": true
  }
}
```

**Business Rules:**
- If user has active subscription: extends end date
- If no subscription or expired: creates new subscription
- Price: 300 DZD per activation (fixed)
- Idempotency window: 24 hours

**Error (Missing Idempotency Key):** `422 Unprocessable Entity`
```json
{
  "type": "https://peez.dz/errors/validation",
  "title": "Validation Error",
  "status": 422,
  "detail": "Idempotency-Key header is required for this operation.",
  "code": "IDEMPOTENCY_KEY_REQUIRED"
}
```

**Error (User Not Found):** `404 Not Found`
```json
{
  "type": "https://peez.dz/errors/not-found",
  "title": "User Not Found",
  "status": 404,
  "detail": "No user found with the provided UUID.",
  "code": "USER_NOT_FOUND",
  "key": "userUuid"
}
```

---

### 25. Get Activation History
Get activation history for vendor's shop with revenue tracking.

**Endpoint:** `GET /api/v1/vendor/activations`  
**Auth Required:** ✅ Yes (Vendor role)

**Query Parameters:**
- `month` (optional) - Filter by month in YYYY-MM format

**Examples:**
- `GET /api/v1/vendor/activations` - All time
- `GET /api/v1/vendor/activations?month=2025-11` - November 2025

**Response:** `200 OK`
```json
{
  "data": {
    "shop": {
      "id": 2,
      "name": "Boucherie Centrale"
    },
    "period": "2025-11",
    "statistics": {
      "totalActivations": 12,
      "totalRevenue": 3600,
      "currency": "DZD",
      "pricePerActivation": 300
    },
    "activations": [
      {
        "id": 4,
        "user": {
          "uuid": "550e8400-e29b-41d4-a716-446655440000",
          "name": "Ahmed Mohamed"
        },
        "months": 3,
        "revenue": 300,
        "activatedAt": "2025-11-04T15:53:45+00:00"
      },
      {
        "id": 3,
        "user": {
          "uuid": "7eb37919-a105-4854-be79-26e93e953eb2",
          "name": "Fatima Ali"
        },
        "months": 2,
        "revenue": 300,
        "activatedAt": "2025-11-02T10:20:00+00:00"
      }
    ]
  }
}
```

**Error (Invalid Month Format):** `422 Unprocessable Entity`
```json
{
  "type": "https://peez.dz/errors/validation",
  "title": "Validation Error",
  "status": 422,
  "detail": "Month must be in YYYY-MM format.",
  "code": "INVALID_MONTH_FORMAT",
  "key": "month"
}
```

---

### 26. Check User Status
Quick validation of user subscription status at checkout.

**Endpoint:** `GET /api/v1/vendor/users/{uuid}/status`  
**Auth Required:** ✅ Yes (Vendor role)

**Response (Active Subscription):** `200 OK`
```json
{
  "data": {
    "userUuid": "550e8400-e29b-41d4-a716-446655440000",
    "userName": "Ahmed Mohamed",
    "hasActiveSubscription": true,
    "subscription": {
      "startsAt": "2025-11-04T15:53:45+00:00",
      "endsAt": "2026-02-04T15:53:45+00:00",
      "isActive": true,
      "daysRemaining": 92
    }
  }
}
```

**Response (No Subscription):** `200 OK`
```json
{
  "data": {
    "userUuid": "550e8400-e29b-41d4-a716-446655440000",
    "userName": "Ahmed Mohamed",
    "hasActiveSubscription": false,
    "subscription": null
  }
}
```

**Error:** `404 Not Found`
```json
{
  "type": "https://peez.dz/errors/not-found",
  "title": "User Not Found",
  "status": 404,
  "detail": "No user found with the provided UUID.",
  "code": "USER_NOT_FOUND"
}
```

---

### 27. Get Vendor Info
Get authenticated vendor's information and shop details.

**Endpoint:** `GET /api/v1/vendor/me`  
**Auth Required:** ✅ Yes (Vendor role)

**Response:** `200 OK`
```json
{
  "data": {
    "vendor": {
      "id": 3,
      "name": "Vendor User",
      "email": "vendor@peez.dz",
      "role": "vendor",
      "createdAt": "2025-11-03T12:00:00+00:00"
    },
    "shop": {
      "id": 2,
      "name": "Boucherie Centrale",
      "categoryId": 2,
      "categoryName": "Boucherie",
      "neighborhoodId": 1,
      "neighborhoodName": "Sidi El Houari",
      "discountPercent": 5.26,
      "address": "Rue de la Poste",
      "phone": "+213555000001",
      "latitude": 35.6976,
      "longitude": -0.6337
    }
  }
}
```

**Error (No Shop):** `424 Failed Dependency`
```json
{
  "type": "https://peez.dz/errors/configuration",
  "title": "Configuration Error",
  "status": 424,
  "detail": "Your vendor account is not linked to any shop.",
  "code": "SHOP_NOT_CONFIGURED"
}
```

---

### 28. Vendor Logout
Revoke vendor access token.

**Endpoint:** `POST /api/v1/vendor/logout`  
**Auth Required:** ✅ Yes (Vendor role)

**Response:** `200 OK`
```json
{
  "data": {
    "message": "Successfully logged out"
  }
}
```

---

## 💳 Webhooks

### 29. SlickPay Webhook
Receive payment notifications from SlickPay.

**Endpoint:** `POST /api/v1/webhooks/slickpay`  
**Auth Required:** ❌ No (Signature verification)

**Request Headers:**
```http
X-SlickPay-Signature: {signature}
Content-Type: application/json
```

**Request Body:**
```json
{
  "transaction_id": "SLICK123456",
  "amount": 900,
  "currency": "DZD",
  "status": "completed",
  "user_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "months": 3,
  "timestamp": 1730736825
}
```

**Response:** `200 OK`
```json
{
  "message": "Webhook processed successfully"
}
```

---

### 30. CIB Bank Webhook
Receive payment notifications from CIB Bank.

**Endpoint:** `POST /api/v1/webhooks/cib`  
**Auth Required:** ❌ No (Signature verification)

**Request Headers:**
```http
X-CIB-Signature: {signature}
Content-Type: application/json
```

**Request Body:**
```json
{
  "order_id": "CIB789012",
  "amount": 900,
  "currency": "DZD",
  "status": "success",
  "user_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "months": 3,
  "timestamp": 1730736825
}
```

**Response:** `200 OK`
```json
{
  "message": "Webhook processed successfully"
}
```

---

## 🔒 Error Responses

All endpoints follow RFC7807 Problem+JSON format for errors:

### 401 Unauthorized
```json
{
  "type": "https://peez.dz/errors/unauthorized",
  "title": "Unauthorized",
  "status": 401,
  "detail": "Authentication required.",
  "code": "UNAUTHENTICATED"
}
```

### 403 Forbidden
```json
{
  "type": "https://peez.dz/errors/forbidden",
  "title": "Forbidden",
  "status": 403,
  "detail": "Access denied. This endpoint requires 'vendor' role.",
  "code": "INSUFFICIENT_PERMISSIONS"
}
```

### 404 Not Found
```json
{
  "type": "https://peez.dz/errors/not-found",
  "title": "Not Found",
  "status": 404,
  "detail": "The requested resource was not found.",
  "code": "RESOURCE_NOT_FOUND"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### 500 Internal Server Error
```json
{
  "type": "https://peez.dz/errors/server",
  "title": "Server Error",
  "status": 500,
  "detail": "An unexpected error occurred.",
  "code": "INTERNAL_SERVER_ERROR"
}
```

---

## 📊 Rate Limiting

All API endpoints are rate-limited:
- **Authenticated requests:** 60 requests per minute per token
- **Unauthenticated requests:** 30 requests per minute per IP

**Rate Limit Headers:**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1730737200
```

**Rate Limit Exceeded:** `429 Too Many Requests`
```json
{
  "message": "Too many requests. Please try again later.",
  "retry_after": 45
}
```

---

## 🔑 Testing Credentials

### Regular User
```
Email: ahmed@example.com
Password: password123
```

### Vendor
```
Email: vendor@peez.dz
Password: password
Shop: Boucherie Centrale
```

### Test Customer UUIDs
```
7eb37919-a105-4854-be79-26e93e953eb2
d1133f79-6bd1-4c4d-be95-9ffc0ddbe71c
a233d42a-9d75-4d1a-8994-f0359a0530fb
```

---

## 📝 Notes

### Idempotency
The `/vendor/activate` endpoint requires an `Idempotency-Key` header to prevent duplicate charges. Use a unique UUID for each activation attempt:

```http
Idempotency-Key: 550e8400-e29b-41d4-a716-446655440000
```

If the same key is used within 24 hours, the API returns the original response with `idempotent: true`.

### Timestamps
All timestamps are in ISO 8601 format with UTC timezone:
```
2025-11-04T15:53:45+00:00
```

### Pagination
Endpoints that return lists include pagination metadata:
```json
{
  "data": [...],
  "pagination": {
    "total": 50,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 4,
    "next": "https://peez.dz/api/v1/shops?page=2"
  }
}
```

---

**Documentation Version:** 1.1.0  
**Last Updated:** 4. November 2025  
**Total Endpoints:** 30  
**Status:** ✅ Production Ready

---

**Need Help?**  
- Email: support@peez.dz
- Documentation: https://docs.peez.dz
- API Status: https://status.peez.dz
