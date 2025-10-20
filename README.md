
# 🗂️ E-Archive System – PT Asrindo Environt Investama

**E-Archive Asrindo** is a web-based document and record management system developed for **PT Asrindo Environt Investama**.  
This system simplifies the structured storage, archiving, and retrieval of digital documents — including **Incoming Letters, Outgoing Letters, Invoices, and Draft Documents** — complete with **user management and activity logging**.

---

## ✨ Main Features

### 👨‍💼 User Management
- Secure login and logout system using PHP native sessions.  
- Page protection with `auth_check.php`.

### 📨 Incoming Letters
- CRUD management (Create, Read, Update, Delete).  
- Upload PDF or JPG attachments.  
- Auto-generate archive number and received date.

### 📤 Outgoing Letters
- Manage outgoing correspondence with number, recipient, subject, and attachments.

### 🧾 Invoices
- Input and search invoices by project or client.  
- Store and preview PDF invoice files.

### 📑 Draft Documents
- Save internal documents such as memos, agreements, and proposals.  
- Upload and preview files grouped by department.

### 🏢 Departments
- CRUD operations for department data used for archive grouping.

### 📜 Activity Logs
- Automatically logs all CRUD actions into `log_aktivitas` table.  
- Displays user activity history with timestamps.

### 📱 Responsive Design
- Modern interface powered by **TailwindCSS**.  
- Adaptive sidebar with smooth animation and hamburger toggle button.

---

## 🧱 Technology Stack

| Layer | Technology |
|--------|-------------|
| **Frontend** | HTML5, CSS3 (TailwindCSS), JavaScript, Lucide Icons |
| **Backend** | PHP Native |
| **Database** | MySQL |
| **Server** | Apache (XAMPP / Laragon) |
| **Security** | PHP Session, Input Sanitization |
| **Upload Handling** | PHP `move_uploaded_file()` |

---

## 📂 Folder Structure

```
archive-management/
├── config/
│   └── koneksi.php
│
├── includes/
│   ├── auth_check.php
│   ├── log_helper.php
│   ├── sidebar.php
│   ├── topbar.php
│   └── loader.php
│
├── modules/
│   ├── dashboard/
│   ├── departemen/
│   ├── surat_masuk/
│   ├── surat_keluar/
│   ├── invoice/
│   ├── draft_dokumen/
│   └── log_aktivitas/
│
├── assets/
│   └── img/
│
├── src/
│   └── output.css
│
├── uploads/
│   ├── surat_masuk/
│   ├── surat_keluar/
│   ├── invoice/
│   └── draft/
│
├── index.php
├── login.php
├── logout.php
└── .htaccess
```

---

## 🧮 Database Structure

### Database: `db_asrindo_archive`

#### `user`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| nama | VARCHAR(100) | Full name |
| username | VARCHAR(50) | Login username |
| password | VARCHAR(255) | Encrypted password |
| role | ENUM('admin', 'staff') | User level |
| created_at | DATETIME | Created date |

#### `departemen`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| nama_departemen | VARCHAR(100) | Department name |

#### `surat_masuk`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| nomor_surat | VARCHAR(50) | Incoming letter number |
| pengirim | VARCHAR(100) | Sender |
| perihal | TEXT | Subject |
| tanggal_masuk | DATE | Received date |
| file | VARCHAR(255) | Attached file |
| departemen_id | INT(11) | Linked department |

#### `surat_keluar`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| nomor_surat | VARCHAR(50) | Outgoing letter number |
| tujuan | VARCHAR(100) | Recipient |
| perihal | TEXT | Subject |
| tanggal_keluar | DATE | Sent date |
| file | VARCHAR(255) | Attachment |
| departemen_id | INT(11) | Linked department |

#### `invoice`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| nomor_invoice | VARCHAR(50) | Invoice number |
| klien | VARCHAR(100) | Client name |
| nominal | DECIMAL(15,2) | Amount |
| tanggal | DATE | Created date |
| file | VARCHAR(255) | Invoice file path |

#### `draft_dokumen`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| judul | VARCHAR(150) | Document title |
| keterangan | TEXT | Short description |
| file | VARCHAR(255) | File path |
| departemen_id | INT(11) | Linked department |

#### `log_aktivitas`
| Field | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key |
| user | VARCHAR(100) | User name |
| aksi | VARCHAR(100) | Action type |
| keterangan | TEXT | Details |
| waktu | DATETIME | Timestamp |

---

## ⚙️ Installation Guide

### 1️⃣ Preparation
- Ensure **XAMPP** or **Laragon** is installed.  
- Activate **Apache** and **MySQL**.

### 2️⃣ Import Database
1. Open **phpMyAdmin → Import**  
2. Choose `db_asrindo_archive.sql`  
3. Click **Go**

### 3️⃣ Configure Database Connection
Edit `config/koneksi.php`:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_asrindo_archive";
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
?>
```

### 4️⃣ Run the Project
Place the folder in:
```
C:\xampp\htdocs\archive-management
```

Then open in browser:
```
http://localhost/archive-management
```

### 5️⃣ Default Accounts
| Username | Password | Role |
|-----------|-----------|------|
| admin | admin123 | admin |
| staff | staff123 | staff |

---

## 🌐 Deployment on Domain

When deployed on a domain (e.g., `archive.asrindo.com`), use this `.htaccess` configuration:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ $1.php [L]

DirectoryIndex login.php
```

---

## 🧰 Developer Notes

Use `log_helper.php` to automatically log new activities:

```php
logAktivitas($conn, "Add Incoming Letter", "Admin added a letter from PT Bina Jaya");
```

All logs are automatically recorded in the `log_aktivitas` table.
---

## 🏷️ License
This project is an internal system of **PT Asrindo Environt Investama**.  
Redistribution or modification is prohibited without written permission from the developer or the company.

---

## 📎 Screenshots

*(Add screenshots of the dashboard, incoming letters page, or invoice page here to enhance your GitHub README.)*

---

> Built with ❤️ by **Fattah Chaerul Majid**  
> Supporting the digital archiving transformation of **PT Asrindo Environt Investama**.
