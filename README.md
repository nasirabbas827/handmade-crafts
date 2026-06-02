# Handmade_Crafts_final  

A web‑based platform for managing and showcasing handmade crafts. The application provides an **admin dashboard** for product, category, user, and order management, as well as a **buyer interface** that integrates PHPMailer for email notifications (e.g., order confirmations, contact forms).

---

## Overview  

Handmade_Crafts_final is a PHP‑driven e‑commerce prototype that allows artisans to list their products and administrators to control the entire site. The repository contains:

| Directory | Important files |
|-----------|-----------------|
| `admin/` | Core admin pages (`admin_home.php`, `admin_login.php`, `admin_product.php`, …) and configuration (`config.php`, `css/style.css`). |
| `buyer/PHPMailer/` | PHPMailer library (source, license, docs). |
| `Database/` | `crafts_db.sql` – initial schema and seed data. |
| Root | `Project File.docx` – design specification (reference only). |

The system is built with **plain PHP** (no framework) and uses **MySQL** for persistence.

---

## Features  

- **Admin Dashboard**  
  - Secure login (`admin_login.php`).  
  - CRUD for categories, products, users, and announcements.  
  - Order reporting and complaint/review viewing.  
  - Site‑wide settings management.  

- **Buyer Experience**  
  - Product browsing and purchase flow.  
  - Automated email notifications via PHPMailer (order confirmation, contact messages).  

- **Database**  
  - Normalized schema for crafts, categories, users, orders, and reviews.  

- **Responsive UI**  
  - Simple CSS (`admin/css/style.css`) for a clean, mobile‑friendly admin interface.  

---

## Tech Stack  

| Layer | Technology |
|-------|------------|
| **Server** | PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **Email** | PHPMailer (bundled in `buyer/PHPMailer/`) |
| **Front‑end** | HTML5, CSS3 (admin panel) |
| **Version Control** | Git |

---

## Installation  

> **Prerequisites**  
> - PHP 7.4 or newer with `mysqli` extension enabled.  
> - MySQL server.  
> - A web server (Apache/Nginx) configured to serve PHP files.  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/Handmade_Crafts_final.git
   cd Handmade_Crafts_final
   ```

2. **Create the database**  

   ```bash
   mysql -u root -p
   CREATE DATABASE crafts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;
   ```

3. **Import the schema**  

   ```bash
   mysql -u root -p crafts_db < Database/crafts_db.sql
   ```

4. **Configure the application**  

   Edit `admin/config.php` and set your credentials:

   ```php
   // admin/config.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'YOUR_DB_USERNAME');
   define('DB_PASS', 'YOUR_DB_PASSWORD');
   define('DB_NAME', 'crafts_db');

   // PHPMailer SMTP settings (example)
   define('SMTP_HOST', 'smtp.example.com');
   define('SMTP_USER', 'YOUR_SMTP_USERNAME');
   define('SMTP_PASS', 'YOUR_SMTP_PASSWORD');
   define('SMTP_PORT', 587);
   ```

5. **Set up the web root**  

   - Point your web server’s document root to the repository folder (or create a virtual host).  
   - Ensure the `admin/` folder is accessible via `http://yourdomain.com/admin/`.

6. **Adjust file permissions** (if needed)