-- E-Commerce Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    category_id INT,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100),
    shipping_zip VARCHAR(20),
    phone VARCHAR(20),
    status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'dummy',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Seed data for users
-- Admin: admin@example.com / admin123
-- User: user@example.com / user123
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@example.com', '$2y$10$oBN4480W2TJ9.oTJWKOrPO8fm5gTdH2sBsCDKrILnHCLAdrgzn6lS', 'admin'),
('John Doe', 'user@example.com', '$2y$10$m6G1VePp15ifZ./4j.6Y5ue3G9TILT5EP1z86Jm8fc1TRrPIGF86C', 'user');

-- Seed data for categories
INSERT INTO categories (name, slug) VALUES
('Electronics', 'electronics'),
('Clothing', 'clothing'),
('Books', 'books'),
('Home & Garden', 'home-garden'),
('Sports', 'sports'),
('Toys', 'toys');

-- Seed data for products
INSERT INTO products (name, slug, description, price, stock, image, category_id, featured) VALUES
('Laptop Pro 15', 'laptop-pro-15', 'High-performance laptop with 15-inch display, 16GB RAM, and 512GB SSD. Perfect for professionals and students.', 1299.99, 25, 'laptop.jpg', 1, TRUE),
('Wireless Headphones', 'wireless-headphones', 'Premium noise-cancelling wireless headphones with 30-hour battery life and superior sound quality.', 199.99, 50, 'headphones.jpg', 1, TRUE),
('Smart Watch', 'smart-watch', 'Feature-rich smartwatch with fitness tracking, heart rate monitor, and smartphone notifications.', 299.99, 40, 'smartwatch.jpg', 1, FALSE),
('Men''s T-Shirt', 'mens-t-shirt', 'Comfortable cotton t-shirt available in multiple colors and sizes. Perfect for casual wear.', 24.99, 100, 'tshirt.jpg', 2, FALSE),
('Women''s Jeans', 'womens-jeans', 'Stylish and comfortable jeans made from premium denim. Available in various sizes.', 59.99, 75, 'jeans.jpg', 2, FALSE),
('Winter Jacket', 'winter-jacket', 'Warm and waterproof winter jacket with insulated lining. Perfect for cold weather.', 129.99, 30, 'jacket.jpg', 2, TRUE),
('Programming Book', 'programming-book', 'Comprehensive guide to modern web development. Covers HTML, CSS, JavaScript, and more.', 39.99, 60, 'book.jpg', 3, FALSE),
('Fiction Novel', 'fiction-novel', 'Bestselling fiction novel that will keep you turning pages late into the night.', 19.99, 80, 'novel.jpg', 3, FALSE),
('Coffee Maker', 'coffee-maker', 'Programmable coffee maker with 12-cup capacity and auto-brew function.', 79.99, 35, 'coffeemaker.jpg', 4, FALSE),
('Indoor Plant Set', 'indoor-plant-set', 'Set of 3 easy-to-care-for indoor plants to brighten up your living space.', 34.99, 45, 'plants.jpg', 4, FALSE),
('Yoga Mat', 'yoga-mat', 'Non-slip yoga mat with extra cushioning for comfortable workouts.', 29.99, 65, 'yogamat.jpg', 5, FALSE),
('Tennis Racket', 'tennis-racket', 'Professional-grade tennis racket with carbon fiber frame.', 149.99, 20, 'racket.jpg', 5, TRUE),
('Building Blocks Set', 'building-blocks-set', 'Creative building blocks set with 500 pieces. Great for children 5 and up.', 44.99, 55, 'blocks.jpg', 6, FALSE),
('Remote Control Car', 'remote-control-car', 'High-speed remote control car with rechargeable battery. Fun for all ages.', 69.99, 40, 'rccar.jpg', 6, FALSE);
