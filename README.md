# PO CAN Travel - Bus Ticket Booking Platform

An efficient and user-friendly bus ticket booking platform built with Laravel 10. It allows users to quickly search, select, and book bus tickets with a secure REST API for mobile applications.

## Features

✅ User Authentication (Register, Login, Logout)
✅ Role-based Access (Customer, Admin)
✅ Bus Schedule Search & Filtering
✅ Ticket Booking System
✅ Order Management
✅ Payment Integration
✅ REST API with Layered Security:
  - API Key Protection
  - Bearer Token Authentication
  - Token Expiration & Validation
  - Standard JSON Response Format

## Tech Stack

- Laravel 10
- MySQL Database
- Laravel Sanctum (API Authentication)
- Eloquent ORM
- Repository Pattern
- Service Layer Architecture
- Request Validation

## Database Schema

See [DATABASE_ERD.md](DATABASE_ERD.md) for complete Entity Relationship Diagram.

### Main Tables:
- users
- buses
- routes
- schedules
- orders
- tickets
- payments
- api_keys

## Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM (optional, for frontend assets)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd po-can-travel
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=po_can_travel
DB_USERNAME=root
DB_PASSWORD=
```

5. **Create database**
```bash
mysql -u root -p
CREATE DATABASE po_can_travel;
exit;
```

6. **Run migrations and seeders**
```bash
php artisan migrate:fresh --seed
```

7. **Start the development server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete API reference.

### Base URL
```
http://localhost:8000/api
```

### Security Layers

1. **API Key Protection** (All endpoints)
   - Header: `X-API-Key: {your_api_key}`

2. **Bearer Token** (Protected endpoints)
   - Header: `Authorization: Bearer {token}`

3. **Token Expiration**
   - Tokens expire after 30 days

### Quick Start

1. Get API Key from database:
```sql
SELECT api_key FROM api_keys WHERE status = 1 LIMIT 1;
```

2. Register a new user:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890"
  }'
```

3. Search schedules:
```bash
curl -X GET "http://localhost:8000/api/schedules?origin=Jakarta&destination=Bandung" \
  -H "X-API-Key: YOUR_API_KEY"
```

4. Create booking:
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "schedule_id": 1,
    "tickets": [
      {
        "seat_number": "A1",
        "passenger_name": "John Doe"
      }
    ],
    "payment_method": "transfer"
  }'
```

## Testing Credentials

### Admin Account
- Email: `admin@pocantravel.com`
- Password: `password`

### Customer Account
- Email: `customer@example.com`
- Password: `password`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── BookingController.php
│   │       └── ScheduleController.php
│   ├── Middleware/
│   │   └── ValidateApiKey.php
│   └── Requests/
│       ├── LoginRequest.php
│       ├── RegisterRequest.php
│       └── BookingRequest.php
├── Services/
│   ├── AuthService.php
│   ├── BookingService.php
│   └── ScheduleService.php
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── OrderRepositoryInterface.php
│   │   ├── TicketRepositoryInterface.php
│   │   ├── PaymentRepositoryInterface.php
│   │   └── ScheduleRepositoryInterface.php
│   ├── UserRepository.php
│   ├── OrderRepository.php
│   ├── TicketRepository.php
│   ├── PaymentRepository.php
│   └── ScheduleRepository.php
├── Models/
│   ├── User.php
│   ├── Bus.php
│   ├── Route.php
│   ├── Schedule.php
│   ├── Order.php
│   ├── Ticket.php
│   ├── Payment.php
│   └── ApiKey.php
├── Providers/
│   └── RepositoryServiceProvider.php
database/
├── migrations/
└── seeders/
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/me` - Get current user

### Schedules (Public)
- `GET /api/schedules` - Search schedules
- `GET /api/schedules/{id}` - Get schedule detail

### Bookings (Protected)
- `GET /api/bookings` - Get user bookings
- `POST /api/bookings` - Create new booking
- `GET /api/bookings/{id}` - Get booking detail
- `POST /api/bookings/{id}/cancel` - Cancel booking

## Validation Rules

All inputs are validated using Laravel Form Requests:

- **Register**: name, email (unique), password (min 8, confirmed), phone
- **Login**: email, password
- **Booking**: schedule_id, tickets array, seat_number, passenger_name, payment_method

## Security Features

1. **API Key Middleware** - Validates API key for all requests
2. **Bearer Token Authentication** - Laravel Sanctum for user authentication
3. **Token Expiration** - Automatic token expiration after 30 days
4. **Password Hashing** - Bcrypt hashing for passwords
5. **Input Validation** - Comprehensive validation for all inputs
6. **CSRF Protection** - Built-in Laravel CSRF protection
7. **SQL Injection Prevention** - Eloquent ORM with parameter binding

## Architecture

This project follows the **Repository Pattern** with a clean architecture:

- **Controllers**: Handle HTTP requests/responses
- **Services**: Business logic and transaction management
- **Repositories**: Data access layer with interfaces
- **Models**: Eloquent ORM models

See [REPOSITORY_PATTERN.md](REPOSITORY_PATTERN.md) for detailed architecture documentation.

## Standard Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Development

### Run migrations
```bash
php artisan migrate
```

### Rollback migrations
```bash
php artisan migrate:rollback
```

### Seed database
```bash
php artisan db:seed
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues and questions, please open an issue in the repository.
