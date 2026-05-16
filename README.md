# Uptime Monitor API

A Laravel 13 API for monitoring website uptime with notifications.

## Requirements
- PHP 8.4+
- Composer
- MySQL 8+ (or SQLite for local dev)

## Setup Instructions

### 1. Clone & Install
```bash
git clone https://github.com/sunnyakpan/uptime-monitor.git
cd uptime-monitor
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure Environment
Edit `.env` with your database and mail credentials.

Key variables:
```env
DB_DATABASE=uptime_monitor_db
MAIL_MAILER=smtp          # You can Use 'log' to just log emails locally
UPTIME_NOTIFICATION_EMAIL=you@example.com
QUEUE_CONNECTION=sync     # Use 'database' for production
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Start the Application
```bash
php artisan serve
```

### 5. Start the Scheduler (in a separate terminal)
```bash
php artisan schedule:work
```

### 6. (Optional) Start Queue Worker
```bash
php artisan queue:work
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/monitors` | Register a URL to monitor |
| GET | `/api/monitors` | List all monitors |
| GET | `/api/monitors/{id}/history` | Get check history |

## Running Tests
```bash
php artisan test
```