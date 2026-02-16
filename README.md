# 📚 Student Record System

A comprehensive **Student Record Management System** built with **PHP, MySQL, HTML, and CSS**. This system provides separate dashboards for students and administrators, fee management, result tracking, and financial management.

<img width="1895" height="862" alt="image" src="https://github.com/user-attachments/assets/5a81299e-f687-416a-bfad-d9c298228144" />

*Main interface of the Student Record System*

---

## 📋 Table of Contents
- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [Complete Project Structure](#-complete-project-structure)
- [CSS Files Overview](#-css-files-overview)
- [Image Assets](#-image-assets)
- [PHP Modules](#-php-modules)
- [Installation & Setup](#-installation--setup)
- [Database Configuration](#-database-configuration)
- [User Roles](#-user-roles)
- [Module Documentation](#-module-documentation)
- [Screenshots](#-screenshots)
- [Future Enhancements](#-future-enhancements)
- [Contact](#-contact)

---

## 🚀 Features

### 👨‍🎓 Student Module
- Student registration and login
- Personal profile management
- View academic results
- Check fee status
- Student dashboard

### 👨‍💼 Admin Module
- Admin dashboard
- Student management (Add, Edit, Delete)
- Result management
- Fee management
- Financial tracking
- User authentication

### 💰 Finance Module
- Fee collection
- Payment tracking
- Financial reports
- Expense management
- Due date reminders

### 📊 Result Module
- Add/update results
- View results by student
- Generate report cards
- Performance analytics

---

## 🛠️ Technologies Used

| Category | Technologies |
|----------|--------------|
| **Frontend** | HTML5, CSS3, JavaScript |
| **Backend** | PHP |
| **Database** | MySQL |
| **Server** | XAMPP (Apache + MySQL) |
| **Styling** | Custom CSS Files |

---
## 📁 Project Structure

```
Loadingg/
├── php/
│   ├── add_student.php
│   ├── admin_dashboard.php
│   ├── db.php
│   ├── fees.php
│   ├── get_student.php
│   ├── login.php
│   ├── manage_finances.php
│   ├── manage_results.php
│   ├── manage_students.php
│   ├── profile.php
│   ├── register.php
│   ├── results.php
│   └── student_dashboard.php
│
├── css/
│   ├── add_student.css
│   ├── dashboard.css
│   ├── fees.css
│   ├── manage_finances.css
│   ├── manage_result.css
│   ├── manage_student.css
│   ├── profile.css
│   ├── results.css
│   ├── student_dashboard.css
│   ├── sty.css
│   └── style.css
│
├── images/
│   ├── first.png
│   ├── second.png
│   ├── third.png
│   ├── four.png
│   ├── records-management.jpg
│   ├── security-interface.jpg
│   ├── security.png
│   └── [other image files...]
│
└── index.html
```

---

## 🎨 CSS Files Overview

| CSS File | Purpose |
|----------|---------|
| `add_student.css` | Styling for the add student form page |
| `dashboard.css` | Main dashboard layout and design |
| `fees.css` | Fee management interface styling |
| `manage_finances.css` | Financial reports and tracking |
| `manage_result.css` | Result entry and management interface |
| `manage_student.css` | Student list and management table |
| `profile.css` | User profile page design |
| `results.css` | Student results display |
| `student_dashboard.css` | Student-specific dashboard |
| `sty.css` | Additional utility styles |
| `style.css` | Global styles and variables |

---

## 🖼️ Image Assets

| Image | Purpose |
|-------|---------|
| `first.png` | Main interface screenshot |
| `second.png` | Dashboard screenshot |
| `third.png` | Results page screenshot |
| `four.png` | Fees page screenshot |
| `database.png` | Database illustration |
| `security.png` | Security features illustration |
| `donut-chart.png` | Analytics visualization |
| `records-management.jpg` | Records management illustration |
| `dashboard-preview.jpg` | Dashboard preview |
| `contact-bg.jpg` | Contact page background |
| `features-bg.jpg` | Features section background |
| `header-bg.jpg` | Header background |
| `pattern.png` | Background pattern |
| `hi.webp` | Welcome illustration |
| `kk.jpg` | Additional design element |
| `wp2951423.png` | Wallpaper background |

---

## ⚙️ PHP Modules

| PHP File | Description |
|----------|-------------|
| `add_student.php` | Form to add new student records |
| `admin_dashboard.php` | Admin control panel with statistics |
| `db.php` | Database connection and configuration |
| `fees.php` | Fee collection and tracking |
| `get_student.php` | AJAX endpoint to fetch student data |
| `login.php` | User authentication |
| `manage_finances.php` | Financial reports and management |
| `manage_results.php` | Add/edit student results |
| `manage_students.php` | View and manage all students |
| `profile.php` | View/edit user profile |
| `register.php` | New user registration |
| `results.php` | Display student results |
| `student_dashboard.php` | Student view dashboard |

---

## ⚙️ Installation & Setup

### Prerequisites

- **XAMPP** (or any local server with PHP and MySQL)
- **Web Browser** (Chrome, Firefox, Edge)
- **Text Editor** (VS Code, Sublime Text)

### Step-by-Step Installation

1. Install XAMPP
   - Download from: https://www.apachefriends.org/
   - Install with default settings

2. Open XAMPP Control Panel
   - Start Apache and MySQL services

3. Copy Project Files
   - Navigate to XAMPP htdocs:
     cd C:\xampp\htdocs\
   
   - Create project folder:
     mkdir Loadingg
   
   - Copy all project files into Loadingg/php/
     (Manually copy or use file explorer)

4. Set up Database
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create new database: student_record_system
   - Import database schema (create tables as needed)

5. Configure Database Connection
   - Edit php/db.php with your database credentials:
     
     <?php
     $host = "localhost";
     $username = "root";
     $password = "";
     $database = "student_record_system";
     
     $conn = mysqli_connect($host, $username, $password, $database);
     
     if (!$conn) {
         die("Connection failed: " . mysqli_connect_error());
     }
     
     ?>

6. Run the Application
   - Open browser and visit:
     http://localhost/Loadingg/php/index.html

## 👥 User Roles

**🔴 Admin User**
- Username: admin
- Password: admin123
- Access: Full system access

**🟢 Student User**
- Username: student_id (e.g., STU001)
- Password: student123
- Access: View own profile, results, fees

---

## 📖 Module Documentation

**🔐 Authentication Module**
- login.php - User login
- register.php - New registration
- profile.php - Profile management

**👨‍🎓 Student Management**
- manage_students.php - View all students
- add_student.php - Add new student
- get_student.php - Fetch student details

**📊 Results Management**
- manage_results.php - Add/edit results
- results.php - View results
- Student-specific result display

**💰 Fees Management**
- fees.php - Fee collection
- manage_finances.php - Financial reports
- Payment tracking
  
## 📸 Screenshots

<div align="center">
  
| | |
|:---:|:---:|
| <img src="https://github.com/user-attachments/assets/0224ea5b-c851-4c45-b802-def1d4f8b3c6" width="300"> | <img src="https://github.com/user-attachments/assets/d2ee9a5d-ce16-4dd8-8423-b881821d4a3f" width="300"> |
| **Dashboard** | **Student Management** |

| | |
|:---:|:---:|
| <img src="https://github.com/user-attachments/assets/bf12757b-4710-4c45-a244-7ffad18a975e" width="300"> | <img src="https://github.com/user-attachments/assets/d00904db-73f2-4d0b-994d-32c5b8843b78" width="300"> |
| **Results Page** | **Fees Management** |

| | |
|:---:|:---:|
| <img src="https://github.com/user-attachments/assets/ee94ba89-0d0b-4fa9-83dd-8db7833af079" width="300"> | <img src="https://github.com/user-attachments/assets/a9940492-5c84-4a50-9893-c890234a3e07" width="300"> |
| **Admin Panel** | **Student View** |

| | |
|:---:|:---:|
| <img src="https://github.com/user-attachments/assets/2031cb20-890d-4310-87fa-becb4a6dead5" width="300"> | <img src="https://github.com/user-attachments/assets/250a8fa9-9caa-4b38-ac3a-e6406b013b41" width="300"> |
| **Login Page** | **Registration Page** |

</div>

## 🔮 Future Enhancements

- [ ] **Email Notifications** - Send fee reminders and result alerts via email
- [ ] **SMS Integration** - Text message alerts for important updates
- [ ] **Online Payments** - Integrated payment gateway (PayPal, Stripe, etc.)
- [ ] **Mobile App** - Android/iOS application for students and parents
- [ ] **Attendance System** - Track student attendance with QR code
- [ ] **Library Management** - Book borrowing and return system
- [ ] **Hostel Management** - Accommodation tracking for boarding students
- [ ] **Transport Management** - Bus route and vehicle tracking
- [ ] **Parent Portal** - Parents can monitor their child's progress
- [ ] **Export Reports** - PDF/Excel export of results and fees
- [ ] **Graphs & Charts** - Visual analytics of student performance
- [ ] **Multi-language Support** - Interface in multiple languages
- [ ] **Backup & Restore** - Automated database backup system
- [ ] **Biometric Integration** - Fingerprint/face recognition for attendance

---

## 📞 Contact

| | |
|---|---|
| **Name** | Kushma Shrestha |
| **Email** | arushsthaii@gmail.com |
| **GitHub** | [@Kushmma](https://github.com/Kushmma) |
| **Instagram** | [@kushmma](https://instagram.com/kushmma) |
