# 🎓 RMS - Result Management System

A comprehensive web-based Result Management System designed for schools to efficiently manage student records, academic results, attendance, and generate professional report cards.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## ✨ Features

### 📊 Academic Management
- **Student Management** - Complete student records with class assignments
- **Class & Subject Management** - Organize classes, subjects, and subject groups
- **Session & Term Management** - Academic year and term tracking
- **Teacher Management** - Assign teachers to subjects

### 📝 Assessment & Grading
- **Scoresheet Entry** - Easy score entry interface for teachers
- **Broadsheet View** - Comprehensive class performance overview
- **Attendance Tracking** - Monitor student attendance
- **Skills Assessment** - Affective and psychomotor skills evaluation
- **Automated Grading** - Configurable grading system with remarks

### 📄 Report Generation
- **Professional Report Cards** - Beautiful, printable student reports
- **PDF Export** - Download reports as PDF
- **Previous Term Scores** - Track student progress across terms
- **Class Comments** - Customizable class teacher and principal comments
- **Signature Support** - Upload and display signatures on reports

### 🎫 Scratch Card System
- **Batch Generation** - Generate scratch cards in bulk
- **PIN Protection** - Secure result access with scratch cards
- **Usage Tracking** - Monitor card usage and validity
- **Batch Management** - Organize cards by batches

### 📰 Content Management
- **Blog System** - Publish school news and announcements
- **Public Pages** - Homepage, About, Contact, Gallery
- **SEO Optimized** - Meta tags and descriptions
- **Responsive Design** - Mobile-friendly interface

### ⚙️ Settings & Configuration
- **School Settings** - Configure school information
- **Report Settings** - Customize grading scales and remarks
- **Profile Management** - Update admin email and password
- **Cache Management** - Optimized performance with caching

## 🚀 Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL >= 8.0
- Node.js & NPM (for frontend assets)

### Step 1: Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/RMS.git
cd RMS
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rms
DB_USERNAME=root
DB_PASSWORD=your_password

SCHOOL_NAME="Your School Name"
```

Run migrations:
```bash
php artisan migrate
```

### Step 5: Seed Database (Optional)
```bash
# Create admin user and sample data
php artisan db:seed
```

Default admin credentials:
- Email: `admin@example.com`
- Password: `password`

### Step 6: Build Assets
```bash
npm run build
```

### Step 7: Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📁 Project Structure

```
RMS/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   └── Console/Commands/     # Artisan commands
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/           # Admin panel views
│   │   ├── public/          # Public-facing views
│   │   └── layouts/         # Layout templates
│   └── css/                 # Stylesheets
├── routes/
│   └── web.php              # Web routes
└── public/                  # Public assets
```

## 🔧 Configuration

### Report Settings
Navigate to **Admin Panel → Reports → Report Settings** to configure:
- Grading scales and grade points
- Remark templates
- Class comments
- Principal and teacher signatures

### School Settings
Navigate to **Admin Panel → Settings** to configure:
- School name and contact information
- Admin profile (email and password)

### Scratch Card Settings
Navigate to **Admin Panel → Scratch Cards** to:
- Generate new scratch card batches
- Set expiry dates
- Monitor card usage

## 📖 Usage Guide

### Adding Students
1. Go to **Students → Add New Student**
2. Fill in student details
3. Assign to a class
4. Save

### Entering Scores
1. Navigate to **Assessment → Scoresheet**
2. Select class, subject, session, and term
3. Enter CA and exam scores
4. Save scores

### Generating Reports
1. Go to **Reports → View Results**
2. Select class, session, and term
3. Click on student to preview report
4. Print or export as PDF

### Publishing Results
1. Navigate to **Reports → Result Management**
2. Select session and term
3. Click **Publish Results**
4. Results become accessible via scratch cards

## 🎨 Customization

### Changing Colors
Edit `tailwind.config.js` to customize the color scheme:
```javascript
colors: {
    primary: '#4F46E5',  // Change primary color
    sidebar: '#1E293B',  // Change sidebar color
}
```

### Logo Upload
Place your school logo at:
```
public/images/school-logo.png
```

### Email Configuration
Update `.env` with your SMTP settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔒 Security

- All passwords are hashed using bcrypt
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection with Blade templating
- Secure scratch card PIN generation

## 🚀 Performance Optimization

### Enable Caching (Production)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Indexing
The system includes optimized indexes on:
- Foreign keys
- Frequently queried columns
- Search fields

### Redis Caching (Optional)
For better performance, configure Redis in `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 📊 System Requirements

### Minimum Requirements
- **Server:** Apache/Nginx
- **PHP:** 8.1+
- **Database:** MySQL 8.0+
- **RAM:** 512MB
- **Storage:** 1GB

### Recommended for Production
- **Server:** Nginx with PHP-FPM
- **PHP:** 8.2+
- **Database:** MySQL 8.0+ with query cache
- **RAM:** 2GB+
- **Storage:** 5GB+
- **Caching:** Redis

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components from [Tailwind CSS](https://tailwindcss.com)
- Icons from [Font Awesome](https://fontawesome.com)
- PDF generation with [DomPDF](https://github.com/barryvdh/laravel-dompdf)

## 📞 Support

For support, email support@yourschool.com or create an issue in the repository.

## 🗺️ Roadmap

- [ ] Parent portal for result access
- [ ] Mobile application
- [ ] SMS notifications
- [ ] Fee management module
- [ ] Timetable management
- [ ] Library management
- [ ] Multi-language support
- [ ] Advanced analytics dashboard

---

**Made with ❤️ for educational institutions**
