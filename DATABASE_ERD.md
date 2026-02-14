# PO CAN Travel - Database ERD Design

## Entity Relationship Diagram

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email (unique)  │
│ password        │
│ phone           │
│ role (enum)     │
│ created_at      │
│ updated_at      │
└────────┬────────┘
         │
         │ 1:N
         │
┌────────▼────────┐
│     orders      │
├─────────────────┤
│ id (PK)         │
│ user_id (FK)    │
│ order_code      │
│ total_price     │
│ status (enum)   │
│ created_at      │
│ updated_at      │
└────┬───────┬────┘
     │       │
     │ 1:N   │ 1:1
     │       │
     │   ┌───▼──────────┐
     │   │   payments   │
     │   ├──────────────┤
     │   │ id (PK)      │
     │   │ order_id(FK) │
     │   │ payment_     │
     │   │   method     │
     │   │ payment_     │
     │   │   status     │
     │   │ paid_at      │
     │   │ created_at   │
     │   │ updated_at   │
     │   └──────────────┘
     │
┌────▼────────────┐
│    tickets      │
├─────────────────┤
│ id (PK)         │
│ order_id (FK)   │
│ schedule_id(FK) │
│ seat_number     │
│ passenger_name  │
│ created_at      │
│ updated_at      │
└────────┬────────┘
         │
         │ N:1
         │
┌────────▼────────┐       ┌─────────────┐
│   schedules     │       │    buses    │
├─────────────────┤       ├─────────────┤
│ id (PK)         │◄──────┤ id (PK)     │
│ bus_id (FK)     │  N:1  │ bus_code    │
│ route_id (FK)   │       │ bus_name    │
│ departure_time  │       │ seat_       │
│ arrival_time    │       │   capacity  │
│ price           │       │ class(enum) │
│ available_seats │       │ created_at  │
│ created_at      │       │ updated_at  │
│ updated_at      │       └─────────────┘
└────────┬────────┘
         │
         │ N:1
         │
┌────────▼────────┐
│     routes      │
├─────────────────┤
│ id (PK)         │
│ origin          │
│ destination     │
│ distance_km     │
│ created_at      │
│ updated_at      │
└─────────────────┘

┌─────────────────┐
│   api_keys      │
├─────────────────┤
│ id (PK)         │
│ app_name        │
│ api_key(unique) │
│ status          │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

## Table Descriptions

### 1. users
Stores user information for both customers and admins.
- **Primary Key**: id
- **Relationships**: 
  - Has many orders

### 2. buses
Stores bus information including capacity and class.
- **Primary Key**: id
- **Relationships**: 
  - Has many schedules

### 3. routes
Stores route information between cities.
- **Primary Key**: id
- **Relationships**: 
  - Has many schedules

### 4. schedules
Stores bus schedules with pricing and availability.
- **Primary Key**: id
- **Foreign Keys**: 
  - bus_id → buses.id
  - route_id → routes.id
- **Relationships**: 
  - Belongs to bus
  - Belongs to route
  - Has many tickets

### 5. orders
Stores booking orders made by users.
- **Primary Key**: id
- **Foreign Keys**: 
  - user_id → users.id
- **Relationships**: 
  - Belongs to user
  - Has many tickets
  - Has one payment

### 6. tickets
Stores individual ticket information for each passenger.
- **Primary Key**: id
- **Foreign Keys**: 
  - order_id → orders.id
  - schedule_id → schedules.id
- **Relationships**: 
  - Belongs to order
  - Belongs to schedule

### 7. payments
Stores payment information for orders.
- **Primary Key**: id
- **Foreign Keys**: 
  - order_id → orders.id
- **Relationships**: 
  - Belongs to order

### 8. api_keys
Stores API keys for mobile app authentication.
- **Primary Key**: id
- **No relationships**

## Enum Values

### users.role
- customer
- admin

### buses.class
- economy
- business
- executive

### orders.status
- pending
- paid
- cancelled

### payments.payment_method
- transfer
- ewallet
- cash

### payments.payment_status
- pending
- success
- failed

## Indexes

Recommended indexes for performance:
- users: email (unique)
- buses: bus_code (unique)
- orders: order_code (unique), user_id
- tickets: order_id, schedule_id
- schedules: bus_id, route_id, departure_time
- api_keys: api_key (unique)
