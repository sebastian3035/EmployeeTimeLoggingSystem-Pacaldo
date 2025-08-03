# Employee-Time-Logging-System-Pacaldo

## Student Information

- **Name:** Sebastian Pacaldo  
- **Program:** Bachelor of Science in Computer Science  
- **Course Code:** IT6314 – Web Application Development 1  

---

## Final Activity - Practical Exam: Employee Time Logging System (PHP PDO)

This project is a complete **Employee Time Logging System** using **PHP PDO** and **MySQL**, designed to manage employee time-in/out records with secure login access, data filtering, profile management, and admin functionality. It uses session handling, form validation, and Bootstrap for an enhanced user interface.

---

## Files Included

| Filename             | Description                                          |
|----------------------|------------------------------------------------------|
| `index.php`          | Redirect page to `login.php`                         |
| `login.php`          | Login page for Admin and Employees                   |
| `employee.php`       | Employee dashboard with time logging features        |
| `admin.php`          | Admin dashboard with filters, export, and management |
| `create_employee.php`| Create new employee account (Admin-only)             |
| `logout.php`         | Logs out the current user and destroys session       |
| `db.php`             | Database connection settings using PDO               |
| `timelogdb.sql`      | SQL file for `timelogdb` (includes `users` and `timelogs` tables) |
| `README.md`          | This documentation file                              |

---

## System Features

### Employee Features
- Time In / Time Out functionality
- View personal time logs
- Edit profile (first name, last name, position)
- Change password and username
- Tabbed interface for easier navigation

### Admin Features
- View logs of all employees
- Filter logs by employee, date, or log type (In/Out)
- Export filtered logs to CSV
- Edit admin profile and credentials
- Add/Create new employee accounts

---

## Setup Instructions

### 1. Upload Files
- Upload all PHP files to your InfinityFree **htdocs** directory using the File Manager or an FTP client.

### 2. Import SQL File
- Log in to your [InfinityFree Control Panel](https://app.infinityfree.net)
- Open **phpMyAdmin**
- Select your database (e.g., `if0_39624585_timelogdb`)
- Import the file `timelogdb.sql`, which creates the `users` and `timelogs` tables.

### 3. Database Configuration
- Open `db.php` and update it with your InfinityFree database credentials:

```php
$host = "sqlXXX.infinityfree.com"; // Replace with your DB host
$dbname = "epiz_XXX_timelogdb";   // Replace with your DB name
$username = "epiz_XXX";           // Replace with your DB username
$password = "your_password";      // Replace with your DB password.

---

## Notes
Passwords are securely hashed using password_hash()
Secure login sessions for Admin and Employees
All database interactions use PDO with prepared statements
Responsive design using Bootstrap

---

## Online Deployment (Optional)
Hosting: InfinityFree
Live Demo: https://yourdomain.infinityfreeapp.com  
>Replace the above link with your actual InfinityFree domain if deployed.

---

## Login Credentials (Sample)
| Role     | Username  | Password  |
|----------|-----------|-----------|
| Admin    | admin     | admin123  |
| Employee | employee1 | emp123    |
>You may update credentials directly in the database or through the Change Credentials tab.

---

## Author
Developed by **Sebastian Pacaldo**
As part of the course **IT6314 – Web Application Development 1**


