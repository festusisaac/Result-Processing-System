# Migration Guide: SQLite to MySQL

## Why Switch?
- Better concurrency (multiple users writing simultaneously)
- Better performance at scale (500+ students)
- Industry standard for production
- Better backup/restore options

## Steps to Migrate

### 1. Install MySQL
```bash
# Windows: Download from mysql.com
# Or use XAMPP/WAMP which includes MySQL
```

### 2. Create MySQL Database
```sql
CREATE DATABASE rms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'rms_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON rms.* TO 'rms_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Update .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rms
DB_USERNAME=rms_user
DB_PASSWORD=your_password
```

### 4. Export SQLite Data
```bash
# Install sqlite3-to-mysql
pip install sqlite3-to-mysql

# Run migration
sqlite3mysql -f database/database.sqlite -d rms -u rms_user -p
```

### 5. Or Fresh Migration
```bash
# Run migrations on MySQL
php artisan migrate:fresh --seed
```

## When to Switch?
- Before deploying to production
- When you have 200+ students
- When experiencing slow performance
- Before result publishing day (high traffic)

## Performance Comparison
| Database | Students | Concurrent Users | Performance |
|----------|----------|------------------|-------------|
| SQLite   | < 500    | < 50             | Good        |
| MySQL    | 500+     | 100+             | Excellent   |
