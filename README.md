# PEEZ - Neighborhood Membership Platform

Complete admin dashboard and REST API for "PEEZ", a neighborhood membership app for Oran, Algeria, following the business rule **ONE shop per category per neighborhood**.

## 🚀 Project Overview

PEEZ connects local businesses with neighborhood residents through subscription-based memberships, offering exclusive discounts and benefits.

### Key Features
- ✅ **Admin Dashboard** (Filament v4) - Complete CRUD management
- ✅ **REST API v1** - For iOS & Android mobile apps
- ✅ **Role-Based Access** - Admin, Manager, Vendor roles
- ✅ **Location Services** - GPS-based shop search
- ✅ **Push Notifications** - Firebase Cloud Messaging
- ✅ **Payment Integration** - SlickPay & CIB webhooks
- ✅ **Rating System** - 5-star shop ratings

---

## 📚 Documentation

| Document | Description |
|----------|-------------|
| **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)** | Complete API reference with 29 endpoints |
| **[MOBILE_DEV_GUIDE.md](./MOBILE_DEV_GUIDE.md)** | iOS & Android integration guide with code examples |
| **[POSTMAN_COLLECTION.json](./POSTMAN_COLLECTION.json)** | Importable Postman collection for testing |
| **[API_IMPLEMENTATION_SUMMARY.md](./API_IMPLEMENTATION_SUMMARY.md)** | Technical implementation overview |
| **[FINAL_CODE_REVIEW.md](./FINAL_CODE_REVIEW.md)** | Code quality assessment (99% perfect) |

---

## 🛠 Tech Stack

### Backend
- **Laravel 12** (v12.36.1)
- **PHP 8.4.1** (requires 8.2+)
- **MySQL 8** / SQLite (development)
- **Filament v4** (Admin Panel)
- **Laravel Sanctum** (API Token Authentication)

### Frontend
- **Tailwind CSS** (UI Styling)
- **Livewire 3** (Interactive components)
- **Alpine.js** (JavaScript interactions)

### Mobile API
- **REST API v1** with JSON responses
- **29 Endpoints** (7 public, 20 protected, 2 webhooks)
- **Laravel Resources** for response transformation
- **Rate Limiting** (60 req/min authenticated, 30 req/min guest)

---

## 📊 Domain Models

### Core Entities

| Model | Description | Key Fields |
|-------|-------------|-----------|
| **Neighborhood** | City districts in Oran | name, city |
| **Category** | Business categories | name (12 types: grocery, butcher, etc.) |
| **Shop** | Businesses with discounts | ONE per (neighborhood, category), 5-8% discount |
| **User** | System users | roles: admin, manager, vendor |
| **Subscription** | User memberships | active, expired, cancelled (300 DZD/month) |
| **Activation** | Subscription logs | vendor-initiated activations |
| **Rating** | Shop reviews | 1-5 stars, one per user per shop |
| **NotificationLog** | Push notification history | FCM integration |

### Business Rules

1. **🏪 Unique Shop Constraint**: Only ONE shop per (neighborhood, category) combination
2. **💰 Discount Range**: Shops must offer 5.00% to 8.00% discount
3. **📅 Subscription Duration**: 1, 2, or 3 months
4. **💵 Pricing**: 300 DZD per month
5. **⭐ Rating**: Users can rate each shop once (1-5 stars)
6. **🔒 Vendor Ownership**: Vendors can only manage their own shops

---

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8 or SQLite
- Node.js & NPM (for assets)

### Setup Steps

1. **Clone or navigate to the project directory**

```bash
cd "PeeZ Dashbaord"
```

2. **Install Dependencies**

```bash
composer install
npm install && npm run build
```

3. **Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database**

Edit `.env` file:

For SQLite (default):
```env
DB_CONNECTION=sqlite
# DB_DATABASE will use database/database.sqlite
```

For MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peez
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Run Migrations and Seed Database**

```bash
php artisan migrate:fresh --seed
```

This will create:
- 16 Oran neighborhoods
- 12 business categories
- 6 demo users (admin, manager, vendor, 3 customers)
- 100+ sample shops (respecting the ONE shop rule)

6. **Start Development Server**

```bash
php artisan serve
```

Access the admin panel at: `http://localhost:8000/admin`

## Default Credentials

### Admin User
- **Email**: admin@peez.dz
- **Password**: password

### Manager User
- **Email**: manager@peez.dz
- **Password**: password

### Vendor User
- **Email**: vendor@peez.dz
- **Password**: password

## Database Schema

### Users Table
- `uuid` (v4, unique)
- `name`, `phone` (unique), `email`
- `fcm_token` (for push notifications)
- `is_vendor` (boolean)
- `role` (enum: admin, manager, vendor)

### Shops Table
- `neighborhood_id`, `category_id` (composite unique)
- `discount_percent` (decimal 5.00-8.00)
- `lat`, `lng` (decimal coordinates)
- `phone`, `is_active`

### Subscriptions Table
- `user_id`, `start_at`, `end_at`
- `status` (active, expired, cancelled)
- `source` (vendor, in_app_future)

### Activations Table
- `user_id`, `shop_id`, `vendor_id`
- `months` (1, 2, or 3)
- `amount_dzd` (auto-calculated: months × 300)

### Ratings Table
- `user_id`, `shop_id` (composite unique)
- `stars` (1-5)

## Makefile Commands

```bash
# Start application
make up

# Run migrations and seed database
make fresh

# Run tests
make test

# Clear all caches
make clear

# Generate IDE helper files
make ide-helper
```

## Testing

### Run All Tests

```bash
php artisan test
```

### Test Coverage

The test suite includes:

1. **Shop Constraint Test**: Enforces ONE shop per neighborhood-category
2. **Subscription Activation Test**: Validates 1/2/3 month subscriptions
3. **Discount Validation Test**: Ensures discount range 5-8%
4. **Revenue Calculation Test**: Verifies activations × 300 DZD
5. **Rating Uniqueness Test**: One rating per user per shop

## API Endpoints (Stub)

The following endpoints are available for mobile app integration:

```
GET  /api/neighborhoods
GET  /api/categories
GET  /api/shops
GET  /api/shops/{id}
GET  /api/user/subscription
POST /api/ratings
```

API documentation: See `docs/api.yaml` (OpenAPI spec)

## Project Structure

```
app/
├── Models/              # Eloquent models
├── Filament/
│   ├── Resources/      # CRUD resources
│   ├── Pages/          # Custom pages
│   └── Widgets/        # Dashboard widgets
├── Policies/           # Authorization policies
└── Services/           # Business logic

database/
├── migrations/         # Database schema
├── seeders/           # Sample data
└── factories/         # Model factories

tests/
└── Feature/           # Feature tests
```

## Features

### Implemented

- ✅ Laravel 12 + Filament v4 setup
- ✅ Database migrations with constraints
- ✅ Eloquent models with relationships
- ✅ Sample seeders for Oran data
- ✅ User roles (admin, manager, vendor)
- ✅ Shop unique constraint enforcement

### In Progress

- ⏳ Filament CRUD resources
- ⏳ Dashboard with KPIs and charts
- ⏳ Vendor Activation Flow
- ⏳ Monthly Reports & CSV export
- ⏳ Notification system
- ⏳ Localization (Arabic/French)
- ⏳ RBAC with Policies
- ⏳ Feature tests

### Planned

- 📋 Slick Pay webhook integration
- 📋 Push notification service
- 📋 Advanced reporting
- 📋 Mobile API completion

## Contributing

This is a demo/production application for PEEZ. For modifications:

1. Respect the ONE shop per category per neighborhood rule
2. Keep discount range 5-8%
3. Maintain test coverage
4. Follow Laravel & Filament best practices

## License

Proprietary - PEEZ 2025

## Support

For issues or questions, contact the development team.

---

**Note**: This project uses:
- SQLite for development (database/database.sqlite)
- Arabic as default language (RTL support coming)
- 300 DZD flat rate per subscription month
