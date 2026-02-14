# PO CAN Travel - API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication & Security Layers

### 1. API Key Protection (Required for all endpoints)
Add header to all requests:
```
X-API-Key: {your_api_key}
```

### 2. Bearer Token Authentication (Required for protected endpoints)
Add header after login:
```
Authorization: Bearer {your_token}
```

### 3. Token Expiration
- Tokens expire after 30 days
- Expiration time is returned in login/register response

### 4. Rate Limiting (Throttle)
- **Auth Routes**: 10 requests per minute (e.g., login, register)
- **General API**: 60 requests per minute
- Returns `429 Too Many Requests` when limit is reached

---

## API Endpoints

### Authentication

#### 1. Register
```http
POST /api/register
Headers: X-API-Key: {api_key}
Content-Type: application/json

Body:
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}

Response (201):
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {...},
    "token": "1|xxxxx",
    "token_type": "Bearer",
    "expires_at": "2024-03-01 10:00:00"
  }
}
```

#### 2. Login
```http
POST /api/login
Headers: X-API-Key: {api_key}
Content-Type: application/json

Body:
{
  "email": "john@example.com",
  "password": "password123"
}

Response (200):
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "2|xxxxx",
    "token_type": "Bearer",
    "expires_at": "2024-03-01 10:00:00"
  }
}
```

#### 3. Logout
```http
POST /api/logout
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Logout successful"
}
```

#### 4. Get Current User
```http
GET /api/me
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "role": "customer"
  }
}
```

---

### Schedules (Public)

#### 1. Search Schedules
```http
GET /api/schedules?origin=Jakarta&destination=Bandung&date=2024-02-15
Headers: X-API-Key: {api_key}

Query Parameters:
- origin (optional): Filter by origin city
- destination (optional): Filter by destination city
- date (optional): Filter by departure date (YYYY-MM-DD)

Response (200):
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "departure_time": "2024-02-15 08:00:00",
        "arrival_time": "2024-02-15 11:00:00",
        "price": "75000.00",
        "available_seats": 40,
        "bus": {
          "id": 1,
          "bus_code": "BUS001",
          "bus_name": "Express Jaya",
          "seat_capacity": 40,
          "class": "economy"
        },
        "route": {
          "id": 1,
          "origin": "Jakarta",
          "destination": "Bandung",
          "distance_km": 150
        }
      }
    ],
    "per_page": 10,
    "total": 1
  }
}
```

#### 2. Get Schedule Detail
```http
GET /api/schedules/{id}
Headers: X-API-Key: {api_key}

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "departure_time": "2024-02-15 08:00:00",
    "arrival_time": "2024-02-15 11:00:00",
    "price": "75000.00",
    "available_seats": 40,
    "bus": {...},
    "route": {...}
  }
}
```

---

### Bookings (Protected)

#### 1. Create Booking
```http
POST /api/bookings
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "schedule_id": 1,
  "tickets": [
    {
      "seat_number": "A1",
      "passenger_name": "John Doe"
    },
    {
      "seat_number": "A2",
      "passenger_name": "Jane Doe"
    }
  ],
  "payment_method": "transfer"
}

Response (201):
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "order_code": "ORD-ABCD123456",
    "total_price": "150000.00",
    "status": "pending",
    "tickets": [...],
    "payment": {...}
  }
}
```

#### 2. Get My Bookings
```http
GET /api/bookings
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "order_code": "ORD-ABCD123456",
        "total_price": "150000.00",
        "status": "pending",
        "created_at": "2024-02-14 10:00:00",
        "tickets": [...],
        "payment": {...}
      }
    ]
  }
}
```

#### 3. Get Booking Detail
```http
GET /api/bookings/{id}
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "order_code": "ORD-ABCD123456",
    "total_price": "150000.00",
    "status": "pending",
    "tickets": [...],
    "payment": {...}
  }
}
```

#### 4. Cancel Booking
```http
POST /api/bookings/{id}/cancel
Headers: 
  X-API-Key: {api_key}
  Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Order cancelled successfully"
}
```

---

### Admin (Role: admin)

All routes in this section require `X-API-Key`, `Bearer Token`, and the user must have the `admin` role.

#### 1. Get All Orders
```http
GET /api/admin/orders
```
Returns all bookings from all users.

#### 2. Confirm Order
```http
POST /api/admin/orders/{id}/confirm
```
Manually confirm a pending booking.

#### 3. Manage Schedules (CRUD)
- **List All**: `GET /api/admin/schedules`
- **Create**: `POST /api/admin/schedules`
- **Update**: `PUT /api/admin/schedules/{id}`
- **Delete**: `DELETE /api/admin/schedules/{id}`

**Create/Update Body:**
```json
{
  "bus_id": 1,
  "route_id": 1,
  "departure_time": "2024-12-01 08:00:00",
  "arrival_time": "2024-12-01 12:00:00",
  "price": 85000,
  "available_seats": 40
}
```

#### 4. List Resources
- **Buses**: `GET /api/admin/buses`
- **Routes**: `GET /api/admin/routes`

---

## Standard JSON Response Format

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

---

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (Invalid API Key or Token)
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

---

## Validation Rules

### Register
- name: required, string, max 255
- email: required, email, unique
- password: required, min 8, confirmed
- phone: required, string, max 15

### Login
- email: required, email
- password: required

### Booking
- schedule_id: required, exists in schedules
- tickets: required, array, min 1
- tickets.*.seat_number: required, string
- tickets.*.passenger_name: required, string, max 255
- payment_method: required, in:transfer,ewallet,cash

### Admin Schedule
- bus_id: required (on create), exists in buses
- route_id: required (on create), exists in routes
- departure_time: required (on create), date, after now
- arrival_time: required (on create), date, after departure_time
- price: required (on create), numeric, min 0
- available_seats: required (on create), integer, min 0

---

## Testing Credentials

### Admin
- Email: admin@pocantravel.com
- Password: password

### Customer
- Email: customer@example.com
- Password: password

### API Key
Check database `api_keys` table after seeding.
