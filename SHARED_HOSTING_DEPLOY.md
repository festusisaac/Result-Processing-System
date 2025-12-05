# 🌐 Deploying RMS to Shared Hosting

This guide will help you deploy your Result Management System to shared hosting (like Namecheap, Hostinger, Bluehost, etc.).

## Prerequisites

- Shared hosting account with cPanel
- PHP 8.1+ support
- SQLite or MySQL database access
- SSH access (optional but recommended)

---

## Step 1: Prepare Your Files

### 1.1 Build Assets Locally

On your local machine, run:

```bash
cd c:\Users\Hp\Desktop\RMS
npm install
npm run build
composer install --optimize-autoloader --no-dev
```

This creates optimized production files.

### 1.2 Create a Deployment Package

Create a ZIP file of your project **excluding** these folders/files:
- `node_modules/`
- `.git/`
- `.env` (you'll create this on the server)
- `storage/logs/*` (keep the folder, delete contents)
- `database/database.sqlite` (if using SQLite)

**Using PowerShell:**
```powershell
# Create a clean copy
$source = "c:\Users\Hp\Desktop\RMS"
$dest = "c:\Users\Hp\Desktop\RMS-deploy"
robocopy $source $dest /E /XD node_modules .git /XF .env database.sqlite

# Compress it
Compress-Archive -Path $dest -DestinationPath "c:\Users\Hp\Desktop\RMS-deploy.zip"
```

---

## Step 2: Upload to Shared Hosting

### 2.1 Access cPanel

1. Log in to your hosting cPanel
2. Go to **File Manager**

### 2.2 Upload Files

**Option A: Using File Manager (Recommended for beginners)**

1. Navigate to your domain's root (usually `public_html/`)
2. Click **Upload**
3. Upload `RMS-deploy.zip`
4. Right-click the ZIP file → **Extract**
5. Move all files from `RMS-deploy/` to `public_html/` (or your domain folder)

**Option B: Using FTP (Faster for large files)**

1. Use FileZilla or WinSCP
2. Connect to your hosting FTP
3. Upload all files to `public_html/`

---

## Step 3: Configure the Public Directory

**CRITICAL:** Laravel's entry point is `public/index.php`, not the root.

### Method 1: Using .htaccess (Easiest)

Create a `.htaccess` file in your domain root (`public_html/`) with:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

This redirects all requests to the `public/` folder.

### Method 2: Change Document Root (Best Practice)

In cPanel:
1. Go to **Domains** → **Domains**
2. Click your domain
3. Change **Document Root** from `/public_html` to `/public_html/public`
4. Save

---

## Step 4: Create .env File

### 4.1 Copy .env.example

In cPanel File Manager:
1. Navigate to your project root
2. Find `.env.example`
3. Right-click → **Copy**
4. Name it `.env`

### 4.2 Edit .env

Right-click `.env` → **Edit** and configure:

```env
APP_NAME=RMS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# For SQLite (Recommended for small schools)
DB_CONNECTION=sqlite
DB_DATABASE=/home/username/public_html/database/database.sqlite

# OR for MySQL (if your host doesn't support SQLite)
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=your_database_name
# DB_USERNAME=your_db_user
# DB_PASSWORD=your_db_password

SCHOOL_NAME="Your School Name"
```

**Important:** Replace `/home/username/` with your actual cPanel username path.

---

## Step 5: Set Permissions

In cPanel File Manager, set these permissions:

```
storage/                → 775 (recursive)
storage/framework/      → 775 (recursive)
storage/logs/           → 775 (recursive)
bootstrap/cache/        → 775 (recursive)
database/               → 775 (if using SQLite)
database/database.sqlite → 664 (if using SQLite)
```

**How to set permissions:**
1. Right-click folder → **Change Permissions**
2. Check: Owner (Read, Write, Execute), Group (Read, Execute), World (Read, Execute)
3. Check **Recurse into subdirectories**
4. Click **Change Permissions**

---

## Step 6: Run Setup Commands

### Option A: Using SSH (Recommended)

If your host provides SSH access:

```bash
# Connect via SSH
ssh username@yourdomain.com

# Navigate to your project
cd public_html

# Generate APP_KEY
php artisan key:generate

# Create database (if using SQLite)
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Option B: Using cPanel Terminal (if available)

Some hosts provide a terminal in cPanel. Use the same commands as above.

### Option C: Using PHP Script (if no SSH)

Create a file `setup.php` in your `public/` folder:

```php
<?php
// Delete this file after running!
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Generating APP_KEY...\n";
$kernel->call('key:generate', ['--force' => true]);

echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);

echo "Seeding database...\n";
$kernel->call('db:seed', ['--force' => true]);

echo "Caching config...\n";
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');

echo "Setup complete! DELETE THIS FILE NOW!";
```

Visit `https://yourdomain.com/setup.php` in your browser, then **DELETE** the file immediately.

---

## Step 7: Verify Installation

1. Visit `https://yourdomain.com`
2. You should see the homepage
3. Try logging in:
   - Email: `admin@rms.com`
   - Password: `password`

---

## Troubleshooting

### 500 Internal Server Error

**Check PHP version:**
- cPanel → **Select PHP Version** → Choose PHP 8.1 or 8.2

**Check .htaccess:**
- Ensure `public/.htaccess` exists
- Verify `mod_rewrite` is enabled (ask your host)

**Check error logs:**
- cPanel → **Errors** → View error log

### Blank Page

**Clear cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Connection Error

**For SQLite:**
- Verify path in `.env` is absolute: `/home/username/public_html/database/database.sqlite`
- Check file permissions: `chmod 664 database/database.sqlite`
- Check folder permissions: `chmod 775 database/`

**For MySQL:**
- Create database in cPanel → **MySQL Databases**
- Create user and assign to database
- Update `.env` with correct credentials

### Missing Extensions

If you get "extension not found" errors:

1. Go to cPanel → **Select PHP Version**
2. Enable these extensions:
   - `gd`
   - `mbstring`
   - `xml`
   - `zip`
   - `pdo_sqlite` (for SQLite) or `pdo_mysql` (for MySQL)
   - `bcmath`
   - `fileinfo`

---

## Post-Deployment

### Update Settings

1. Login to admin panel
2. Go to **Settings**
3. Update:
   - School name
   - Contact information
   - Admin email and password

### Security Checklist

- [ ] Change default admin password
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Ensure `.env` is not publicly accessible
- [ ] Enable HTTPS (most hosts provide free SSL)
- [ ] Set up regular backups

---

## Updating Your Application

When you make changes locally:

1. Build assets: `npm run build`
2. Upload changed files via FTP/File Manager
3. SSH into server and run:
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## Performance Tips

1. **Enable OPcache** (cPanel → PHP Settings)
2. **Use MySQL** instead of SQLite for 500+ students
3. **Enable Gzip compression** (already in `.htaccess`)
4. **Use CDN** for static assets (optional)

---

## Need Help?

- Check your hosting's knowledge base
- Contact your hosting support
- Check Laravel logs: `storage/logs/laravel.log`

---

**Congratulations! Your RMS is now live on shared hosting!** 🎉
