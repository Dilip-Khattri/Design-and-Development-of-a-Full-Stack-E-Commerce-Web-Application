# Full-Stack E-Commerce Web Application

A fully functional MVP e-commerce website with authentication, product catalog, shopping cart, order management, and admin panel.

## Features

### User Features
- ✅ User registration and authentication (with password hashing)
- ✅ Browse products with search and category filters
- ✅ Product detail pages with images and descriptions
- ✅ Shopping cart with quantity management
- ✅ Secure checkout process
- ✅ Order history and tracking
- ✅ User profile management

### Admin Features
- ✅ Admin dashboard with statistics
- ✅ Product management (CRUD operations)
- ✅ Order management with status updates
- ✅ User management
- ✅ Low stock alerts
- ✅ Image upload for products

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP (Core PHP with PDO)
- **Database**: MySQL
- **Authentication**: PHP Sessions + Password Hashing
- **Security**: CSRF protection, SQL injection prevention, XSS protection
- **File Uploads**: Local storage

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- GD library for image processing

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/Dilip-Khattri/Design-and-Development-of-a-Full-Stack-E-Commerce-Web-Application.git
cd Design-and-Development-of-a-Full-Stack-E-Commerce-Web-Application
```

### 2. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE ecommerce_db;
```

2. Import the database schema:
```bash
mysql -u root -p ecommerce_db < database.sql
```

Or use phpMyAdmin to import the `database.sql` file.

### 3. Configure Database Connection

Edit `includes/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Set Permissions

Ensure the uploads directory is writable:

```bash
chmod -R 755 uploads/
```

### 5. Web Server Configuration

#### Apache
Place the project in your web server document root (e.g., `/var/www/html/` or `htdocs/`)

#### XAMPP/WAMP
Place the project folder in the `htdocs` directory.

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost/ecommerce/
```

Or if you're using a different setup:
```
http://your-server-address/path-to-project/
```

## Default Credentials

### Admin Account
- **Email**: admin@example.com
- **Password**: admin123

### User Account
- **Email**: user@example.com
- **Password**: user123

⚠️ **Important**: Change these credentials in production!

## Project Structure

```
/ecommerce/
├── /admin/                    # Admin panel
│   ├── dashboard.php
│   ├── products.php
│   ├── add-product.php
│   ├── edit-product.php
│   ├── orders.php
│   ├── users.php
│   └── /includes/
│       ├── admin-header.php
│       ├── admin-sidebar.php
│       └── admin-footer.php
├── /assets/
│   ├── /css/                  # Stylesheets
│   │   ├── style.css
│   │   └── admin.css
│   ├── /js/                   # JavaScript files
│   │   ├── main.js
│   │   ├── cart.js
│   │   └── admin.js
│   └── /images/
├── /includes/                 # Core PHP files
│   ├── config.php            # Database configuration
│   ├── functions.php         # Helper functions
│   ├── auth.php              # Authentication functions
│   ├── header.php
│   └── footer.php
├── /uploads/products/         # Uploaded product images
├── index.php                  # Homepage
├── products.php               # Product listing
├── product-detail.php         # Single product view
├── cart.php                   # Shopping cart
├── cart-handler.php           # AJAX cart operations
├── checkout.php               # Checkout page
├── payment.php                # Payment processing
├── process-payment.php        # Payment handler
├── orders.php                 # Order history
├── login.php
├── register.php
├── logout.php
├── profile.php
├── database.sql               # Database schema
└── README.md
```

## Security Features

- ✅ Password hashing using PHP's `password_hash()`
- ✅ CSRF token protection on all forms
- ✅ SQL injection prevention using PDO prepared statements
- ✅ XSS protection via input sanitization
- ✅ Session security with regeneration
- ✅ File upload validation
- ✅ Session timeout (1 hour)

## Database Schema

### Tables
- **users** - User accounts and authentication
- **categories** - Product categories
- **products** - Product catalog
- **cart** - Shopping cart items
- **orders** - Customer orders
- **order_items** - Order line items

## Usage Guide

### For Customers

1. **Register/Login**: Create an account or login with existing credentials
2. **Browse Products**: View all products, search, or filter by category
3. **Add to Cart**: Click "Add to Cart" on product pages
4. **Checkout**: Review cart, enter shipping details, and complete order
5. **Track Orders**: View order history and status in "My Orders"

### For Administrators

1. **Login**: Use admin credentials to access admin panel
2. **Dashboard**: View statistics and recent activity
3. **Manage Products**: Add, edit, or delete products
4. **Manage Orders**: View orders and update their status
5. **View Users**: Monitor registered users and their activity

## Features in Detail

### Payment System
- Dummy payment simulation (no real payment gateway)
- Order status tracking (pending → paid → shipped → delivered)
- Automatic stock updates on order completion

### Cart System
- Persistent cart (stored in database)
- Real-time quantity updates
- Stock validation
- Automatic total calculation

### Search & Filter
- Text search across product names and descriptions
- Category-based filtering
- Pagination support

## Troubleshooting

### Database Connection Error
- Verify database credentials in `includes/config.php`
- Ensure MySQL service is running
- Check if database exists

### File Upload Issues
- Verify `uploads/` directory permissions
- Check PHP `upload_max_filesize` and `post_max_size` settings
- Ensure GD library is installed

### Session Issues
- Check if PHP sessions are enabled
- Verify session save path permissions

## Future Enhancements

Potential features for future versions:
- Real payment gateway integration (Stripe, PayPal)
- Email notifications
- Product reviews and ratings
- Wishlist functionality
- Advanced analytics dashboard
- Multiple product images
- Inventory management
- Discount codes/coupons
- Social media integration

## License

This project is open source and available for educational purposes.

## Contact

For questions or support, please contact the repository owner.

---

**Note**: This is a demonstration/educational project. For production use, additional security hardening and features should be implemented.
