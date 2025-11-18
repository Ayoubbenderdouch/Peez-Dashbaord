# PEEZ iOS App - Comprehensive Development Prompt

## 🎯 Project Overview

**PEEZ** is a neighborhood membership platform for Oran, Algeria, connecting local residents with participating shops through subscription-based memberships. Users pay **300 DZD/month** to access **5-8% discounts** at partner shops across 12 business categories (bakeries, butcher shops, grocery stores, pharmacies, etc.) in 16 neighborhoods.

This prompt defines the requirements for the **iOS client app** (for end-users/customers).

---

## 📱 Platform & Technical Stack

### iOS Requirements
- **Platform**: iOS 15.0+
- **Language**: Swift 5.9+
- **Architecture**: MVVM (Model-View-ViewModel)
- **UI Framework**: SwiftUI preferred, UIKit acceptable
- **Minimum Device**: iPhone 8 and later

### Core Dependencies
```swift
// Networking
- Alamofire ~> 5.8 (HTTP networking)
- SwiftyJSON (JSON parsing)

// Storage
- KeychainAccess (secure token storage)
- CoreData or Realm (local caching)

// UI/UX
- Kingfisher (async image loading)
- SkeletonView (loading states)

// Maps
- MapKit (native Apple Maps)
- CoreLocation (GPS services)

// Push Notifications
- Firebase Cloud Messaging (FCM)
- UserNotifications framework

// QR Code
- AVFoundation (QR scanner)
- CoreImage (QR generation)

// Other
- Combine (reactive programming)
```

---

## 🌐 Backend API Integration

### Base URL
```
Production: https://peez.dz/api/v1
Development: http://localhost:8000/api/v1
```

### Authentication
- **Type**: Bearer Token (Laravel Sanctum)
- **Storage**: Keychain (secure)
- **Header**: `Authorization: Bearer {token}`

### Available Endpoints (30 total)

#### Public Endpoints (no auth required)
1. `POST /auth/register` - Create account
2. `POST /auth/login` - Login
3. `GET /neighborhoods` - List all neighborhoods
4. `GET /categories` - List all categories
5. `GET /shops` - List shops (with filters)
6. `GET /shops/{id}` - Shop details
7. `GET /shops/nearby` - GPS-based shop search

#### Protected Endpoints (auth required)
8. `GET /auth/me` - Get current user
9. `PUT /auth/profile` - Update profile
10. `POST /auth/fcm-token` - Update push token
11. `POST /auth/logout` - Logout
12. `GET /subscriptions/status` - Current subscription
13. `GET /subscriptions/history` - Past subscriptions
14. `POST /ratings` - Rate a shop
15. `GET /ratings/my-ratings` - My ratings
16. `GET /users/{uuid}/card` - Digital membership card with QR

**Full API Documentation**: See `API_DOCUMENTATION_COMPLETE.md` (1365 lines)

---

## 🎨 App Structure & User Flows

### 1. Onboarding Flow (First Launch)
```
Splash Screen (2s)
    ↓
Welcome Screens (3 slides)
    - Slide 1: "Welcome to PEEZ - Your Neighborhood Benefits"
    - Slide 2: "300 DZD/Month - Access 100+ Partner Shops"
    - Slide 3: "5-8% Discounts on Daily Essentials"
    ↓
Login/Register Choice
```

### 2. Authentication Screens

#### Register Screen
**Fields:**
- Full Name (required)
- Phone Number (required, +213 format, unique)
- Email (required, unique)
- Password (min 8 chars, required)
- Password Confirmation (required)

**Features:**
- Input validation (real-time)
- Phone number formatter (Algerian format)
- Show/hide password toggle
- Terms & Privacy checkbox
- "Already have an account?" → Login

**API Call:**
```swift
POST /api/v1/auth/register
{
  "name": "Ahmed Mohamed",
  "phone": "+213555123456",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "fcm_token": "firebase-device-token"
}
```

#### Login Screen
**Fields:**
- Email or Phone
- Password

**Features:**
- Remember me checkbox
- Forgot password link
- Biometric login (Face ID/Touch ID) after first login
- "Don't have an account?" → Register

**API Call:**
```swift
POST /api/v1/auth/login
{
  "email": "ahmed@example.com",
  "password": "password123",
  "fcm_token": "firebase-device-token"
}
```

### 3. Main App Structure (Tab Bar)

```
┌─────────────────────────────────────┐
│   Home   │  Shops  │  Card  │  Me  │
└─────────────────────────────────────┘
```

#### Tab 1: Home
- Welcome header with user name
- Subscription status card (active/expired)
  - If active: Shows end date, days remaining, progress bar
  - If expired: "Renew Now" button → Vendor Activation flow
- Quick Stats:
  - Total shops available in user's neighborhood
  - Total savings this month
  - Shops visited count
- Category carousel (horizontal scroll)
- "Nearby Shops" section (3-5 closest shops)
- "Recently Rated" section

#### Tab 2: Shops (Main Feature)
**Top Section:**
- Search bar (shop name search)
- Filter button → Opens filter sheet:
  - Category (12 options with icons)
  - Neighborhood (16 options)
  - Distance radius (1km, 3km, 5km, 10km)
  - Sort by (Distance, Rating, Discount %)
- Map/List toggle button

**List View:**
- Shop cards showing:
  - Shop name + category icon
  - Neighborhood name
  - Discount percentage (highlighted, e.g., "7.5% OFF")
  - Distance from user (e.g., "1.2 km away")
  - Star rating (e.g., 4.5 ⭐ · 12 reviews)
  - Phone number (tap to call)
  - Navigation button → Opens Apple Maps
- Pull to refresh
- Infinite scroll pagination

**Map View:**
- Apple MapKit integration
- Custom pin annotations with category icons
- User location (blue dot)
- Tap pin → Shows shop card callout
- "List View" button to switch back

**API Calls:**
```swift
GET /api/v1/shops?category_id=1&neighborhood_id=2&page=1
GET /api/v1/shops/nearby?latitude=35.6976&longitude=-0.6337&radius=5
```

#### Tab 3: My Card (Digital Membership)
- Full-screen membership card design:
  - PEEZ logo
  - User name
  - Membership ID (e.g., "PEEZ-20251103-00002")
  - Member since date
  - QR Code (large, centered)
  - Subscription status badge (Active/Expired)
  - Expiry date with countdown
- "How to use" info button
- "Share card" button (screenshot sharing)

**Features:**
- Animated gradient background
- QR code generated from backend
- Auto-refresh when app comes to foreground
- Works offline (cached QR)

**API Call:**
```swift
GET /api/v1/users/{uuid}/card
```

**Response includes:**
- User info
- Subscription details
- QR code as base64 PNG image
- QR payload with signature

#### Tab 4: Profile (Me)
**Sections:**

1. **User Info Card**
   - Avatar (first letter of name)
   - Name
   - Email
   - Phone
   - "Edit Profile" button

2. **Subscription Section**
   - Current plan card
   - "View History" → Subscription History screen
   - "Activate/Extend" button → Vendor Activation info

3. **Activity Section**
   - My Ratings (list of rated shops)
   - Favorite Shops (coming soon)

4. **App Settings**
   - Notifications toggle
   - Language (Arabic/French)
   - Dark mode toggle
   - About PEEZ
   - Help & Support
   - Terms & Privacy

5. **Logout Button** (red, bottom)

---

## 📱 Detailed Screen Specifications

### Shop Detail Screen

**Navigation:** From Shops list → Tap shop card

**Content:**
1. **Header Image/Map Preview**
   - Small map showing shop location
   - Category icon overlay

2. **Shop Info Card**
   - Shop name (large, bold)
   - Category badge
   - Neighborhood
   - Discount percentage (prominent, green badge)
   - Open/Closed status (if available)

3. **Action Buttons Row**
   - 📞 Call (tel: link)
   - 🧭 Navigate (Apple Maps deep link)
   - ⭐ Rate (opens rating modal)

4. **Details Section**
   - Address (full)
   - Phone number
   - GPS coordinates (latitude, longitude)

5. **Ratings & Reviews Section**
   - Average rating (large, e.g., 4.5/5.0)
   - Rating distribution bar chart:
     - 5 stars: ████████ 8
     - 4 stars: ████ 3
     - 3 stars: ██ 1
     - 2 stars: 0
     - 1 star: 0
   - Reviews list (paginated):
     - User name
     - Star rating
     - Comment
     - Date
   - "Load More" button

**API Calls:**
```swift
GET /api/v1/shops/{id}
GET /api/v1/ratings?shop_id={id}
```

---

### Rating Modal (Sheet)

**Triggered by:** "Rate" button on shop detail

**Content:**
- Shop name + category
- Star rating selector (1-5, tap to select)
  - Large, animated stars
  - Yellow color for selected
- Comment text field (optional, max 500 chars)
- Character counter
- "Submit Rating" button (disabled until stars selected)

**Business Rule:**
- Users can only rate each shop once
- If already rated → Show "You rated this shop X stars"

**API Call:**
```swift
POST /api/v1/ratings
{
  "shop_id": 1,
  "stars": 5,
  "comment": "Excellent service and quality!"
}
```

**Error Handling:**
- If already rated: "You have already rated this shop"
- Show edit option to update rating (future)

---

### Subscription History Screen

**Navigation:** Profile → View History

**Content:**
- List of all past subscriptions (chronological, newest first)
- Each item shows:
  - Status badge (Active/Expired/Cancelled)
  - Start date
  - End date
  - Duration (1/2/3 months)
  - Source (Vendor/In-App)
  - Price (300 DZD/month)
- Empty state if no history

**API Call:**
```swift
GET /api/v1/subscriptions/history
```

---

### Vendor Activation Info Screen

**Purpose:** Explain how to activate/renew subscription

**Content:**
1. **Hero Section**
   - Illustration of vendor scanning QR
   - Headline: "Activate at Any Partner Shop"

2. **How it Works (3 Steps)**
   - Step 1: Visit any PEEZ partner shop
   - Step 2: Show your QR code to the vendor
   - Step 3: Choose plan (1/2/3 months)
   - Step 4: Pay 300 DZD/month in cash

3. **Pricing Card**
   - 1 Month: 300 DZD
   - 2 Months: 600 DZD (save 0%)
   - 3 Months: 900 DZD (save 0%)

4. **Find Shops Button**
   - → Navigates to Shops tab with filter: "All shops"

5. **Future: In-App Payment (Coming Soon)**
   - SlickPay / CIB Bank integration badge
   - "Notify me" button

---

### Search Results Screen

**Triggered by:** Search bar on Shops tab

**Features:**
- Real-time search (debounced, 300ms)
- Search by shop name
- Recent searches (local storage)
- Clear search button
- Results count: "12 shops found"
- Same list/map view as main Shops tab

**API Call:**
```swift
GET /api/v1/shops?search={query}
```

---

### Notifications Screen

**Navigation:** Bell icon (top right, any screen)

**Content:**
- List of push notifications
- Each notification:
  - Icon (based on type)
  - Title
  - Message
  - Timestamp (relative, e.g., "2 hours ago")
  - Unread indicator (blue dot)
- Mark all as read button
- Empty state: "No notifications yet"

**Notification Types:**
1. Subscription expiring soon (7 days, 3 days, 1 day)
2. New shop in your neighborhood
3. Special offers/campaigns
4. App updates

---

## 🎨 UI/UX Design Guidelines

### Color Palette
```swift
// Primary Colors
- Brand Orange: #F97316 (UIColor.systemOrange)
- Brand Amber: #F59E0B
- Accent Green: #10B981 (for success states)

// Backgrounds
- Light mode: #FFFFFF, #F9FAFB
- Dark mode: #1F2937, #111827

// Text
- Primary: #111827 (dark) / #F9FAFB (dark mode)
- Secondary: #6B7280
- Tertiary: #9CA3AF

// Status Colors
- Success: #10B981 (active subscription)
- Warning: #F59E0B (expiring soon)
- Danger: #EF4444 (expired)
- Info: #3B82F6
```

### Typography
```swift
// SF Pro (System Font)
- Display: Bold, 28pt (hero titles)
- Headline: Semibold, 20pt (screen titles)
- Body: Regular, 16pt (main content)
- Callout: Medium, 15pt (card titles)
- Caption: Regular, 13pt (metadata)
```

### Component Library

#### Shop Card Component
```
┌─────────────────────────────────────┐
│ 🥖 Boulangerie du Centre        7.5%│ ← Badge
│ Sidi El Houari · 1.2 km away       │
│ ⭐ 4.5 · 12 reviews                 │
│ 📞 +213 555 111 222   [Navigate →] │
└─────────────────────────────────────┘
```

#### Subscription Status Card
```
┌─────────────────────────────────────┐
│ ✅ Active Membership                │
│ ━━━━━━━━━━━━━━━━━━━━━━━━ 75%       │ ← Progress bar
│ 89 days remaining                   │
│ Expires: Feb 4, 2026                │
│                     [Extend →]      │
└─────────────────────────────────────┘
```

### Animations
- Smooth transitions (0.3s ease-in-out)
- Pull-to-refresh with spring animation
- Card tap: Scale down 0.95x + haptic feedback
- Star rating: Bounce animation on tap
- Loading: Skeleton screens (not spinners)

### Icons
- Use SF Symbols (native iOS icons)
- Category icons: Custom emoji or vector assets
  - 🥖 Bakery
  - 🥩 Butcher
  - 🛒 Grocery
  - 💊 Pharmacy
  - etc.

---

## 🔔 Push Notifications

### Firebase Cloud Messaging Setup

1. **FCM Token Management**
   - Request permission on first launch
   - Send token to backend on login/register
   - Update token when refreshed
   - Clear token on logout

**API Call:**
```swift
POST /api/v1/auth/fcm-token
{
  "fcm_token": "firebase-device-token-here"
}
```

2. **Notification Types**

| Type | Title | Body | Deep Link |
|------|-------|------|-----------|
| Subscription Expiring | "Your PEEZ membership expires in 3 days" | "Renew at any partner shop" | Profile → Activate |
| New Shop | "New bakery in Sidi El Houari" | "Boulangerie Moderne offers 7% discount" | Shop Detail |
| Campaign | "Special offer this weekend!" | "Extra 2% discount at all pharmacies" | Shops List |
| Rating Request | "Rate your recent visit" | "How was Boulangerie du Centre?" | Shop Detail |

3. **In-App Notification Handling**
```swift
// When app is in foreground: Show banner
// When app is in background: System notification
// On tap: Navigate to deep link
```

---

## 🗺 Location Services

### Required Permissions
- **When In Use**: For nearby shops feature
- **Purpose String**: "PEEZ needs your location to find nearby partner shops and calculate distances."

### Features

1. **Nearby Shops (API-driven)**
```swift
GET /api/v1/shops/nearby?latitude=35.6976&longitude=-0.6337&radius=5
```
- Uses Haversine formula (backend)
- Returns shops with `distance_km` field
- Default radius: 5km
- Max radius: 50km

2. **Map View**
- Show user location (blue dot)
- Custom annotations for shops (category icons)
- Callout shows: name, discount, rating
- "Navigate" button → Apple Maps directions

3. **Location Updates**
- Request location when opening Shops tab
- Update on pull-to-refresh
- Cache last location (1 hour)

---

## 💾 Local Storage & Caching

### UserDefaults
- Auth token (migrate to Keychain)
- User preferences (language, theme)
- Last selected filters
- Onboarding completed flag

### Keychain
- **Auth token** (secure storage)
- User UUID

### Core Data / Realm (Optional)
- Cached shops (offline access)
- Subscription history
- Notifications
- My ratings

**Cache Strategy:**
- Cache shops list for 1 hour
- Cache user profile for 30 minutes
- Always fetch subscription status (no cache)
- Cache QR code image for offline access

---

## 🔐 Security Requirements

1. **Token Management**
   - Store in Keychain (not UserDefaults)
   - Clear on logout
   - Refresh on 401 response

2. **SSL Pinning** (Optional but recommended)
   - Pin production SSL certificate
   - Prevent man-in-the-middle attacks

3. **Input Validation**
   - Sanitize all user inputs
   - Validate email/phone formats
   - Password strength indicator

4. **QR Code Security**
   - Verify signature on scan (backend handles)
   - Check expiry date
   - Don't allow screenshots in production (optional)

---

## 📊 Analytics & Tracking

### Events to Track (Firebase Analytics)

**User Actions:**
- `app_open`
- `signup_completed`
- `login_completed`
- `shop_viewed` (shop_id, category)
- `shop_rated` (shop_id, stars)
- `shop_called` (shop_id)
- `shop_navigated` (shop_id)
- `qr_code_viewed`
- `filter_applied` (category, neighborhood)
- `search_performed` (query)

**Business Metrics:**
- `subscription_status_checked`
- `activation_info_viewed`
- `subscription_expired_warning_shown`

**Crashes & Errors:**
- Use Firebase Crashlytics
- Log API errors with context

---

## ⚠️ Error Handling

### Network Errors

```swift
enum PeeZAPIError: Error, LocalizedError {
    case unauthorized           // 401
    case forbidden             // 403
    case notFound              // 404
    case validationError([String: [String]])  // 422
    case serverError           // 500+
    case networkUnavailable    // No connection
    case timeout
    case invalidResponse

    var errorDescription: String? {
        switch self {
        case .unauthorized:
            return "Session expired. Please login again."
        case .forbidden:
            return "You don't have permission to perform this action."
        case .notFound:
            return "The requested resource was not found."
        case .validationError(let errors):
            return errors.values.first?.first ?? "Invalid data"
        case .serverError:
            return "Something went wrong. Please try again later."
        case .networkUnavailable:
            return "No internet connection. Please check your network."
        case .timeout:
            return "Request timed out. Please try again."
        case .invalidResponse:
            return "Received invalid response from server."
        }
    }
}
```

### User-Facing Error Messages

**Show inline errors for:**
- Form validation (email format, password length)
- Rating already submitted
- Subscription expired (when viewing shops)

**Show alerts for:**
- Network failures
- Server errors
- Authentication failures

**Show toast/banner for:**
- Success messages (rating submitted, profile updated)
- Info messages (location permission required)

### Retry Mechanisms
- Auto-retry failed API calls (3 times, exponential backoff)
- Pull-to-refresh for manual retry
- "Try Again" button on error states

---

## 🧪 Testing Requirements

### Unit Tests
- ViewModel logic
- API request/response parsing
- Business rules (discount calculation, date formatting)
- Input validation

### UI Tests
- Login/Register flow
- Shop search and filtering
- Rating submission
- QR code display

### Manual Testing Checklist
- [ ] Registration with valid/invalid data
- [ ] Login with correct/incorrect credentials
- [ ] View shops list (with/without filters)
- [ ] Shop detail page loads correctly
- [ ] Submit rating (first time and duplicate)
- [ ] View QR code (active/expired subscription)
- [ ] Location permission flow
- [ ] Map view with custom pins
- [ ] Push notification handling
- [ ] Offline mode (cached data)
- [ ] Dark mode support
- [ ] Different iPhone sizes (SE, 12, 14 Pro Max)
- [ ] iOS 15, 16, 17 compatibility

---

## 🌍 Localization

### Supported Languages
1. **French** (default) - Oran is French-speaking
2. **Arabic** (RTL support) - Native language

### Key Strings to Translate
- All UI labels and buttons
- Error messages
- Onboarding content
- Category names (from API)
- Neighborhood names (from API)

### Implementation
```swift
// Use NSLocalizedString
Text("welcome_message".localized)

// Support RTL layout
.environment(\.layoutDirection, isArabic ? .rightToLeft : .leftToRight)
```

---

## 📦 Deliverables

### Phase 1: MVP (4-6 weeks)
- [ ] Onboarding screens
- [ ] Authentication (register, login, logout)
- [ ] Home tab (basic stats)
- [ ] Shops tab (list view, filters)
- [ ] Shop detail screen
- [ ] My Card tab (QR code)
- [ ] Profile tab (basic info)
- [ ] Rating functionality
- [ ] Push notification setup

### Phase 2: Enhanced Features (2-3 weeks)
- [ ] Map view for shops
- [ ] Nearby shops with GPS
- [ ] Subscription history
- [ ] My ratings list
- [ ] Search functionality
- [ ] Dark mode support
- [ ] Arabic localization

### Phase 3: Polish & Optimization (1-2 weeks)
- [ ] Animations and transitions
- [ ] Offline mode
- [ ] Performance optimization
- [ ] Analytics integration
- [ ] App Store assets (screenshots, description)
- [ ] TestFlight beta testing

---

## 🚀 Deployment

### App Store Submission

**Metadata:**
- **App Name**: PEEZ - Neighborhood Benefits
- **Subtitle**: Save on daily shopping in Oran
- **Category**: Lifestyle / Shopping
- **Age Rating**: 4+ (No restricted content)

**Description:**
```
PEEZ connects you with 100+ local partner shops in Oran, Algeria.

For just 300 DZD/month, unlock:
✅ 5-8% discounts at bakeries, butcher shops, grocery stores, and more
✅ Access to 12 business categories across 16 neighborhoods
✅ Digital membership card with QR code
✅ Real-time shop locations and ratings
✅ Exclusive member benefits

How it works:
1. Sign up in seconds
2. Activate your membership at any partner shop
3. Show your QR code to save on every purchase

Download PEEZ today and start saving on your daily essentials!
```

**Keywords:**
`discounts, oran, algeria, shopping, membership, local shops, savings, neighborhood, peez`

**Screenshots Required:**
- 6.5" Display (iPhone 14 Pro Max)
- 5.5" Display (iPhone 8 Plus)
- iPad Pro (optional)

**Privacy Policy Required:**
- Data collection: name, email, phone, location
- Push notification tokens
- Usage analytics

---

## 🔄 Future Features (Roadmap)

### Version 1.1
- [ ] In-app subscription purchase (SlickPay/CIB)
- [ ] Favorite shops
- [ ] Share shop with friends
- [ ] Wallet integration (Apple Pay)

### Version 1.2
- [ ] Shop opening hours
- [ ] Special offers/campaigns section
- [ ] Referral program (invite friends)
- [ ] Achievements/badges

### Version 1.3
- [ ] Vendor app integration (separate app)
- [ ] Review photos upload
- [ ] Shop recommendations based on history
- [ ] Multi-city support (beyond Oran)

---

## 📞 Support & Resources

### Backend Team Contact
- **API Base URL**: https://peez.dz/api/v1
- **API Documentation**: `API_DOCUMENTATION_COMPLETE.md` (30 endpoints)
- **Mobile Dev Guide**: `MOBILE_DEV_GUIDE.md`
- **Postman Collection**: `POSTMAN_COLLECTION.json`

### Design Assets
- Brand logo (SVG + PNG)
- Category icons (12 icons)
- Membership card template
- App icon (1024x1024)

### Test Accounts
```
User Account:
Email: ahmed@example.com
Password: password123
UUID: 7eb37919-a105-4854-be79-26e93e953eb2

Vendor Account (for testing activation):
Email: vendor@peez.dz
Password: password
Shop: Boucherie Centrale
```

---

## ✅ Success Criteria

**App is successful when users can:**
1. Register and login in <30 seconds
2. Find nearby shops with 3 taps
3. View their digital QR code instantly
4. Rate shops in <10 seconds
5. Check subscription status at a glance

**Performance Targets:**
- App launch: <2 seconds (cold start)
- API response time: <500ms (average)
- Crash rate: <1%
- App Store rating: >4.5 stars

---

## 🎓 Implementation Example: Login Flow

```swift
// MARK: - LoginViewModel.swift
import Combine
import Foundation

class LoginViewModel: ObservableObject {
    @Published var email: String = ""
    @Published var password: String = ""
    @Published var isLoading: Bool = false
    @Published var errorMessage: String?
    @Published var isAuthenticated: Bool = false

    private let apiClient = PeeZAPIClient.shared
    private var cancellables = Set<AnyCancellable>()

    var isFormValid: Bool {
        !email.isEmpty && email.isValidEmail && password.count >= 8
    }

    func login() {
        guard isFormValid else {
            errorMessage = "Please enter valid credentials"
            return
        }

        isLoading = true
        errorMessage = nil

        apiClient.login(email: email, password: password)
            .receive(on: DispatchQueue.main)
            .sink { [weak self] completion in
                self?.isLoading = false
                if case .failure(let error) = completion {
                    self?.errorMessage = error.localizedDescription
                }
            } receiveValue: { [weak self] response in
                // Save token
                KeychainManager.shared.saveToken(response.token)

                // Save user
                UserDefaultsManager.shared.saveUser(response.user)

                // Update state
                self?.isAuthenticated = true

                // Request notification permission
                NotificationManager.shared.requestPermission()
            }
            .store(in: &cancellables)
    }
}

// MARK: - LoginView.swift (SwiftUI)
import SwiftUI

struct LoginView: View {
    @StateObject private var viewModel = LoginViewModel()
    @State private var showPassword = false

    var body: some View {
        NavigationView {
            VStack(spacing: 24) {
                // Logo
                Image("peez_logo")
                    .resizable()
                    .scaledToFit()
                    .frame(height: 100)

                // Email Field
                VStack(alignment: .leading, spacing: 8) {
                    Text("Email or Phone")
                        .font(.callout)
                        .foregroundColor(.secondary)

                    TextField("ahmed@example.com", text: $viewModel.email)
                        .textFieldStyle(.roundedBorder)
                        .keyboardType(.emailAddress)
                        .autocapitalization(.none)
                        .disabled(viewModel.isLoading)
                }

                // Password Field
                VStack(alignment: .leading, spacing: 8) {
                    Text("Password")
                        .font(.callout)
                        .foregroundColor(.secondary)

                    HStack {
                        if showPassword {
                            TextField("Enter password", text: $viewModel.password)
                        } else {
                            SecureField("Enter password", text: $viewModel.password)
                        }

                        Button {
                            showPassword.toggle()
                        } label: {
                            Image(systemName: showPassword ? "eye.slash" : "eye")
                                .foregroundColor(.secondary)
                        }
                    }
                    .padding(12)
                    .background(Color(.systemGray6))
                    .cornerRadius(8)
                    .disabled(viewModel.isLoading)
                }

                // Error Message
                if let error = viewModel.errorMessage {
                    Text(error)
                        .font(.caption)
                        .foregroundColor(.red)
                        .frame(maxWidth: .infinity, alignment: .leading)
                }

                // Login Button
                Button {
                    viewModel.login()
                } label: {
                    HStack {
                        if viewModel.isLoading {
                            ProgressView()
                                .tint(.white)
                        } else {
                            Text("Login")
                                .fontWeight(.semibold)
                        }
                    }
                    .frame(maxWidth: .infinity)
                    .padding()
                    .background(viewModel.isFormValid ? Color.orange : Color.gray)
                    .foregroundColor(.white)
                    .cornerRadius(12)
                }
                .disabled(!viewModel.isFormValid || viewModel.isLoading)

                // Forgot Password
                Button("Forgot Password?") {
                    // Navigate to forgot password
                }
                .font(.callout)
                .foregroundColor(.orange)

                Spacer()

                // Register Link
                HStack {
                    Text("Don't have an account?")
                        .foregroundColor(.secondary)
                    NavigationLink("Sign Up") {
                        RegisterView()
                    }
                    .fontWeight(.semibold)
                    .foregroundColor(.orange)
                }
            }
            .padding(24)
            .navigationTitle("Welcome Back")
            .navigationBarTitleDisplayMode(.large)
        }
        .fullScreenCover(isPresented: $viewModel.isAuthenticated) {
            MainTabView()
        }
    }
}
```

---

## 📄 Summary

This iOS app will provide PEEZ users in Oran, Algeria with a seamless mobile experience to:
- Discover 100+ partner shops across 12 categories
- Manage their 300 DZD/month subscription
- Access their digital membership card with QR code
- Find nearby shops using GPS
- Rate and review shops
- Save 5-8% on daily purchases

**Key Differentiators:**
- Location-based shop discovery
- Secure QR code membership card
- Offline support for cached data
- Clean, modern SwiftUI interface
- Full Arabic RTL support

**Timeline:** 7-11 weeks from start to App Store submission

---

**Document Version**: 1.0
**Created**: November 8, 2025
**For**: PEEZ iOS App Development
**Backend**: Laravel 12 REST API (30 endpoints)
**Target Launch**: Q1 2026
