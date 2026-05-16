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

## Authentication

This API uses **Laravel Sanctum** for token-based authentication.

### Flow
1. Register an account via `POST /api/register`
2. Login via `POST /api/login` to receive a Bearer token
3. Include the token in all subsequent requests:
```
Authorization: Bearer YOUR_TOKEN_HERE
```
4. Logout via `POST /api/logout` to revoke the token

## API Endpoints

### Public Routes — No authentication required

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user account |
| POST | `/api/login` | Login and receive an API token |

### Protected Routes — Require `Authorization: Bearer TOKEN`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout and revoke token |
| POST | `/api/monitors` | Register a URL to monitor |
| GET | `/api/monitors` | List your monitored URLs |
| GET | `/api/monitors/{id}/history` | Get check history for a monitor |

> ⚠️ All monitor endpoints are scoped to the authenticated user.
> Users can only see and manage their own monitors.

## Request & Response Examples

### Register
```json
POST /api/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
}
```

### Login
```json
POST /api/login
{
    "email": "john@example.com",
    "password": "password"
}
```

### Add a Monitor
```json
POST /api/monitors
Authorization: Bearer YOUR_TOKEN_HERE

{
    "url": "https://example.com",
    "check_interval": 5,
    "threshold": 3
}
```

## Notifications

When a monitored site goes **down** or **recovers**, an email notification is automatically
sent to the **owner of that monitor** (the user who registered the URL).

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
  ✓ cannot see other users monitors
  ✓ can get check history
  ✓ history returns 404 for missing monitor
  ✓ unauthenticated user cannot access monitors

  Tests:  9 passed
```