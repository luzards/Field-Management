# AM Activity Tracker

A complete Area Manager activity tracking system with:
- **Flutter Mobile App** (Android) — for Area Managers
- **Laravel Admin Dashboard** (Web) — for Admins
- **REST API** — connects both

## Quick Start

### Prerequisites
1. **PHP 8.2+** & **Composer** — [getcomposer.org](https://getcomposer.org)
2. **MySQL 8.0+** — [dev.mysql.com/downloads](https://dev.mysql.com/downloads/)
3. **Flutter SDK 3.3+** — [flutter.dev/docs/get-started](https://flutter.dev/docs/get-started/install)
4. **Android Studio** — for emulator & Android SDK

### 1. Laravel Backend Setup

```bash
cd am-tracker-api

# Install dependencies
composer install

# Copy environment file & generate app key
cp .env.example .env
php artisan key:generate

# Edit .env — set your DB credentials:
# DB_DATABASE=am_tracker
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Create database in MySQL
mysql -u root -p -e "CREATE DATABASE am_tracker;"

# Run migrations & seed sample data
php artisan migrate:fresh --seed

# Create storage symlink (for photo uploads)
php artisan storage:link

# Start the server
php artisan serve
```

Dashboard: http://localhost:8000/admin/login

### 2. Flutter App Setup

```bash
cd am-tracker-app

# Get dependencies
flutter pub get

# If using a real device, update API URL in:
# lib/config/api_config.dart
# Change 10.0.2.2 → your computer's local IP

# Run on emulator or device
flutter run
```

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@amtracker.com | password |
| **AM** | john@amtracker.com | password |
| **AM** | jane@amtracker.com | password |
| **AM** | budi@amtracker.com | password |

## Project Structure

```
am-tracker-api/          ← Laravel Backend
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/         ← Mobile API controllers
│   │   └── Admin/       ← Dashboard controllers
│   ├── Models/          ← Eloquent models
│   └── Http/Middleware/ ← Admin auth middleware
├── database/
│   ├── migrations/      ← Database schema
│   └── seeders/         ← Sample data
├── resources/views/admin/ ← Dashboard Blade views
└── routes/
    ├── api.php          ← API routes
    └── web.php          ← Admin routes

am-tracker-app/          ← Flutter Mobile App
└── lib/
    ├── config/          ← API configuration
    ├── models/          ← Data models
    ├── services/        ← API, Auth, GPS, Notifications
    └── screens/         ← UI screens
```

## Key Features

### Mobile App (AM)
- Login with token auth
- View today's & weekly schedules
- GPS + Camera check-in at stores
- 10-meter geofence verification
- Push notifications for reminders
- Check-in history

### Admin Dashboard
- Stats overview dashboard
- CRUD for Area Managers
- CRUD for Stores (with map picker)
- CRUD for Schedules
- Check-in review (photo + map with geofence)
- Activity audit log
