# Full-Stack E-Commerce Web Application (MVP)

A fully functional e-commerce web application built with Node.js, Express, MongoDB, and Vanilla JavaScript. This MVP includes user authentication, product management, shopping cart, order processing, and an admin panel.

## 🚀 Features

### User Features
- **Authentication**: Secure signup/login with JWT and bcrypt password hashing
- **Product Browsing**: View all products with details, images, and pricing
- **Shopping Cart**: Add, update, and remove items with real-time cart updates
- **Checkout**: Secure checkout process with shipping information
- **Order Management**: View order history and track order status
- **Responsive Design**: Mobile-friendly UI for all devices

### Admin Features
- **Dashboard**: Overview of users, products, orders, and revenue
- **Product Management**: Create, edit, and delete products
- **Order Management**: View and update order statuses
- **User Management**: View all registered users

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: Node.js, Express.js
- **Database**: MongoDB with Mongoose ODM
- **Authentication**: JWT (JSON Web Tokens)
- **Security**: bcryptjs for password hashing
- **File Storage**: Local file system

## 📁 Project Structure

```
├── client/                    # Frontend files
│   ├── pages/                # HTML pages
│   │   ├── index.html       # Home page
│   │   ├── login.html       # Login page
│   │   ├── signup.html      # Signup page
│   │   ├── products.html    # Products listing
│   │   ├── cart.html        # Shopping cart
│   │   ├── checkout.html    # Checkout page
│   │   ├── orders.html      # User orders
│   │   └── admin.html       # Admin panel
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css   # Main stylesheet
│   │   ├── js/
│   │   │   └── utils.js     # Frontend utilities
│   │   └── images/          # Static images
│   └── components/          # Reusable components
│
├── server/                   # Backend files
│   ├── config/
│   │   └── db.js            # Database configuration
│   ├── models/              # Mongoose models
│   │   ├── User.js          # User model
│   │   ├── Product.js       # Product model
│   │   ├── Cart.js          # Cart model
│   │   └── Order.js         # Order model
│   ├── controllers/         # Route controllers
│   │   ├── authController.js
│   │   ├── productController.js
│   │   ├── cartController.js
│   │   ├── orderController.js
│   │   └── adminController.js
│   ├── routes/              # API routes
│   │   ├── auth.js
│   │   ├── products.js
│   │   ├── cart.js
│   │   ├── orders.js
│   │   └── admin.js
│   ├── middlewares/         # Custom middlewares
│   │   ├── auth.js          # JWT authentication
│   │   └── error.js         # Error handling
│   ├── server.js            # Express server
│   └── seed.js              # Database seeder
│
├── uploads/                 # Uploaded product images
├── .env.example            # Environment variables template
├── .gitignore              # Git ignore rules
├── package.json            # Node dependencies
└── README.md               # This file
```

## 🔧 Installation & Setup

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (v4.4 or higher)
- npm or yarn

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd Design-and-Development-of-a-Full-Stack-E-Commerce-Web-Application
```

### Step 2: Install Dependencies
```bash
npm install
```

### Step 3: Configure Environment Variables
Create a `.env` file in the root directory:
```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:
```env
PORT=5000
MONGODB_URI=mongodb://localhost:27017/ecommerce_mvp
JWT_SECRET=your_super_secret_jwt_key_change_this_in_production
JWT_EXPIRE=7d
ADMIN_EMAIL=admin@ecommerce.com
ADMIN_PASSWORD=Admin@123
```

### Step 4: Start MongoDB
Make sure MongoDB is running on your system:
```bash
# For macOS (using Homebrew)
brew services start mongodb-community

# For Ubuntu/Debian
sudo systemctl start mongod

# For Windows
net start MongoDB
```

### Step 5: Seed the Database
Populate the database with sample data:
```bash
npm run seed
```

This will create:
- Admin user (admin@ecommerce.com / Admin@123)
- Sample user (john@example.com / password123)
- 8 sample products

### Step 6: Start the Server
```bash
# Development mode (with auto-reload)
npm run dev

# Production mode
npm start
```

The server will start on http://localhost:5000

## 🎯 Usage

### Access the Application
1. Open your browser and navigate to `http://localhost:5000`
2. Browse products, or login to access full features

### Login Credentials

#### Admin Account
- **Email**: admin@ecommerce.com
- **Password**: Admin@123
- **Access**: Full admin panel access

#### Regular User Account
- **Email**: john@example.com
- **Password**: password123
- **Access**: Shopping and order management

## 📚 API Documentation

### Authentication Endpoints

#### Sign Up
```
POST /api/auth/signup
Body: { name, email, password }
```

#### Login
```
POST /api/auth/login
Body: { email, password }
```

#### Get Current User
```
GET /api/auth/me
Headers: Authorization: Bearer <token>
```

### Product Endpoints

#### Get All Products
```
GET /api/products
```

#### Get Single Product
```
GET /api/products/:id
```

#### Create Product (Admin Only)
```
POST /api/products
Headers: Authorization: Bearer <admin-token>
Body: { name, description, price, stock, category, image }
```

#### Update Product (Admin Only)
```
PUT /api/products/:id
Headers: Authorization: Bearer <admin-token>
Body: { name, description, price, stock, category, image }
```

#### Delete Product (Admin Only)
```
DELETE /api/products/:id
Headers: Authorization: Bearer <admin-token>
```

### Cart Endpoints

#### Get User Cart
```
GET /api/cart
Headers: Authorization: Bearer <token>
```

#### Add to Cart
```
POST /api/cart
Headers: Authorization: Bearer <token>
Body: { productId, quantity }
```

#### Update Cart Item
```
PUT /api/cart/:productId
Headers: Authorization: Bearer <token>
Body: { quantity }
```

#### Remove from Cart
```
DELETE /api/cart/:productId
Headers: Authorization: Bearer <token>
```

### Order Endpoints

#### Create Order
```
POST /api/orders
Headers: Authorization: Bearer <token>
Body: { items, shippingAddress }
```

#### Get My Orders
```
GET /api/orders/myorders
Headers: Authorization: Bearer <token>
```

#### Get Single Order
```
GET /api/orders/:id
Headers: Authorization: Bearer <token>
```

#### Update Order to Paid
```
PUT /api/orders/:id/pay
Headers: Authorization: Bearer <token>
```

#### Get All Orders (Admin Only)
```
GET /api/orders
Headers: Authorization: Bearer <admin-token>
```

#### Update Order Status (Admin Only)
```
PUT /api/orders/:id/status
Headers: Authorization: Bearer <admin-token>
Body: { status }
```

### Admin Endpoints

#### Get Dashboard Stats
```
GET /api/admin/stats
Headers: Authorization: Bearer <admin-token>
```

#### Get All Users
```
GET /api/admin/users
Headers: Authorization: Bearer <admin-token>
```

## 🔒 Security Features

- **Password Hashing**: All passwords are hashed using bcryptjs
- **JWT Authentication**: Secure token-based authentication
- **Protected Routes**: Middleware to protect authenticated routes
- **Role-Based Access**: Admin-only routes for sensitive operations
- **Input Validation**: Server-side validation for all inputs
- **CORS Protection**: Cross-Origin Resource Sharing configuration
- **Error Handling**: Centralized error handling middleware

## 🎨 Frontend Features

- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile
- **Real-time Updates**: Cart count updates without page refresh
- **Form Validation**: Client-side validation for better UX
- **Loading States**: Visual feedback during API calls
- **Alert System**: User-friendly success/error messages
- **Modal Dialogs**: Product details and forms in modals

## 🧪 Testing

### Manual Testing Checklist

**Authentication**
- [ ] User can sign up with valid credentials
- [ ] User can login with correct credentials
- [ ] Invalid credentials are rejected
- [ ] JWT token is stored and used correctly

**Products**
- [ ] All products are displayed correctly
- [ ] Product details modal shows complete information
- [ ] Admin can create new products
- [ ] Admin can edit existing products
- [ ] Admin can delete products

**Cart**
- [ ] Items can be added to cart
- [ ] Cart quantity can be updated
- [ ] Items can be removed from cart
- [ ] Cart persists across sessions

**Orders**
- [ ] Checkout process works correctly
- [ ] Orders are created successfully
- [ ] Order history is displayed
- [ ] Admin can update order status

## 📝 Code Quality

- **Clean Code**: Well-commented and organized code
- **Consistent Style**: Follows JavaScript best practices
- **Error Handling**: Comprehensive error handling throughout
- **Modular Design**: Separation of concerns with MVC pattern
- **RESTful API**: Following REST conventions

## 🚀 Deployment

### Environment Variables for Production
Make sure to set secure values for:
- `JWT_SECRET`: Use a strong, random secret
- `MONGODB_URI`: Your production MongoDB connection string
- `NODE_ENV`: Set to 'production'

### Production Checklist
- [ ] Change default admin credentials
- [ ] Set strong JWT secret
- [ ] Enable HTTPS
- [ ] Configure MongoDB with authentication
- [ ] Set up proper logging
- [ ] Configure CORS for your domain
- [ ] Implement rate limiting
- [ ] Set up backup strategy

## 🤝 Contributing

This is an MVP project. For enhancements:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🙏 Acknowledgments

- Built following modern web development best practices
- Designed for educational and small business purposes
- MVP focused - production-ready foundation

## 📞 Support

For issues or questions:
1. Check the documentation above
2. Review the code comments
3. Open an issue on GitHub

---

**Note**: This is an MVP (Minimum Viable Product) designed for small-scale applications. For enterprise-scale applications, consider implementing:
- Real payment gateway integration
- Advanced search and filtering
- Product reviews and ratings
- Email notifications
- Advanced analytics
- Microservices architecture
- CDN for static assets
- Caching layer (Redis)
- Load balancing
