# PO CAN Travel - Testing Guide

## Quick Setup

### 1. Run Setup Script

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

**Windows:**
```bash
setup.bat
```

### 2. Start Server
```bash
php artisan serve
```

## Manual Testing Steps

### Step 1: Get API Key

```bash
php artisan tinker
```

Then in tinker:
```php
App\Models\ApiKey::first()->api_key
```

Copy the API key for use in all requests.

### Step 2: Test Authentication

#### Register New User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890"
  }'
```

Expected Response:
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {...},
    "token": "1|xxxxx",
    "token_type": "Bearer",
    "expires_at": "2024-03-15 10:00:00"
  }
}
```

#### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "password"
  }'
```

Save the token from the response!

### Step 3: Test Schedule Search

```bash
curl -X GET "http://localhost:8000/api/schedules?origin=Jakarta&destination=Bandung" \
  -H "X-API-Key: YOUR_API_KEY"
```

Expected Response:
```json
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
        "bus": {...},
        "route": {...}
      }
    ]
  }
}
```

### Step 4: Test Booking Creation

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

Expected Response:
```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "order_code": "ORD-ABCD123456",
    "total_price": "75000.00",
    "status": "pending",
    "tickets": [...],
    "payment": {...}
  }
}
```

### Step 5: Test Get My Bookings

```bash
curl -X GET http://localhost:8000/api/bookings \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Step 6: Test Cancel Booking

```bash
curl -X POST http://localhost:8000/api/bookings/1/cancel \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Testing with Postman

1. Import the collection: `PO_CAN_Travel.postman_collection.json`
2. Set environment variables:
   - `base_url`: http://localhost:8000/api
   - `api_key`: Your API key from database
   - `token`: Will be set automatically after login

3. Test in order:
   - Authentication → Login
   - Schedules → Search Schedules
   - Bookings → Create Booking
   - Bookings → Get My Bookings

## Validation Testing

### Test Invalid Email
```bash
curl -X POST http://localhost:8000/api/register \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "invalid-email",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890"
  }'
```

Expected: 422 Validation Error

### Test Missing API Key
```bash
curl -X GET http://localhost:8000/api/schedules
```

Expected: 401 Unauthorized

### Test Invalid Token
```bash
curl -X GET http://localhost:8000/api/bookings \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer invalid_token"
```

Expected: 401 Unauthenticated

### Test Booking Without Seats
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "schedule_id": 1,
    "tickets": [],
    "payment_method": "transfer"
  }'
```

Expected: 422 Validation Error

## Database Verification

### Check Users
```sql
SELECT * FROM users;
```

### Check Schedules
```sql
SELECT s.*, b.bus_name, r.origin, r.destination 
FROM schedules s
JOIN buses b ON s.bus_id = b.id
JOIN routes r ON s.route_id = r.id;
```

### Check Orders
```sql
SELECT o.*, u.name as customer_name 
FROM orders o
JOIN users u ON o.user_id = u.id;
```

### Check Tickets
```sql
SELECT t.*, o.order_code, s.departure_time 
FROM tickets t
JOIN orders o ON t.order_id = o.id
JOIN schedules s ON t.schedule_id = s.id;
```

## Security Testing

### 1. API Key Protection
All endpoints should reject requests without valid API key.

### 2. Bearer Token Authentication
Protected endpoints should reject requests without valid token.

### 3. Token Expiration
Tokens should expire after 30 days (43200 minutes).

### 4. Authorization
Users should only see their own bookings.

## Performance Testing

### Load Test with Apache Bench
```bash
# Test schedule search
ab -n 100 -c 10 -H "X-API-Key: YOUR_API_KEY" \
  http://localhost:8000/api/schedules

# Test with authentication
ab -n 100 -c 10 \
  -H "X-API-Key: YOUR_API_KEY" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/bookings
```

## Common Issues

### Issue: "SQLSTATE[HY000] [1049] Unknown database"
**Solution:** Create the database first:
```sql
CREATE DATABASE po_can_travel;
```

### Issue: "API Key is required"
**Solution:** Add X-API-Key header to all requests.

### Issue: "Unauthenticated"
**Solution:** 
1. Login first to get token
2. Add Authorization header with Bearer token

### Issue: "Not enough available seats"
**Solution:** Check schedule availability or use different schedule_id.

## Test Coverage Checklist

- [ ] User Registration
- [ ] User Login
- [ ] User Logout
- [ ] Get Current User
- [ ] Search Schedules (no filters)
- [ ] Search Schedules (with origin)
- [ ] Search Schedules (with destination)
- [ ] Search Schedules (with date)
- [ ] Get Schedule Detail
- [ ] Create Booking (single ticket)
- [ ] Create Booking (multiple tickets)
- [ ] Get My Bookings
- [ ] Get Booking Detail
- [ ] Cancel Booking
- [ ] API Key Validation
- [ ] Bearer Token Validation
- [ ] Input Validation Errors
- [ ] Authorization (user can only see own bookings)

## Success Criteria

✅ All endpoints return standard JSON format
✅ API Key protection works on all endpoints
✅ Bearer Token authentication works on protected endpoints
✅ Tokens expire after 30 days
✅ All validations work correctly
✅ Users can only access their own bookings
✅ Seat availability updates correctly
✅ Order cancellation restores seat availability
