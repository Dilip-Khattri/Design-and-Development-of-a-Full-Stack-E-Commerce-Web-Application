# Quick Installation Guide

## Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

## Installation Steps

1. **Import Database**
   ```bash
   mysql -u root -p
   CREATE DATABASE ecommerce_db;
   USE ecommerce_db;
   SOURCE database.sql;
   ```

2. **Configure Database**
   Edit `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ecommerce_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Set Permissions**
   ```bash
   chmod -R 755 uploads/
   ```

4. **Access Application**
   - User site: http://localhost/ecommerce/
   - Admin panel: http://localhost/ecommerce/admin/

## Default Login

**Admin:**
- Email: admin@example.com
- Password: admin123

**User:**
- Email: user@example.com
- Password: user123

## Notes
- The password for both demo accounts is hashed as "password"
- Change the SITE_URL in includes/config.php for your environment
- Product images will be uploaded to uploads/products/
- For production, update all passwords and security settings
