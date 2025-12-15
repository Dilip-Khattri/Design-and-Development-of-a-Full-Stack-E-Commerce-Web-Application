# Deployment Guide

This guide covers deploying the E-Commerce MVP to production environments.

## 🚀 Deployment Options

### Option 1: Heroku (Recommended for Beginners)
### Option 2: DigitalOcean/AWS/Azure
### Option 3: Vercel + MongoDB Atlas

---

## 🔒 Pre-Deployment Checklist

Before deploying to production, ensure:

- [ ] All environment variables are properly configured
- [ ] Strong JWT secret is set
- [ ] Default admin password is changed
- [ ] MongoDB has authentication enabled
- [ ] CORS is configured for your domain
- [ ] HTTPS/SSL is enabled
- [ ] Rate limiting is implemented (optional but recommended)
- [ ] Error logging is configured
- [ ] Database backups are configured

---

## Option 1: Heroku Deployment

### Prerequisites
- Heroku account ([signup](https://signup.heroku.com/))
- Heroku CLI installed ([download](https://devcenter.heroku.com/articles/heroku-cli))
- MongoDB Atlas account ([signup](https://www.mongodb.com/cloud/atlas/register))

### Step 1: Prepare Your Application

Create a `Procfile` in the root directory:
```
web: node server/server.js
```

Update `package.json` to specify Node version:
```json
{
  "engines": {
    "node": "20.x",
    "npm": "10.x"
  }
}
```

### Step 2: Set Up MongoDB Atlas

1. Create a free cluster on MongoDB Atlas
2. Create a database user
3. Whitelist all IPs (0.0.0.0/0) for Heroku
4. Get your connection string

### Step 3: Deploy to Heroku

```bash
# Login to Heroku
heroku login

# Create Heroku app
heroku create your-ecommerce-app

# Set environment variables
heroku config:set MONGODB_URI="your-mongodb-atlas-connection-string"
heroku config:set JWT_SECRET="your-super-secret-jwt-key-change-this"
heroku config:set JWT_EXPIRE="7d"
heroku config:set ADMIN_EMAIL="admin@yourdomain.com"
heroku config:set ADMIN_PASSWORD="YourStrongPassword123!"
heroku config:set NODE_ENV="production"

# Deploy
git push heroku main

# Seed database
heroku run npm run seed

# Open application
heroku open
```

### Step 4: Configure Custom Domain (Optional)

```bash
heroku domains:add www.yourdomain.com
```

Then configure your DNS provider with the provided DNS target.

---

## Option 2: DigitalOcean/AWS/Azure

### Prerequisites
- Server with Ubuntu 20.04+ or similar
- SSH access to server
- Domain name (optional)

### Step 1: Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install MongoDB
wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
sudo apt update
sudo apt install -y mongodb-org

# Start MongoDB
sudo systemctl start mongod
sudo systemctl enable mongod

# Install PM2 for process management
sudo npm install -g pm2
```

### Step 2: Deploy Application

```bash
# Clone repository
cd /var/www
sudo git clone <your-repo-url> ecommerce
cd ecommerce

# Install dependencies
npm install --production

# Create .env file
sudo nano .env
```

Add your environment variables:
```env
PORT=5000
MONGODB_URI=mongodb://localhost:27017/ecommerce_mvp
JWT_SECRET=your-super-secret-jwt-key
JWT_EXPIRE=7d
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=YourStrongPassword123!
NODE_ENV=production
```

```bash
# Seed database
npm run seed

# Start with PM2
pm2 start server/server.js --name ecommerce
pm2 save
pm2 startup
```

### Step 3: Configure Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Create Nginx configuration
sudo nano /etc/nginx/sites-available/ecommerce
```

Add this configuration:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        proxy_pass http://localhost:5000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/ecommerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 4: Configure SSL with Let's Encrypt

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is configured automatically
```

---

## Option 3: Vercel + MongoDB Atlas

### Prerequisites
- Vercel account ([signup](https://vercel.com/signup))
- MongoDB Atlas account
- Vercel CLI (optional)

### Step 1: Prepare for Vercel

Create `vercel.json`:
```json
{
  "version": 2,
  "builds": [
    {
      "src": "server/server.js",
      "use": "@vercel/node"
    }
  ],
  "routes": [
    {
      "src": "/api/(.*)",
      "dest": "server/server.js"
    },
    {
      "src": "/(.*)",
      "dest": "client/$1"
    }
  ]
}
```

### Step 2: Deploy

1. Connect your GitHub repository to Vercel
2. Configure environment variables in Vercel dashboard
3. Deploy

Or using CLI:
```bash
npm i -g vercel
vercel login
vercel --prod
```

**Note:** Vercel is better suited for the frontend. Consider separating backend to a different service.

---

## 🔧 Production Environment Variables

```env
# Server
PORT=5000
NODE_ENV=production

# Database
MONGODB_URI=mongodb+srv://user:pass@cluster.mongodb.net/ecommerce?retryWrites=true&w=majority

# JWT
JWT_SECRET=use-a-strong-random-secret-key-here
JWT_EXPIRE=7d

# Admin
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=VeryStrongPassword123!@#

# Optional: Email service (for future features)
# EMAIL_HOST=smtp.gmail.com
# EMAIL_PORT=587
# EMAIL_USER=your-email@gmail.com
# EMAIL_PASSWORD=your-app-password
```

---

## 🔐 Security Hardening

### 1. Enable Helmet for Security Headers

```bash
npm install helmet
```

Update `server/server.js`:
```javascript
const helmet = require('helmet');
app.use(helmet());
```

### 2. Implement Rate Limiting

```bash
npm install express-rate-limit
```

Add to `server/server.js`:
```javascript
const rateLimit = require('express-rate-limit');

const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100 // limit each IP to 100 requests per windowMs
});

app.use('/api/', limiter);
```

### 3. Configure CORS Properly

Update in `server/server.js`:
```javascript
const cors = require('cors');

const corsOptions = {
  origin: ['https://yourdomain.com', 'https://www.yourdomain.com'],
  credentials: true
};

app.use(cors(corsOptions));
```

### 4. Enable MongoDB Authentication

```bash
# On MongoDB server
mongo
use admin
db.createUser({
  user: "admin",
  pwd: "strongpassword",
  roles: ["userAdminAnyDatabase", "dbAdminAnyDatabase", "readWriteAnyDatabase"]
})
```

Update connection string:
```
mongodb://admin:strongpassword@localhost:27017/ecommerce_mvp?authSource=admin
```

---

## 📊 Monitoring & Logging

### Set Up PM2 Monitoring (for VPS deployment)

```bash
pm2 install pm2-logrotate
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 7

# Monitor
pm2 monit
pm2 logs ecommerce
```

### Configure Error Logging

Install winston:
```bash
npm install winston
```

Create `server/config/logger.js`:
```javascript
const winston = require('winston');

const logger = winston.createLogger({
  level: 'info',
  format: winston.format.json(),
  transports: [
    new winston.transports.File({ filename: 'error.log', level: 'error' }),
    new winston.transports.File({ filename: 'combined.log' })
  ]
});

if (process.env.NODE_ENV !== 'production') {
  logger.add(new winston.transports.Console({
    format: winston.format.simple()
  }));
}

module.exports = logger;
```

---

## 🔄 Database Backups

### Automated MongoDB Backups

Create backup script `scripts/backup.sh`:
```bash
#!/bin/bash
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/mongodb"
DB_NAME="ecommerce_mvp"

mongodump --db $DB_NAME --out $BACKUP_DIR/$TIMESTAMP

# Keep only last 7 days of backups
find $BACKUP_DIR -mtime +7 -delete
```

Make it executable and add to cron:
```bash
chmod +x scripts/backup.sh
crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * /var/www/ecommerce/scripts/backup.sh
```

---

## 🚨 Health Checks

Add health check endpoint in `server/server.js`:
```javascript
app.get('/health', (req, res) => {
  res.status(200).json({
    status: 'OK',
    timestamp: new Date().toISOString(),
    uptime: process.uptime()
  });
});
```

---

## 📈 Performance Optimization

### 1. Enable Gzip Compression

```bash
npm install compression
```

```javascript
const compression = require('compression');
app.use(compression());
```

### 2. Implement Caching

For static assets, configure cache headers:
```javascript
app.use(express.static('client', {
  maxAge: '1d'
}));
```

### 3. Database Indexing

Add indexes to frequently queried fields:
```javascript
// In models
userSchema.index({ email: 1 });
productSchema.index({ category: 1, price: 1 });
orderSchema.index({ user: 1, createdAt: -1 });
```

---

## 🔍 Troubleshooting

### Application Won't Start
```bash
# Check logs
pm2 logs ecommerce
# or
heroku logs --tail

# Verify environment variables
pm2 env 0
# or
heroku config
```

### Database Connection Issues
```bash
# Test MongoDB connection
mongo --host localhost --port 27017

# Check MongoDB status
sudo systemctl status mongod
```

### High Memory Usage
```bash
# Restart application
pm2 restart ecommerce

# Check memory
pm2 monit
```

---

## 📝 Post-Deployment Checklist

- [ ] Application is accessible via domain
- [ ] SSL certificate is installed and auto-renews
- [ ] Environment variables are configured
- [ ] Database is seeded with admin user
- [ ] Admin can login and access admin panel
- [ ] Users can signup, login, and place orders
- [ ] Error logging is working
- [ ] Backups are configured
- [ ] Monitoring is set up
- [ ] Security headers are enabled
- [ ] Rate limiting is active
- [ ] Default credentials are changed

---

## 🆘 Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor error logs
- Check application health

**Weekly:**
- Review security logs
- Check disk space
- Monitor database size

**Monthly:**
- Update dependencies
- Review and rotate logs
- Test backup restoration
- Security audit

**Quarterly:**
- Performance review
- Database optimization
- Update SSL certificates (if not auto-renewed)

---

## 📞 Emergency Contacts

Keep a list of:
- Server provider support
- Database administrator
- DNS provider support
- SSL certificate provider

---

**Remember:** Always test deployments in a staging environment before going to production!
