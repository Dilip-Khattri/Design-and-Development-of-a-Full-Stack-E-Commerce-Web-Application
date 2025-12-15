require('dotenv').config();
const mongoose = require('mongoose');
const User = require('./models/User');
const Product = require('./models/Product');
const connectDB = require('./config/db');

// Sample products data
const products = [
  {
    name: 'Wireless Headphones',
    description: 'High-quality wireless headphones with noise cancellation and 30-hour battery life.',
    price: 99.99,
    stock: 50,
    category: 'Electronics',
    image: '/assets/images/headphones.jpg'
  },
  {
    name: 'Smart Watch',
    description: 'Feature-rich smartwatch with fitness tracking, heart rate monitor, and notifications.',
    price: 199.99,
    stock: 30,
    category: 'Electronics',
    image: '/assets/images/smartwatch.jpg'
  },
  {
    name: 'Laptop Stand',
    description: 'Ergonomic aluminum laptop stand for better posture and cooling.',
    price: 39.99,
    stock: 100,
    category: 'Accessories',
    image: '/assets/images/laptop-stand.jpg'
  },
  {
    name: 'USB-C Hub',
    description: '7-in-1 USB-C hub with HDMI, USB 3.0, SD card reader, and power delivery.',
    price: 49.99,
    stock: 75,
    category: 'Accessories',
    image: '/assets/images/usb-hub.jpg'
  },
  {
    name: 'Mechanical Keyboard',
    description: 'RGB mechanical keyboard with customizable keys and tactile switches.',
    price: 129.99,
    stock: 40,
    category: 'Electronics',
    image: '/assets/images/keyboard.jpg'
  },
  {
    name: 'Wireless Mouse',
    description: 'Ergonomic wireless mouse with precision tracking and long battery life.',
    price: 29.99,
    stock: 80,
    category: 'Electronics',
    image: '/assets/images/mouse.jpg'
  },
  {
    name: 'Phone Case',
    description: 'Durable protective phone case with shock absorption and slim design.',
    price: 19.99,
    stock: 200,
    category: 'Accessories',
    image: '/assets/images/phone-case.jpg'
  },
  {
    name: 'Portable Charger',
    description: '20000mAh portable charger with fast charging and dual USB ports.',
    price: 34.99,
    stock: 60,
    category: 'Electronics',
    image: '/assets/images/charger.jpg'
  }
];

const seedDatabase = async () => {
  try {
    await connectDB();

    // Clear existing data
    await User.deleteMany();
    await Product.deleteMany();

    console.log('Data cleared...');

    // Create admin user
    const adminUser = await User.create({
      name: 'Admin User',
      email: process.env.ADMIN_EMAIL || 'admin@ecommerce.com',
      password: process.env.ADMIN_PASSWORD || 'Admin@123',
      role: 'admin'
    });

    console.log('Admin user created:', adminUser.email);

    // Create sample regular user
    const regularUser = await User.create({
      name: 'John Doe',
      email: 'john@example.com',
      password: 'password123',
      role: 'user'
    });

    console.log('Sample user created:', regularUser.email);

    // Create products
    await Product.insertMany(products);

    console.log(`${products.length} products created`);
    console.log('Database seeded successfully!');
    console.log('\n--- Admin Credentials ---');
    console.log('Email:', adminUser.email);
    console.log('Password:', process.env.ADMIN_PASSWORD || 'Admin@123');
    console.log('\n--- Sample User Credentials ---');
    console.log('Email:', regularUser.email);
    console.log('Password: password123');

    process.exit(0);
  } catch (error) {
    console.error('Error seeding database:', error);
    process.exit(1);
  }
};

seedDatabase();
