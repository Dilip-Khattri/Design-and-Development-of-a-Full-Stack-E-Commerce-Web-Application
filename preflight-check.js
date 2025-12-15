#!/usr/bin/env node

/**
 * Pre-flight check script to verify setup before running the application
 */

const fs = require('fs');
const path = require('path');

console.log('🚀 E-Commerce MVP - Pre-flight Checks\n');

let hasErrors = false;

// Check 1: Node.js version
console.log('1. Checking Node.js version...');
const nodeVersion = process.version;
const majorVersion = parseInt(nodeVersion.split('.')[0].substring(1));
if (majorVersion >= 14) {
  console.log(`   ✅ Node.js ${nodeVersion} (>= v14 required)`);
} else {
  console.log(`   ❌ Node.js ${nodeVersion} (>= v14 required)`);
  hasErrors = true;
}

// Check 2: Required files
console.log('\n2. Checking required files...');
const requiredFiles = [
  'package.json',
  'server/server.js',
  'server/config/db.js',
  'server/seed.js',
  '.env.example'
];

requiredFiles.forEach(file => {
  const filePath = path.join(__dirname, file);
  if (fs.existsSync(filePath)) {
    console.log(`   ✅ ${file}`);
  } else {
    console.log(`   ❌ ${file} - MISSING`);
    hasErrors = true;
  }
});

// Check 3: .env file
console.log('\n3. Checking environment configuration...');
const envPath = path.join(__dirname, '.env');
if (fs.existsSync(envPath)) {
  console.log('   ✅ .env file exists');
  
  // Read .env and check required variables
  const envContent = fs.readFileSync(envPath, 'utf8');
  const requiredEnvVars = ['PORT', 'MONGODB_URI', 'JWT_SECRET', 'JWT_EXPIRE'];
  
  requiredEnvVars.forEach(varName => {
    if (envContent.includes(varName)) {
      console.log(`   ✅ ${varName} is set`);
    } else {
      console.log(`   ❌ ${varName} - MISSING`);
      hasErrors = true;
    }
  });
} else {
  console.log('   ⚠️  .env file not found - Copy .env.example to .env and configure it');
  hasErrors = true;
}

// Check 4: Node modules
console.log('\n4. Checking dependencies...');
const nodeModulesPath = path.join(__dirname, 'node_modules');
if (fs.existsSync(nodeModulesPath)) {
  console.log('   ✅ node_modules directory exists');
  
  // Check critical packages
  const criticalPackages = ['express', 'mongoose', 'jsonwebtoken', 'bcryptjs', 'dotenv'];
  criticalPackages.forEach(pkg => {
    const pkgPath = path.join(nodeModulesPath, pkg);
    if (fs.existsSync(pkgPath)) {
      console.log(`   ✅ ${pkg}`);
    } else {
      console.log(`   ❌ ${pkg} - MISSING (run npm install)`);
      hasErrors = true;
    }
  });
} else {
  console.log('   ❌ node_modules not found - Run: npm install');
  hasErrors = true;
}

// Check 5: Directory structure
console.log('\n5. Checking directory structure...');
const requiredDirs = [
  'server/models',
  'server/controllers',
  'server/routes',
  'server/middlewares',
  'client/pages',
  'client/assets/css',
  'client/assets/js'
];

requiredDirs.forEach(dir => {
  const dirPath = path.join(__dirname, dir);
  if (fs.existsSync(dirPath)) {
    console.log(`   ✅ ${dir}`);
  } else {
    console.log(`   ❌ ${dir} - MISSING`);
    hasErrors = true;
  }
});

// Check 6: Uploads directory
console.log('\n6. Checking uploads directory...');
const uploadsPath = path.join(__dirname, 'uploads');
if (!fs.existsSync(uploadsPath)) {
  console.log('   ⚠️  Creating uploads directory...');
  fs.mkdirSync(uploadsPath, { recursive: true });
  console.log('   ✅ uploads directory created');
} else {
  console.log('   ✅ uploads directory exists');
}

// Summary
console.log('\n' + '='.repeat(60));
if (hasErrors) {
  console.log('❌ Pre-flight check FAILED');
  console.log('\nPlease fix the errors above before starting the application.');
  console.log('\nQuick fixes:');
  console.log('  - Run: npm install');
  console.log('  - Copy .env.example to .env and configure it');
  console.log('  - Ensure MongoDB is installed and running');
  process.exit(1);
} else {
  console.log('✅ Pre-flight check PASSED');
  console.log('\nYour application is ready to run!');
  console.log('\nNext steps:');
  console.log('  1. Make sure MongoDB is running');
  console.log('  2. Run: npm run seed (to populate database)');
  console.log('  3. Run: npm start (to start the server)');
  console.log('  4. Open: http://localhost:5000');
  console.log('\nAdmin credentials:');
  console.log('  Email: admin@ecommerce.com');
  console.log('  Password: Admin@123');
  process.exit(0);
}
