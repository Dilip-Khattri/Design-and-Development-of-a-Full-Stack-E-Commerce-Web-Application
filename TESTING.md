# Testing Guide

## Prerequisites for Testing

Before testing the application, ensure you have:
- Node.js (v14 or higher) installed
- MongoDB running locally or accessible via connection string
- All dependencies installed (`npm install`)
- `.env` file configured

## Testing Checklist

### 1. Database Connection Test

```bash
# Start MongoDB (example for different systems)
# macOS with Homebrew
brew services start mongodb-community

# Ubuntu/Debian
sudo systemctl start mongod

# Windows
net start MongoDB
```

Verify MongoDB is running:
```bash
mongosh  # or mongo
```

### 2. Seed Database

```bash
npm run seed
```

Expected output:
- Admin user created
- Sample user created
- 8 products created
- Displays admin credentials

### 3. Start Server

```bash
npm start
# or for development with auto-reload
npm run dev
```

Expected output:
- Server running on port 5000
- MongoDB Connected message

### 4. Manual API Testing

Use tools like Postman, curl, or your browser to test:

#### Authentication Tests

**Sign Up:**
```bash
curl -X POST http://localhost:5000/api/auth/signup \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"test123"}'
```

Expected: 201 status, token and user object

**Login:**
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ecommerce.com","password":"Admin@123"}'
```

Expected: 200 status, token and user object

**Get Current User:**
```bash
curl http://localhost:5000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

Expected: 200 status, user data

#### Product Tests

**Get All Products:**
```bash
curl http://localhost:5000/api/products
```

Expected: 200 status, array of products

**Get Single Product:**
```bash
curl http://localhost:5000/api/products/PRODUCT_ID
```

Expected: 200 status, product object

**Create Product (Admin Only):**
```bash
curl -X POST http://localhost:5000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{"name":"New Product","description":"Test","price":50,"stock":10,"category":"Test"}'
```

Expected: 201 status, created product

#### Cart Tests

**Get Cart:**
```bash
curl http://localhost:5000/api/cart \
  -H "Authorization: Bearer USER_TOKEN"
```

Expected: 200 status, cart object

**Add to Cart:**
```bash
curl -X POST http://localhost:5000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer USER_TOKEN" \
  -d '{"productId":"PRODUCT_ID","quantity":2}'
```

Expected: 200 status, updated cart

#### Order Tests

**Create Order:**
```bash
curl -X POST http://localhost:5000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer USER_TOKEN" \
  -d '{"items":[{"product":"PRODUCT_ID","quantity":1}],"shippingAddress":{"street":"123 Main","city":"City","state":"State","zipCode":"12345","country":"USA"}}'
```

Expected: 201 status, order object

**Get My Orders:**
```bash
curl http://localhost:5000/api/orders/myorders \
  -H "Authorization: Bearer USER_TOKEN"
```

Expected: 200 status, array of orders

### 5. Frontend Testing

Navigate to `http://localhost:5000` in your browser and test:

**Homepage:**
- [ ] Page loads without errors
- [ ] Navigation menu is visible
- [ ] Hero section displays correctly
- [ ] Feature cards are visible

**Authentication:**
- [ ] Navigate to login page
- [ ] Login with admin credentials (admin@ecommerce.com / Admin@123)
- [ ] Verify redirect to admin panel
- [ ] Logout
- [ ] Login with user credentials (john@example.com / password123)
- [ ] Verify redirect to products page
- [ ] Test signup with new user

**Products Page:**
- [ ] All products display in grid
- [ ] Click product to view details modal
- [ ] Modal shows correct product information
- [ ] Add product to cart (must be logged in)
- [ ] Verify success message

**Shopping Cart:**
- [ ] Cart shows added items
- [ ] Update quantity works
- [ ] Remove item works
- [ ] Cart summary calculates correctly
- [ ] Proceed to checkout button works

**Checkout:**
- [ ] Form displays shipping fields
- [ ] Fill in shipping information
- [ ] Submit order
- [ ] Verify success message
- [ ] Verify redirect to orders page

**My Orders:**
- [ ] Orders list displays
- [ ] Order details are correct
- [ ] Order status is shown

**Admin Panel:**
- [ ] Login as admin
- [ ] Dashboard stats load correctly
- [ ] Products tab shows all products
- [ ] Add new product works
- [ ] Edit product works
- [ ] Delete product works
- [ ] Orders tab shows all orders
- [ ] Update order status works
- [ ] Users tab shows all users

### 6. Security Testing

**Password Hashing:**
- [ ] Check database - passwords should be hashed
- [ ] Cannot login with wrong password

**JWT Authentication:**
- [ ] Cannot access protected routes without token
- [ ] Cannot access admin routes as regular user
- [ ] Token expires after configured time

**Input Validation:**
- [ ] Try creating user with invalid email
- [ ] Try creating product with negative price
- [ ] Try adding more items to cart than in stock

### 7. Responsive Design Testing

Test on different screen sizes:
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

### 8. Error Handling Testing

**Test error scenarios:**
- [ ] Invalid API endpoints return 404
- [ ] Missing required fields return 400
- [ ] Unauthorized access returns 401
- [ ] Forbidden access returns 403
- [ ] Non-existent resources return 404
- [ ] Server errors return 500 with proper message

## Performance Testing

### Load Testing (Optional)

Use tools like Apache Bench or Artillery:

```bash
# Example with Apache Bench
ab -n 100 -c 10 http://localhost:5000/api/products
```

Monitor:
- Response times
- Memory usage
- CPU usage

## Code Quality Checks

### Linting (if configured)
```bash
npm run lint
```

### Code Review Checklist
- [ ] All routes have proper error handling
- [ ] All passwords are hashed
- [ ] JWT tokens are validated
- [ ] Input validation is in place
- [ ] CORS is properly configured
- [ ] Environment variables are used for secrets
- [ ] Code is well-commented
- [ ] File structure is organized

## Common Issues and Solutions

**Issue: Cannot connect to MongoDB**
- Solution: Make sure MongoDB is running (`mongod` command)
- Check connection string in `.env`

**Issue: Token expired**
- Solution: Login again to get new token

**Issue: Port already in use**
- Solution: Change PORT in `.env` or kill process using port 5000

**Issue: Module not found**
- Solution: Run `npm install` again

**Issue: CORS errors**
- Solution: Check CORS configuration in server.js

## Success Criteria

All tests should pass with:
- ✅ No console errors
- ✅ Proper HTTP status codes
- ✅ Correct data returned
- ✅ Responsive UI
- ✅ Secure authentication
- ✅ Data persistence
- ✅ Proper error messages

## Notes

- Always test with fresh database seed for consistent results
- Use incognito/private browsing for clean session testing
- Clear localStorage if experiencing authentication issues
- Check browser console for frontend errors
- Check server terminal for backend errors
