# ITHM Central Management System (CMS)

A comprehensive multi-campus management platform for the Institute of Tourism and Hospitality Management.

## 🚀 Quick Start

### Prerequisites
- XAMPP with Apache and MySQL running
- PHP 8.x
- MySQL 8.x

### Installation

1. **Clone/Copy to XAMPP htdocs:**
   ```
   D:\xampp\htdocs\ithm\
   ```

2. **Database Setup (production examples):**
   - Import schema via mysql (adjust path/creds):
   ```bash
   mysql -h localhost -u ithmpwus_ztdcp -p ithmpwus_ithm_cms < /home/ithmpwus/cms.ithm.edu.pk/database/schema.sql
   ```
   - Run PHP migration scripts (CLI, not via browser):
   ```bash
   php /home/ithmpwus/cms.ithm.edu.pk/database/run_migration.php
   php /home/ithmpwus/cms.ithm.edu.pk/database/run_migration_campus.php
   php /home/ithmpwus/cms.ithm.edu.pk/database/run_migration_fee_structures.php
   php /home/ithmpwus/cms.ithm.edu.pk/database/run_migration_academics.php
   php /home/ithmpwus/cms.ithm.edu.pk/database/run_migration_admissions_trash.php
   ```

3. **Access the Application:**
   - URL: `https://cms.ithm.edu.pk`
   - Login: `https://cms.ithm.edu.pk/login`

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@ithm.edu.pk | Admin@123 |

## 🔑 User Roles & Permissions

### System Admin
- Full system control
- Manage campuses, users, and settings
- Access all data across campuses

### Main Campus Admin
- Manage courses and fee structures
- Review all admissions across campuses
- Verify payments and send reminders

### Sub Campus Admin
- Manage own campus admissions
- Verify payments for own campus
- Upload certificates for students

### Student
- Apply for admissions
- Upload documents
- Pay fees and download vouchers
- Download certificates

## 📁 Project Structure

```
cms.ithm.edu.pk/
├── app/
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Database models
│   ├── Core/            # Framework core (Router, Database, etc.)
│   ├── Helpers/         # Utility functions
│   └── Middleware/      # Auth, Role-based access
├── config/
│   ├── config.php       # App configuration
│   └── database.php     # Database settings
├── database/
│   └── schema.sql       # Database schema
├── public/              # Web root
│   ├── index.php        # Entry point
│   └── assets/          # CSS, JS, Images
├── resources/
│   └── views/           # HTML templates
├── routes/
│   └── web.php          # Route definitions
└── storage/
    ├── uploads/         # User uploads
    └── logs/            # Error logs
```

## 🌐 Key Features

### Admission Management
- Multi-step application form
- Document upload with verification
- Status tracking (Pending, Approved, Rejected, Update Required)
- Auto-generation of application numbers

### Fee Management
- Configurable fee structures per course/campus
- Auto-generated fee vouchers on admission approval
- Payment proof upload with admin verification
- Fee reminders (manual + automatic)
- Overdue tracking and defaulter reports

### Certificate Management
- Admin uploads certificates for completed students
- Students receive notifications
- Download option with printed copy reminder

### Notification System
- Automatic notifications for:
  - Admission status changes
  - Fee voucher generation
  - Payment verification results
  - Certificate availability
- Manual fee reminders by admins

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection on all forms
- Session-based authentication
- Role-based access control
- Input sanitization and validation
- SQL injection prevention (PDO prepared statements)
- File type validation for uploads

## 📱 Technology Stack

- **Backend:** PHP 8.x (Custom MVC)
- **Database:** MySQL 8.x
- **Frontend:** Tailwind CSS 3.x, Alpine.js 3.x
- **Icons:** Font Awesome 6.x

## 🛠️ Configuration

### Database Configuration (`config/database.php`)
```php
return [
    'host' => 'localhost',
    'database' => 'ithm_cms',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

### Application Configuration (`config/config.php`)
- BASE_URL: Auto-detected
- Session lifetime: 1 hour
- Max upload size: 5MB
- Allowed file types: Images, PDFs

## 📊 User Flows

### Student Admission Flow
1. Register → Login → Apply for Admission
2. Fill multi-step form (Personal, Guardian, Academic, Documents)
3. Submit application → Receive confirmation
4. Wait for admin review
5. If approved → Download fee voucher → Pay → Upload proof
6. Admin verifies → Roll number assigned → Enrollment confirmed

### Admin Workflow
1. Login → Dashboard with stats
2. Review pending admissions
3. Approve/Reject/Request updates
4. Verify payment submissions
5. Assign roll numbers
6. Upload certificates for completed students

## 🚀 Deployment Notes

### For Production
1. Set `APP_ENV` to `production` in config/config.php
2. Disable display_errors
3. Configure SMTP for email notifications
4. Set secure session cookie options
5. Enable HTTPS

### File Permissions
- `storage/uploads/`: 755 (writable)
- `storage/logs/`: 755 (writable)

## 📞 Support

For technical support or queries, contact the IT department.

---

**Version:** 1.0.0  
**Last Updated:** December 2024

