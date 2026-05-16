# Uptime Monitor API

A Laravel 13 API for monitoring website uptime with notifications.

## Requirements
- PHP 8.4+
- Composer
- MySQL 8+

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
MAIL_MAILER=smtp          # Use 'log' to just log emails locally
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

Tests use a **separate MySQL test database** to avoid affecting your development data.

### 1. Create the Test Database
```bash
mysql -u root -e "CREATE DATABASE uptime_monitor_test;"
```

### 2. Run the Test Suite
```bash
php artisan test
```

The `phpunit.xml` is pre-configured to use MySQL with the `uptime_monitor_test` database, 
with session, cache, queue, and mail drivers all set to `array` for fast isolated testing.

### Expected Output
```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\MonitorApiTest
  ✓ can create monitor
  ✓ duplicate url is rejected
  ✓ url is required
  ✓ can list monitors
  ✓ can get check history
  ✓ history returns 404 for missing monitor

  Tests:  7 passed
```