# API Documentation

Base URL: `http://localhost:5000/api`

All API responses follow this format:
```json
{
  "success": true|false,
  "data": {...},      // On success
  "message": "..."    // On error
}
```

## Authentication

All protected routes require JWT token in the Authorization header:
```
Authorization: Bearer <your-jwt-token>
```

---

## Auth Endpoints

### Sign Up
Create a new user account.

**Endpoint:** `POST /auth/signup`

**Access:** Public

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "64abc123...",
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

---

### Login
Authenticate user and get JWT token.

**Endpoint:** `POST /auth/login`

**Access:** Public

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "64abc123...",
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

---

### Get Current User
Get logged-in user's information.

**Endpoint:** `GET /auth/me`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64abc123...",
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "createdAt": "2024-01-15T10:30:00.000Z"
  }
}
```

---

## Product Endpoints

### Get All Products
Retrieve all products.

**Endpoint:** `GET /products`

**Access:** Public

**Success Response (200):**
```json
{
  "success": true,
  "count": 8,
  "data": [
    {
      "_id": "64abc123...",
      "name": "Wireless Headphones",
      "description": "High-quality wireless headphones...",
      "price": 99.99,
      "stock": 50,
      "category": "Electronics",
      "image": "/assets/images/headphones.jpg",
      "createdAt": "2024-01-15T10:30:00.000Z"
    }
  ]
}
```

---

### Get Single Product
Retrieve a specific product by ID.

**Endpoint:** `GET /products/:id`

**Access:** Public

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64abc123...",
    "name": "Wireless Headphones",
    "description": "High-quality wireless headphones...",
    "price": 99.99,
    "stock": 50,
    "category": "Electronics",
    "image": "/assets/images/headphones.jpg",
    "createdAt": "2024-01-15T10:30:00.000Z"
  }
}
```

---

### Create Product
Create a new product (Admin only).

**Endpoint:** `POST /products`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "New Product",
  "description": "Product description",
  "price": 49.99,
  "stock": 100,
  "category": "Electronics",
  "image": "/assets/images/product.jpg"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "data": {
    "_id": "64abc456...",
    "name": "New Product",
    "description": "Product description",
    "price": 49.99,
    "stock": 100,
    "category": "Electronics",
    "image": "/assets/images/product.jpg",
    "createdAt": "2024-01-15T12:00:00.000Z"
  }
}
```

---

### Update Product
Update an existing product (Admin only).

**Endpoint:** `PUT /products/:id`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Updated Product Name",
  "price": 59.99,
  "stock": 80
}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64abc123...",
    "name": "Updated Product Name",
    "description": "Product description",
    "price": 59.99,
    "stock": 80,
    "category": "Electronics",
    "image": "/assets/images/product.jpg"
  }
}
```

---

### Delete Product
Delete a product (Admin only).

**Endpoint:** `DELETE /products/:id`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

---

## Cart Endpoints

### Get User Cart
Retrieve current user's cart.

**Endpoint:** `GET /cart`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64cart123...",
    "user": "64user123...",
    "items": [
      {
        "product": {
          "_id": "64prod123...",
          "name": "Wireless Headphones",
          "price": 99.99,
          "image": "/assets/images/headphones.jpg"
        },
        "quantity": 2
      }
    ],
    "updatedAt": "2024-01-15T14:00:00.000Z"
  }
}
```

---

### Add to Cart
Add a product to cart.

**Endpoint:** `POST /cart`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "productId": "64prod123...",
  "quantity": 2
}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64cart123...",
    "user": "64user123...",
    "items": [
      {
        "product": {...},
        "quantity": 2
      }
    ]
  }
}
```

---

### Update Cart Item
Update quantity of a cart item.

**Endpoint:** `PUT /cart/:productId`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "quantity": 3
}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64cart123...",
    "items": [...]
  }
}
```

---

### Remove from Cart
Remove a product from cart.

**Endpoint:** `DELETE /cart/:productId`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64cart123...",
    "items": []
  }
}
```

---

### Clear Cart
Remove all items from cart.

**Endpoint:** `DELETE /cart`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64cart123...",
    "items": []
  }
}
```

---

## Order Endpoints

### Create Order
Create a new order from cart items.

**Endpoint:** `POST /orders`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product": "64prod123...",
      "quantity": 2
    }
  ],
  "shippingAddress": {
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "zipCode": "10001",
    "country": "USA"
  }
}
```

**Success Response (201):**
```json
{
  "success": true,
  "data": {
    "_id": "64order123...",
    "user": "64user123...",
    "items": [...],
    "totalAmount": 199.98,
    "status": "pending",
    "shippingAddress": {...},
    "createdAt": "2024-01-15T15:00:00.000Z"
  }
}
```

---

### Get My Orders
Retrieve current user's orders.

**Endpoint:** `GET /orders/myorders`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "count": 3,
  "data": [
    {
      "_id": "64order123...",
      "items": [...],
      "totalAmount": 199.98,
      "status": "paid",
      "createdAt": "2024-01-15T15:00:00.000Z"
    }
  ]
}
```

---

### Get Single Order
Retrieve a specific order.

**Endpoint:** `GET /orders/:id`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64order123...",
    "user": {...},
    "items": [...],
    "totalAmount": 199.98,
    "status": "paid",
    "shippingAddress": {...},
    "createdAt": "2024-01-15T15:00:00.000Z"
  }
}
```

---

### Update Order to Paid
Mark an order as paid.

**Endpoint:** `PUT /orders/:id/pay`

**Access:** Private

**Headers:**
```
Authorization: Bearer <token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64order123...",
    "status": "paid",
    "paidAt": "2024-01-15T15:05:00.000Z"
  }
}
```

---

### Get All Orders (Admin)
Retrieve all orders (Admin only).

**Endpoint:** `GET /orders`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
```

**Success Response (200):**
```json
{
  "success": true,
  "count": 25,
  "data": [
    {
      "_id": "64order123...",
      "user": {
        "name": "John Doe",
        "email": "john@example.com"
      },
      "items": [...],
      "totalAmount": 199.98,
      "status": "shipped",
      "createdAt": "2024-01-15T15:00:00.000Z"
    }
  ]
}
```

---

### Update Order Status (Admin)
Update order status (Admin only).

**Endpoint:** `PUT /orders/:id/status`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "status": "shipped"
}
```

Valid statuses: `pending`, `paid`, `shipped`, `delivered`, `cancelled`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "_id": "64order123...",
    "status": "shipped",
    "shippedAt": "2024-01-15T16:00:00.000Z"
  }
}
```

---

## Admin Endpoints

### Get Dashboard Stats
Get statistics for admin dashboard.

**Endpoint:** `GET /admin/stats`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "totalUsers": 150,
    "totalProducts": 8,
    "totalOrders": 75,
    "totalRevenue": 15750.50,
    "pendingOrders": 10,
    "paidOrders": 45,
    "shippedOrders": 20
  }
}
```

---

### Get All Users (Admin)
Retrieve all users.

**Endpoint:** `GET /admin/users`

**Access:** Private (Admin only)

**Headers:**
```
Authorization: Bearer <admin-token>
```

**Success Response (200):**
```json
{
  "success": true,
  "count": 150,
  "data": [
    {
      "_id": "64user123...",
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "createdAt": "2024-01-10T10:00:00.000Z"
    }
  ]
}
```

---

## Error Responses

All errors follow this format:

**400 Bad Request:**
```json
{
  "success": false,
  "message": "Please provide all required fields"
}
```

**401 Unauthorized:**
```json
{
  "success": false,
  "message": "Not authorized to access this route"
}
```

**403 Forbidden:**
```json
{
  "success": false,
  "message": "User role 'user' is not authorized to access this route"
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Product not found"
}
```

**500 Server Error:**
```json
{
  "success": false,
  "message": "Server Error"
}
```

---

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 500 | Internal Server Error - Server error |

---

## Rate Limiting

Currently, there are no rate limits implemented. For production, consider adding rate limiting middleware.

## CORS

CORS is enabled for all origins in development. For production, configure specific origins in `server/server.js`.
