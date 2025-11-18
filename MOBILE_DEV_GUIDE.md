# PeeZ API - Mobile Development Quick Start

## 🎯 Overview
Complete REST API v1 für iOS und Android App-Entwicklung. Alle Endpunkte implementiert, getestet und dokumentiert.

---

## 📦 Was ist fertig?

### ✅ Backend Implementation (100%)
- **6 API Resources** - JSON Transformer für alle Models
  - `UserResource` - Benutzer mit UUID, Role, Timestamps
  - `ShopResource` - Shops mit Location, Ratings, Relations
  - `SubscriptionResource` - Mit days_remaining Berechnung
  - `RatingResource` - Mit User/Shop Details
  - `CategoryResource` - Mit optional shop_count
  - `NeighborhoodResource` - Mit optional shop_count

- **4 API Controllers** - Business Logic
  - `AuthController` - 7 Methods (register, login, logout, me, updateProfile, updateFcmToken, forgotPassword)
  - `ShopController` - 5 Methods (index, show, nearby, byNeighborhood, byCategory)
  - `SubscriptionController` - 3 Methods (status, history, activate)
  - `RatingController` - 3 Methods (rate, index, myRatings)

- **29 API Routes** - Vollständig definiert in `routes/api.php`
  - 7 Public Routes (Registrierung, Login, Kategorien, Neighborhoods)
  - 20 Protected Routes (auth:sanctum Middleware)
  - 2 Webhook Routes (SlickPay, CIB)

### ✅ Documentation (100%)
- **API_DOCUMENTATION.md** (1200+ Zeilen)
  - Alle 29 Endpoints mit Request/Response Examples
  - Authentifikation Flow mit Laravel Sanctum
  - Error Handling und Rate Limiting
  - iOS Swift Code Examples
  - Android Kotlin Code Examples

- **POSTMAN_COLLECTION.json**
  - Importierbare Collection für Postman/Insomnia
  - 29 vordefinierte Requests in 6 Ordnern
  - Automatische Token-Extraktion nach Login
  - Environment Variables Setup

---

## 🚀 Quick Start für Mobile Devs

### 1. API Testen
```bash
# Postman Collection importieren
1. Postman öffnen
2. Import → File → POSTMAN_COLLECTION.json wählen
3. Environment erstellen: "PeeZ Dev"
4. Variable setzen: base_url = http://your-domain.com/api/v1
5. Test: Auth → Login → Token wird automatisch gespeichert
```

### 2. iOS Integration (Swift)
```swift
// 1. Installiere Dependencies
// Podfile:
pod 'Alamofire', '~> 5.8'
pod 'SwiftyJSON'
pod 'KeychainAccess'

// 2. API Client erstellen
import Alamofire

class PeeZAPIClient {
    static let shared = PeeZAPIClient()
    let baseURL = "https://your-domain.com/api/v1"
    
    private var token: String? {
        get { UserDefaults.standard.string(forKey: "auth_token") }
        set { UserDefaults.standard.set(newValue, forKey: "auth_token") }
    }
    
    private var headers: HTTPHeaders {
        var headers: HTTPHeaders = [
            "Accept": "application/json",
            "Content-Type": "application/json"
        ]
        if let token = token {
            headers.add(name: "Authorization", value: "Bearer \(token)")
        }
        return headers
    }
    
    // Login
    func login(email: String, password: String, completion: @escaping (Result<User, Error>) -> Void) {
        let params: [String: Any] = [
            "email": email,
            "password": password
        ]
        
        AF.request("\(baseURL)/auth/login",
                   method: .post,
                   parameters: params,
                   encoding: JSONEncoding.default)
            .responseDecodable(of: LoginResponse.self) { response in
                switch response.result {
                case .success(let data):
                    self.token = data.token
                    completion(.success(data.user))
                case .failure(let error):
                    completion(.failure(error))
                }
            }
    }
    
    // Get Shops
    func getShops(categoryId: Int? = nil, completion: @escaping (Result<[Shop], Error>) -> Void) {
        var params: [String: Any] = [:]
        if let categoryId = categoryId {
            params["category_id"] = categoryId
        }
        
        AF.request("\(baseURL)/shops",
                   method: .get,
                   parameters: params,
                   headers: headers)
            .responseDecodable(of: ShopsResponse.self) { response in
                switch response.result {
                case .success(let data):
                    completion(.success(data.data))
                case .failure(let error):
                    completion(.failure(error))
                }
            }
    }
    
    // Rate Shop
    func rateShop(shopId: Int, stars: Int, completion: @escaping (Result<Rating, Error>) -> Void) {
        let params: [String: Any] = [
            "shop_id": shopId,
            "stars": stars
        ]
        
        AF.request("\(baseURL)/ratings",
                   method: .post,
                   parameters: params,
                   encoding: JSONEncoding.default,
                   headers: headers)
            .responseDecodable(of: RatingResponse.self) { response in
                switch response.result {
                case .success(let data):
                    completion(.success(data.data))
                case .failure(let error):
                    completion(.failure(error))
                }
            }
    }
}

// 3. Models definieren
struct User: Codable {
    let id: Int
    let uuid: String
    let name: String
    let phone: String
    let email: String
    let isVendor: Bool
    let role: String
    
    enum CodingKeys: String, CodingKey {
        case id, uuid, name, phone, email, role
        case isVendor = "is_vendor"
    }
}

struct Shop: Codable {
    let id: Int
    let name: String
    let address: String
    let phone: String
    let discountPercentage: Double
    let location: Location
    let neighborhood: Neighborhood
    let category: Category
    let rating: ShopRating
    
    enum CodingKeys: String, CodingKey {
        case id, name, address, phone, location, neighborhood, category, rating
        case discountPercentage = "discount_percentage"
    }
}

struct Location: Codable {
    let latitude: Double
    let longitude: Double
}

struct ShopRating: Codable {
    let average: Double
    let count: Int
}

// 4. Verwendung in ViewController
class ShopsViewController: UIViewController {
    override func viewDidLoad() {
        super.viewDidLoad()
        loadShops()
    }
    
    func loadShops() {
        PeeZAPIClient.shared.getShops { result in
            switch result {
            case .success(let shops):
                print("Loaded \(shops.count) shops")
                // Update UI
            case .failure(let error):
                print("Error: \(error)")
                // Show error
            }
        }
    }
}
```

### 3. Android Integration (Kotlin)
```kotlin
// 1. build.gradle (app level)
dependencies {
    implementation 'com.squareup.retrofit2:retrofit:2.9.0'
    implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
    implementation 'com.squareup.okhttp3:logging-interceptor:4.11.0'
    implementation 'org.jetbrains.kotlinx:kotlinx-coroutines-android:1.7.3'
}

// 2. API Service Interface
interface PeeZApiService {
    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): LoginResponse
    
    @GET("shops")
    suspend fun getShops(
        @Query("category_id") categoryId: Int? = null,
        @Query("neighborhood_id") neighborhoodId: Int? = null
    ): ShopsResponse
    
    @POST("ratings")
    suspend fun rateShop(@Body request: RatingRequest): RatingResponse
    
    @GET("subscriptions/status")
    suspend fun getActiveSubscriptions(): SubscriptionsResponse
}

// 3. Retrofit Client Setup
object RetrofitClient {
    private const val BASE_URL = "https://your-domain.com/api/v1/"
    
    private val loggingInterceptor = HttpLoggingInterceptor().apply {
        level = HttpLoggingInterceptor.Level.BODY
    }
    
    private val authInterceptor = Interceptor { chain ->
        val token = PreferenceManager.getToken()
        val request = chain.request().newBuilder()
            .addHeader("Accept", "application/json")
            .apply {
                if (!token.isNullOrEmpty()) {
                    addHeader("Authorization", "Bearer $token")
                }
            }
            .build()
        chain.proceed(request)
    }
    
    private val okHttpClient = OkHttpClient.Builder()
        .addInterceptor(loggingInterceptor)
        .addInterceptor(authInterceptor)
        .build()
    
    val apiService: PeeZApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(PeeZApiService::class.java)
    }
}

// 4. Data Classes
data class LoginRequest(
    val email: String,
    val password: String,
    val fcm_token: String? = null
)

data class LoginResponse(
    val user: User,
    val token: String
)

data class User(
    val id: Int,
    val uuid: String,
    val name: String,
    val phone: String,
    val email: String,
    val is_vendor: Boolean,
    val role: String
)

data class Shop(
    val id: Int,
    val name: String,
    val address: String,
    val phone: String,
    val discount_percentage: Double,
    val location: Location,
    val neighborhood: Neighborhood,
    val category: Category,
    val rating: ShopRating
)

data class Location(
    val latitude: Double,
    val longitude: Double
)

data class ShopRating(
    val average: Double,
    val count: Int
)

// 5. Repository Pattern
class ShopRepository {
    private val apiService = RetrofitClient.apiService
    
    suspend fun getShops(categoryId: Int? = null): Result<List<Shop>> {
        return try {
            val response = apiService.getShops(categoryId = categoryId)
            Result.success(response.data)
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
    
    suspend fun rateShop(shopId: Int, stars: Int): Result<Rating> {
        return try {
            val request = RatingRequest(shopId, stars)
            val response = apiService.rateShop(request)
            Result.success(response.data)
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}

// 6. ViewModel (MVVM Pattern)
class ShopsViewModel : ViewModel() {
    private val repository = ShopRepository()
    
    private val _shops = MutableLiveData<List<Shop>>()
    val shops: LiveData<List<Shop>> = _shops
    
    private val _loading = MutableLiveData<Boolean>()
    val loading: LiveData<Boolean> = _loading
    
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error
    
    fun loadShops(categoryId: Int? = null) {
        viewModelScope.launch {
            _loading.value = true
            repository.getShops(categoryId).fold(
                onSuccess = { shops ->
                    _shops.value = shops
                    _error.value = null
                },
                onFailure = { exception ->
                    _error.value = exception.message
                }
            )
            _loading.value = false
        }
    }
}

// 7. Activity/Fragment Usage
class ShopsActivity : AppCompatActivity() {
    private lateinit var viewModel: ShopsViewModel
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_shops)
        
        viewModel = ViewModelProvider(this)[ShopsViewModel::class.java]
        
        viewModel.shops.observe(this) { shops ->
            // Update RecyclerView
            adapter.submitList(shops)
        }
        
        viewModel.loading.observe(this) { isLoading ->
            progressBar.isVisible = isLoading
        }
        
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }
        
        // Load shops
        viewModel.loadShops()
    }
}
```

---

## 📋 Implementation Checklist

### Backend Setup
- [x] Laravel Sanctum installiert
- [x] API Routes definiert (`routes/api.php`)
- [x] Controllers implementiert (4 Controllers, 18 Methods)
- [x] Resources implementiert (6 Resources)
- [x] Middleware configured (auth:sanctum)

### Authentication Flow
- [x] POST /auth/register - User erstellen
- [x] POST /auth/login - Token erhalten
- [x] POST /auth/logout - Token widerrufen
- [x] GET /auth/me - User Info abrufen
- [x] PUT /auth/profile - Profil aktualisieren
- [x] POST /auth/fcm-token - FCM Token für Push Notifications

### Core Features
- [x] GET /shops - Liste mit Filtern
- [x] GET /shops/nearby - Location-based Suche (Haversine)
- [x] POST /ratings - Shop bewerten (1-5 Sterne)
- [x] GET /subscriptions/status - Aktive Abos
- [x] POST /subscriptions/activate - Neues Abo (Vendor only)

### Testing Tools
- [x] Postman Collection mit 29 Requests
- [x] Environment Variables Setup
- [x] Auto-token Extraction
- [x] Sample Requests/Responses

---

## 🔐 Authentication Details

### Flow:
1. **Register/Login** → Erhalte `token`
2. **Store Token** in KeychainAccess (iOS) / SharedPreferences (Android)
3. **Add Header** bei jedem Request: `Authorization: Bearer {token}`
4. **Handle 401** → Token expired, neu einloggen

### Token Lebensdauer:
- Standardmäßig **unbegrenzt** bis manueller Logout
- Kann in `config/sanctum.php` angepasst werden

---

## 🌍 Location-Based Features

### Nearby Shops (Haversine Formula)
```http
GET /api/v1/shops/nearby?latitude=36.7538&longitude=3.0588&radius=5
```
- Berechnet Entfernung in Kilometern
- Default radius: 5km
- Max radius: 50km
- Sortiert nach Entfernung

### iOS MapKit Integration
```swift
import MapKit

// User Location
let userLocation = CLLocation(latitude: 36.7538, longitude: 3.0588)

// Shop Annotation
class ShopAnnotation: NSObject, MKAnnotation {
    let shop: Shop
    var coordinate: CLLocationCoordinate2D
    var title: String?
    var subtitle: String?
    
    init(shop: Shop) {
        self.shop = shop
        self.coordinate = CLLocationCoordinate2D(
            latitude: shop.location.latitude,
            longitude: shop.location.longitude
        )
        self.title = shop.name
        self.subtitle = "\(shop.discountPercentage)% Discount"
    }
}
```

### Android Google Maps Integration
```kotlin
import com.google.android.gms.maps.GoogleMap
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.MarkerOptions

fun addShopMarkers(map: GoogleMap, shops: List<Shop>) {
    shops.forEach { shop ->
        val position = LatLng(
            shop.location.latitude,
            shop.location.longitude
        )
        map.addMarker(
            MarkerOptions()
                .position(position)
                .title(shop.name)
                .snippet("${shop.discount_percentage}% Discount")
        )
    }
}
```

---

## 🔔 Push Notifications

### FCM Token Handling
```swift
// iOS - Send FCM Token to Backend
func sendFCMToken(_ token: String) {
    PeeZAPIClient.shared.updateFCMToken(token) { result in
        switch result {
        case .success:
            print("FCM token updated")
        case .failure(let error):
            print("Error: \(error)")
        }
    }
}
```

```kotlin
// Android - Send FCM Token
class MyFirebaseMessagingService : FirebaseMessagingService() {
    override fun onNewToken(token: String) {
        super.onNewToken(token)
        
        viewModelScope.launch {
            RetrofitClient.apiService.updateFCMToken(
                FCMTokenRequest(token)
            )
        }
    }
}
```

---

## 📊 Pagination Handling

Alle Listen-Endpunkte sind paginiert (20 Items pro Seite):

```json
{
  "data": [...],
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

### iOS Pagination
```swift
var currentPage = 1
var isLoadingMore = false
var hasMorePages = true

func loadMore() {
    guard !isLoadingMore && hasMorePages else { return }
    
    isLoadingMore = true
    currentPage += 1
    
    PeeZAPIClient.shared.getShops(page: currentPage) { result in
        self.isLoadingMore = false
        switch result {
        case .success(let response):
            self.shops.append(contentsOf: response.data)
            self.hasMorePages = response.meta.currentPage < response.meta.lastPage
        case .failure(let error):
            print("Error: \(error)")
        }
    }
}
```

### Android Pagination (Paging 3)
```kotlin
class ShopsPagingSource : PagingSource<Int, Shop>() {
    override suspend fun load(params: LoadParams<Int>): LoadResult<Int, Shop> {
        val page = params.key ?: 1
        
        return try {
            val response = RetrofitClient.apiService.getShops(page = page)
            LoadResult.Page(
                data = response.data,
                prevKey = if (page == 1) null else page - 1,
                nextKey = if (response.meta.current_page < response.meta.last_page) 
                    page + 1 else null
            )
        } catch (e: Exception) {
            LoadResult.Error(e)
        }
    }
}
```

---

## ⚠️ Error Handling

### HTTP Status Codes
- `200 OK` - Success
- `201 Created` - Resource created
- `401 Unauthorized` - Token missing/invalid
- `403 Forbidden` - Not allowed (z.B. nicht dein Shop)
- `404 Not Found` - Resource nicht gefunden
- `422 Unprocessable Entity` - Validation Error
- `500 Server Error` - Backend Problem

### Validation Errors
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### iOS Error Handling
```swift
enum PeeZError: Error {
    case unauthorized
    case validationError([String: [String]])
    case serverError
    case networkError
}

func handleError(_ error: AFError) -> PeeZError {
    if let statusCode = error.responseCode {
        switch statusCode {
        case 401:
            return .unauthorized
        case 422:
            if let data = error.errorData,
               let json = try? JSONSerialization.jsonObject(with: data),
               let dict = json as? [String: Any],
               let errors = dict["errors"] as? [String: [String]] {
                return .validationError(errors)
            }
        case 500...:
            return .serverError
        }
    }
    return .networkError
}
```

---

## 🧪 Testing Endpoints

### Mit curl:
```bash
# Register
curl -X POST http://your-domain.com/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Ahmed","email":"ahmed@example.com","phone":"+213555123456","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"ahmed@example.com","password":"password123"}'

# Get Shops (with token)
curl -X GET http://your-domain.com/api/v1/shops \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📞 Support

**Backend Developer**: PeeZ Development Team  
**API Version**: v1.0  
**Documentation**: `API_DOCUMENTATION.md`  
**Postman Collection**: `POSTMAN_COLLECTION.json`

---

## ✨ Nächste Schritte

1. **Postman Collection importieren** und alle Endpoints testen
2. **iOS/Android Project aufsetzen** mit Dependencies
3. **API Client implementieren** (Retrofit/Alamofire)
4. **Auth Flow testen** (Register → Login → Store Token)
5. **Features implementieren**:
   - Shop-Liste mit Filtern
   - Map mit Nearby Shops
   - Rating System
   - Subscription Management
6. **Push Notifications** mit FCM integrieren
7. **Error Handling** und Loading States
8. **Testing** auf echten Geräten

---

**Status**: ✅ API Ready for Production  
**Date**: 2025-01-15  
**Dokumentation**: Vollständig
