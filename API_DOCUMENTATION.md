# PeeZ API Documentation v1.0

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication
All protected endpoints require Bearer token authentication using Laravel Sanctum.

### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

---

## Authentication Endpoints

### 1. Register
Create a new user account.

**Endpoint:** `POST /auth/register`

**Request:**
```json
{
  "name": "Ahmed Mohamed",
  "phone": "+213555123456",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "fcm_token": "firebase-cloud-messaging-token" // Optional
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
    "is_vendor": false,
    "role": "user",
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z"
  },
  "token": "1|laravel_sanctum_token_here"
}
```

---

### 2. Login
Authenticate existing user.

**Endpoint:** `POST /auth/login`

**Request:**
```json
{
  "email": "ahmed@example.com",
  "password": "password123",
  "fcm_token": "firebase-cloud-messaging-token" // Optional
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "phone": "+213555123456",
    "email": "ahmed@example.com",
    "is_vendor": false,
    "role": "user",
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z"
  },
  "token": "2|new_laravel_sanctum_token"
}
```

**Error Response:** `422 Unprocessable Entity`
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

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
{
  "message": "Logged out successfully"
}
```

---

### 4. Get Current User
Get authenticated user profile.

**Endpoint:** `GET /auth/me`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
{
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Mohamed",
    "phone": "+213555123456",
    "email": "ahmed@example.com",
    "is_vendor": true,
    "role": "vendor",
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z"
  }
}
```

---

### 5. Update Profile
Update authenticated user information.

**Endpoint:** `PUT /auth/profile`

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "name": "Ahmed Updated",
  "phone": "+213555999888",
  "email": "ahmed.new@example.com",
  "password": "newpassword123", // Optional
  "password_confirmation": "newpassword123" // Required if password provided
}
```

**Response:** `200 OK`
```json
{
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Ahmed Updated",
    "phone": "+213555999888",
    "email": "ahmed.new@example.com",
    "is_vendor": true,
    "role": "vendor",
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T11:45:00Z"
  }
}
```

---

### 6. Update FCM Token
Update Firebase Cloud Messaging token for push notifications.

**Endpoint:** `POST /auth/fcm-token`

**Headers:** `Authorization: Bearer {token}`

**Request:**
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
Request password reset link.

**Endpoint:** `POST /auth/forgot-password`

**Request:**
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

## Shop Endpoints

### 1. Get All Shops
Get paginated list of shops with optional filters.

**Endpoint:** `GET /shops`

**Query Parameters:**
- `neighborhood_id` (optional): Filter by neighborhood
- `category_id` (optional): Filter by category
- `search` (optional): Search by shop name
- `page` (optional): Page number (default: 1)

**Example:** `GET /shops?category_id=3&neighborhood_id=5&search=pizza`

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "name": "Pizza Deluxe",
      "address": "123 Main Street",
      "phone": "+213555111222",
      "discount_percentage": 7.5,
      "location": {
        "latitude": 36.7538,
        "longitude": 3.0588
      },
      "neighborhood": {
        "id": 5,
        "name": "Bab Ezzouar",
        "city": "Algiers"
      },
      "category": {
        "id": 3,
        "name": "Restaurant",
        "slug": "restaurant"
      },
      "rating": {
        "average": 4.5,
        "count": 23
      }
    }
  ],
  "links": {
    "first": "http://api/shops?page=1",
    "last": "http://api/shops?page=5",
    "prev": null,
    "next": "http://api/shops?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 95
  }
}
```

---

### 2. Get Single Shop
Get detailed shop information.

**Endpoint:** `GET /shops/{id}`

**Response:** `200 OK`
```json
{
  "data": {
    "id": 1,
    "name": "Pizza Deluxe",
    "address": "123 Main Street",
    "phone": "+213555111222",
    "discount_percentage": 7.5,
    "location": {
      "latitude": 36.7538,
      "longitude": 3.0588
    },
    "neighborhood": {
      "id": 5,
      "name": "Bab Ezzouar",
      "city": "Algiers"
    },
    "category": {
      "id": 3,
      "name": "Restaurant",
      "slug": "restaurant"
    },
    "rating": {
      "average": 4.5,
      "count": 23
    }
  }
}
```

---

### 3. Get Nearby Shops
Find shops within specified radius.

**Endpoint:** `GET /shops/nearby`

**Query Parameters:**
- `latitude` (required): User's latitude
- `longitude` (required): User's longitude
- `radius` (optional): Search radius in kilometers (default: 5, max: 50)

**Example:** `GET /shops/nearby?latitude=36.7538&longitude=3.0588&radius=10`

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "name": "Pizza Deluxe",
      "address": "123 Main Street",
      "phone": "+213555111222",
      "discount_percentage": 7.5,
      "distance": 2.3,
      "location": {
        "latitude": 36.7538,
        "longitude": 3.0588
      },
      "neighborhood": {
        "id": 5,
        "name": "Bab Ezzouar",
        "city": "Algiers"
      },
      "category": {
        "id": 3,
        "name": "Restaurant",
        "slug": "restaurant"
      },
      "rating": {
        "average": 4.5,
        "count": 23
      }
    }
  ]
}
```

---

### 4. Get Shops by Neighborhood
Get all shops in a specific neighborhood.

**Endpoint:** `GET /shops/neighborhood/{neighborhoodId}`

**Response:** `200 OK` (Paginated, same structure as "Get All Shops")

---

### 5. Get Shops by Category
Get all shops in a specific category.

**Endpoint:** `GET /shops/category/{categoryId}`

**Response:** `200 OK` (Paginated, same structure as "Get All Shops")

---

## Subscription Endpoints

### 1. Get Active Subscriptions
Get user's active subscriptions.

**Endpoint:** `GET /subscriptions/status`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "status": "active",
      "source": "slickpay",
      "start_date": "2025-01-01T00:00:00Z",
      "end_date": "2025-04-01T00:00:00Z",
      "days_remaining": 45,
      "is_active": true,
      "shop": {
        "id": 1,
        "name": "Pizza Deluxe",
        "neighborhood": {
          "id": 5,
          "name": "Bab Ezzouar"
        }
      },
      "created_at": "2025-01-01T10:00:00Z"
    }
  ]
}
```

---

### 2. Get Subscription History
Get all user subscriptions (active and expired).

**Endpoint:** `GET /subscriptions/history`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK` (Paginated)
```json
{
  "data": [
    {
      "id": 2,
      "status": "expired",
      "source": "cib",
      "start_date": "2024-10-01T00:00:00Z",
      "end_date": "2024-12-31T00:00:00Z",
      "days_remaining": 0,
      "is_active": false,
      "shop": {
        "id": 3,
        "name": "Café Central"
      },
      "created_at": "2024-10-01T09:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 8
  }
}
```

---

### 3. Activate Subscription (Vendor Only)
Create a new subscription for a shop.

**Endpoint:** `POST /subscriptions/activate`

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "shop_id": 1,
  "duration": 3,
  "payment_method": "slickpay",
  "payment_reference": "SLICK-2025-123456"
}
```

**Fields:**
- `shop_id`: Shop ID (vendor must own this shop)
- `duration`: 1, 2, or 3 months
- `payment_method`: "slickpay", "cib", or "cash"
- `payment_reference`: Optional payment reference

**Response:** `200 OK`
```json
{
  "data": {
    "id": 5,
    "status": "active",
    "source": "slickpay",
    "start_date": "2025-01-15T00:00:00Z",
    "end_date": "2025-04-15T00:00:00Z",
    "days_remaining": 90,
    "is_active": true,
    "shop": {
      "id": 1,
      "name": "Pizza Deluxe"
    },
    "created_at": "2025-01-15T12:00:00Z"
  }
}
```

**Error Response:** `403 Forbidden`
```json
{
  "message": "You do not own this shop"
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "This shop already has an active subscription"
}
```

---

## Rating Endpoints

### 1. Rate a Shop
Create or update rating for a shop.

**Endpoint:** `POST /ratings`

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "shop_id": 1,
  "stars": 5
}
```

**Fields:**
- `shop_id`: Shop ID to rate
- `stars`: Rating from 1 to 5

**Response:** `200 OK`
```json
{
  "data": {
    "id": 10,
    "stars": 5,
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed"
    },
    "shop": {
      "id": 1,
      "name": "Pizza Deluxe"
    },
    "created_at": "2025-01-15T14:30:00Z"
  }
}
```

---

### 2. Get Shop Ratings
Get all ratings for a specific shop.

**Endpoint:** `GET /ratings?shop_id={shopId}`

**Query Parameters:**
- `shop_id` (required): Shop ID

**Example:** `GET /ratings?shop_id=1`

**Response:** `200 OK` (Paginated)
```json
{
  "data": [
    {
      "id": 10,
      "stars": 5,
      "user": {
        "id": 1,
        "name": "Ahmed Mohamed"
      },
      "shop": {
        "id": 1,
        "name": "Pizza Deluxe"
      },
      "created_at": "2025-01-15T14:30:00Z"
    },
    {
      "id": 9,
      "stars": 4,
      "user": {
        "id": 3,
        "name": "Fatima Ali"
      },
      "shop": {
        "id": 1,
        "name": "Pizza Deluxe"
      },
      "created_at": "2025-01-14T10:20:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 23
  }
}
```

---

### 3. Get My Ratings
Get current user's ratings.

**Endpoint:** `GET /ratings/my-ratings`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK` (Paginated, same structure as "Get Shop Ratings")

---

## Category & Neighborhood Endpoints

### 1. Get All Categories
Get list of all categories.

**Endpoint:** `GET /categories`

**Query Parameters:**
- `include_count` (optional): Include shop counts if present

**Example:** `GET /categories?include_count=true`

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "name": "Restaurant",
      "slug": "restaurant",
      "shops_count": 45
    },
    {
      "id": 2,
      "name": "Café",
      "slug": "cafe",
      "shops_count": 32
    }
  ]
}
```

---

### 2. Get All Neighborhoods
Get list of all neighborhoods.

**Endpoint:** `GET /neighborhoods`

**Query Parameters:**
- `include_count` (optional): Include shop counts if present

**Example:** `GET /neighborhoods?include_count=true`

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "name": "Bab Ezzouar",
      "city": "Algiers",
      "shops_count": 23
    },
    {
      "id": 2,
      "name": "Hydra",
      "city": "Algiers",
      "shops_count": 18
    }
  ]
}
```

---

## Webhook Endpoints

### 1. SlickPay Webhook
Receive payment notifications from SlickPay.

**Endpoint:** `POST /webhooks/slickpay`

**Request:** (From SlickPay)
```json
{
  "transaction_id": "SLICK-2025-123456",
  "status": "completed",
  "amount": 900,
  "metadata": {
    "user_id": 1,
    "shop_id": 5,
    "duration": 3
  }
}
```

**Response:** `200 OK`
```json
{
  "message": "Webhook processed successfully"
}
```

---

### 2. CIB Payment Webhook
Receive payment notifications from CIB.

**Endpoint:** `POST /webhooks/cib`

**Request:** (From CIB)
```json
{
  "payment_id": "CIB-2025-987654",
  "status": "success",
  "amount": 600,
  "reference": {
    "user_id": 2,
    "shop_id": 8,
    "duration": 2
  }
}
```

**Response:** `200 OK`
```json
{
  "message": "Webhook processed successfully"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "message": "Server Error"
}
```

---

## Rate Limiting

API requests are rate-limited to:
- **60 requests per minute** for authenticated users
- **30 requests per minute** for guest users

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
Retry-After: 30
```

---

## Testing with Postman

Import the Postman collection (see `POSTMAN_COLLECTION.json`) for quick testing.

### Quick Start:
1. Register a new user → Get token
2. Set token in Postman environment variable `{{token}}`
3. Test protected endpoints
4. Create ratings, view shops, manage subscriptions

---

## iOS/Android Integration

### Recommended Libraries:

**iOS (Swift):**
- Alamofire for networking
- SwiftyJSON for JSON parsing
- KeychainAccess for token storage

**Android (Kotlin):**
- Retrofit for networking
- Gson for JSON parsing
- SharedPreferences for token storage

### Sample iOS Code:
```swift
import Alamofire

struct AuthResponse: Codable {
    let user: User
    let token: String
}

func login(email: String, password: String, completion: @escaping (Result<AuthResponse, Error>) -> Void) {
    let parameters: [String: String] = [
        "email": email,
        "password": password
    ]
    
    AF.request("https://your-domain.com/api/v1/auth/login",
               method: .post,
               parameters: parameters,
               encoder: JSONParameterEncoder.default)
        .responseDecodable(of: AuthResponse.self) { response in
            switch response.result {
            case .success(let authResponse):
                // Store token
                UserDefaults.standard.set(authResponse.token, forKey: "auth_token")
                completion(.success(authResponse))
            case .failure(let error):
                completion(.failure(error))
            }
        }
}
```

### Sample Android Code:
```kotlin
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.*

interface ApiService {
    @POST("auth/login")
    suspend fun login(@Body credentials: LoginRequest): LoginResponse
    
    @GET("shops")
    suspend fun getShops(@Query("category_id") categoryId: Int?): ShopsResponse
    
    @Headers("Authorization: Bearer {token}")
    @POST("ratings")
    suspend fun rateShop(@Body rating: RatingRequest): RatingResponse
}

val retrofit = Retrofit.Builder()
    .baseUrl("https://your-domain.com/api/v1/")
    .addConverterFactory(GsonConverterFactory.create())
    .build()

val apiService = retrofit.create(ApiService::class.java)
```

---

## Support

For API support, contact: **support@peez-app.com**

**API Version:** v1.0  
**Last Updated:** January 15, 2025
