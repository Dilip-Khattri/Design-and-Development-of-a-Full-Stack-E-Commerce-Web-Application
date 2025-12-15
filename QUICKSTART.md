# Quick Start Guide - E-Commerce MVP

## ⚡ 5-Minute Setup

This guide will get you up and running in 5 minutes!

### Step 1: Prerequisites (1 minute)

Make sure you have these installed:
- **Node.js** (v14+): Download from [nodejs.org](https://nodejs.org)
- **MongoDB** (v4.4+): Download from [mongodb.com](https://www.mongodb.com/try/download/community)

Verify installation:
```bash
node --version   # Should show v14 or higher
npm --version    # Should show 6 or higher
mongod --version # Should show 4.4 or higher
```

### Step 2: Clone & Install (1 minute)

```bash
# Clone the repository
git clone <your-repo-url>
cd Design-and-Development-of-a-Full-Stack-E-Commerce-Web-Application

# Install dependencies
npm install
```

### Step 3: Configure Environment (30 seconds)

```bash
# Copy the example environment file
cp .env.example .env

# Edit .env if needed (default settings work for local development)
# On Windows: copy .env.example .env
```

The default `.env` file works out of the box with MongoDB running locally.

### Step 4: Start MongoDB (30 seconds)

**macOS (with Homebrew):**
```bash
brew services start mongodb-community
```

**Ubuntu/Debian:**
```bash
sudo systemctl start mongod
```

**Windows:**
```bash
net start MongoDB
```

**Or manually:**
```bash
mongod
```

### Step 5: Seed Database (30 seconds)

```bash
npm run seed
```

You should see:
- ✅ Admin user created: admin@ecommerce.com
- ✅ Sample user created: john@example.com
- ✅ 8 products created

### Step 6: Start Application (30 seconds)

```bash
npm start
```

You should see:
```
Server running on port 5000
MongoDB Connected: localhost
```

### Step 7: Access Application (1 minute)

Open your browser and go to: **http://localhost:5000**

---

## 🎯 What to Test

### 1. Browse Products (No login required)
- Click "Shop Now" or "Products" in navigation
- View product list
- Click any product to see details

### 2. User Experience
**Login as Regular User:**
- Email: `john@example.com`
- Password: `password123`

Then:
- Browse products
- Add items to cart
- Go to cart and update quantities
- Proceed to checkout
- Fill shipping info and place order
- View your orders

### 3. Admin Experience
**Login as Admin:**
- Email: `admin@ecommerce.com`
- Password: `Admin@123`

Then:
- View dashboard with stats
- Add new products
- Edit existing products
- Delete products
- View all orders
- Update order status
- View all users

---

## 🔍 Quick Verification

Run the preflight check anytime:
```bash
npm run check
```

This verifies:
- Node.js version
- All required files
- Environment configuration
- Dependencies installed
- Directory structure
- Database connection readiness

---

## 🆘 Common Issues

### MongoDB Connection Error

**Error:** `MongoNetworkError: connect ECONNREFUSED`

**Solution:**
```bash
# Check if MongoDB is running
ps aux | grep mongod  # Linux/Mac
tasklist | findstr mongod  # Windows

# If not running, start it
brew services start mongodb-community  # macOS
sudo systemctl start mongod  # Ubuntu
net start MongoDB  # Windows
```

### Port Already in Use

**Error:** `Error: listen EADDRINUSE: address already in use :::5000`

**Solution 1:** Kill the process using port 5000
```bash
# Linux/Mac
lsof -ti:5000 | xargs kill -9

# Windows
netstat -ano | findstr :5000
taskkill /PID <PID> /F
```

**Solution 2:** Change the port in `.env`
```env
PORT=3000
```

### Module Not Found

**Error:** `Error: Cannot find module 'express'`

**Solution:**
```bash
npm install
```

### JWT Token Expired

**Error:** Login issues or "Not authorized" errors

**Solution:** 
- Clear browser localStorage
- Login again to get a new token

---

## 📱 Access Points

Once running, access these URLs:

| Page | URL | Description |
|------|-----|-------------|
| **Home** | http://localhost:5000 | Landing page |
| **Products** | http://localhost:5000/pages/products.html | Product catalog |
| **Login** | http://localhost:5000/pages/login.html | User login |
| **Sign Up** | http://localhost:5000/pages/signup.html | New user registration |
| **Cart** | http://localhost:5000/pages/cart.html | Shopping cart (requires login) |
| **Checkout** | http://localhost:5000/pages/checkout.html | Checkout page (requires login) |
| **Orders** | http://localhost:5000/pages/orders.html | Order history (requires login) |
| **Admin** | http://localhost:5000/pages/admin.html | Admin panel (requires admin login) |

---

## 🎓 Learning Path

**For Beginners:**
1. Start by browsing products (no login)
2. Sign up as a new user
3. Add items to cart
4. Complete a purchase
5. View your order history

**For Developers:**
1. Review the code structure in README.md
2. Explore API endpoints with Postman
3. Check TESTING.md for comprehensive tests
4. Modify products and see changes
5. Explore admin panel features

**For Admins:**
1. Login with admin credentials
2. View dashboard statistics
3. Add/edit/delete products
4. Manage orders
5. View user list

---

## 🔐 Default Credentials

### Admin Account
```
Email: admin@ecommerce.com
Password: Admin@123
```

### Sample User Account
```
Email: john@example.com
Password: password123
```

⚠️ **Important:** Change these credentials in production!

---

## 📊 Sample Data

After seeding, you'll have:
- **2 Users**: 1 admin, 1 regular user
- **8 Products**: Various electronics and accessories
- **0 Orders**: Create your first order by shopping!

---

## 🚀 Next Steps

After getting familiar with the application:

1. **Read Full Documentation:** Check README.md for detailed info
2. **Run Tests:** Follow TESTING.md for comprehensive testing
3. **Customize:** Modify products, categories, or add features
4. **Deploy:** Prepare for production deployment

---

## 💡 Pro Tips

- Use `npm run dev` instead of `npm start` for auto-reload during development
- Keep MongoDB running in a separate terminal window
- Use browser DevTools to inspect API calls
- Check server terminal for backend errors
- Check browser console for frontend errors

---

## 📞 Getting Help

If you encounter issues:

1. **Check preflight:** `npm run check`
2. **Review logs:** Check server terminal output
3. **Clear data:** Drop database and re-seed if needed
4. **Read docs:** README.md and TESTING.md have detailed info

---

**You're all set! Happy coding! 🎉**
