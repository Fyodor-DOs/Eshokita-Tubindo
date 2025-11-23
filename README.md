# PT Eshokita - Sistem Manajemen Penjualan dan Pengiriman

Aplikasi web berbasis CodeIgniter 4 untuk mengelola penjualan, pengiriman, invoice, pembayaran, dan stock produk air minum kemasan.

## Deskripsi

Sistem ini dirancang untuk membantu PT Eshokita dalam mengelola operasional bisnis penjualan dan distribusi produk air minum dengan berbagai varian (Kristal Besar, Kristal Kecil, Serut) dalam kemasan 10kg dan 20kg. Aplikasi ini menyediakan fitur lengkap mulai dari pengelolaan customer, produk, invoice, pembayaran, pengiriman, hingga laporan rekap penjualan.

## Fitur Utama

### 1. **Dashboard**
   - Total Penjualan Bulan Ini (dengan breakdown Cash dan Kredit)
   - Total Customer
   - Total Pengiriman
   - Rekap Penjualan Bulanan (dengan filter bulan)
   - Tabel detail penjualan per nota dengan varian produk
   - Export ke PDF

### 2. **Manajemen Customer**
   - CRUD Customer (Tambah, Edit, Hapus)
   - Data: Nama, Alamat, No. Telp, Rute, Tipe Pembayaran
   - Tabel dengan DataTables (Search, Sort, Export)

### 3. **Manajemen Produk**
   - Kategori Produk (Kristal Besar, Kristal Kecil, Serut)
   - Detail Produk dengan Unit Weight (10kg, 20kg)
   - Manajemen Stock
   - Harga per produk

### 4. **Invoice & Pembayaran**
   - Pembuatan Invoice dengan multiple items
   - Status: Draft, Pending, Lunas
   - Tipe Pembayaran: Cash, Kredit
   - Detail Items dengan qty dan harga
   - History pembayaran
   - Filter ganda: Status Pembayaran & Status Pengiriman
   - Print Invoice

### 5. **Pengiriman**
   - Kelola status pengiriman (Siap, Mengirim, Diterima, Gagal)
   - Upload bukti pengiriman
   - Tracking pengiriman
   - Surat Jalan terintegrasi
   - Tampilan customer di setiap pengiriman

### 6. **Surat Jalan**
   - Generate surat jalan otomatis
   - Detail barang yang dikirim
   - Print surat jalan (Single & Batch)
   - Format: KB (Kristal Besar), KK (Kristal Kecil), SRT (Serut)
   - Tampilan customer terintegrasi

### 7. **Penerimaan**
   - Catat penerimaan barang
   - Stock In management
   - History penerimaan

### 8. **Stock Management**
   - Monitoring stock real-time
   - Stock per kategori produk
   - Riwayat transaksi stock
   - Stock In/Out tracking

### 9. **Rute Pengiriman**
   - Kelola rute pengiriman
   - Assign customer ke rute
   - Filter berdasarkan rute

### 10. **Rekap Penjualan**
   - Laporan penjualan bulanan detail
   - Breakdown per varian produk (KB10kg, KB20kg, KK10kg, KK20kg, SRT10kg, SRT20kg)
   - Summary Cash vs Kredit
   - Export PDF dengan format optimized
   - Tabel Sisa vs Laku per kategori

### 11. **User Management**
   - Authentication (Login/Logout)
   - Forgot Password dengan Token Reset
   - Role-based access (Admin, Staff)
   - Password hashing dengan bcrypt
   - Brute force protection (5 attempts, 5 menit lockout)

## 🛠️ Teknologi & Tools

### Backend
- **Framework**: CodeIgniter 4.5.x
- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL/MariaDB
- **PDF Generator**: Dompdf
- **Authentication**: Session-based dengan bcrypt

### Frontend
- **CSS Framework**: Bootstrap 5.3.3
- **Icons**: Bootstrap Icons 1.11.3
- **JavaScript**: jQuery 3.6.x
- **DataTables**: 2.x (dengan Responsive, Buttons extensions)
  - Export: Copy, CSV, Excel, PDF, Print
- **Alert/Modal**: SweetAlert2 11.x
- **AJAX**: Fetch API & jQuery AJAX

### Development Tools
- **Dependency Manager**: Composer
- **Version Control**: Git
- **Testing**: PHPUnit (framework bawaan CI4)

## Requirements

- PHP 8.1 atau lebih tinggi dengan extensions:
  - `intl`
  - `mbstring`
  - `mysqlnd`
  - `xml`
  - `gd` atau `imagick`
- MySQL 5.7+ atau MariaDB 10.3+
- Composer 2.x
- Web Server: Apache/Nginx (dengan mod_rewrite untuk Apache)

## Instalasi

### 1. Install Dependencies
```bash
cd PT_Eshokita
composer install
```

### 2. Setup Environment
Copy file `.env.example` ke `.env` lalu edit sesuai kebutuhan:
```bash
copy .env.example .env
```

### 3. Buat Database
```sql
CREATE DATABASE pt_eshokita;
```

### 4. Jalankan Migration
```bash
php spark migrate
```

### 5. Jalankan Seeder 
```bash
php spark db:seed UserSeeder
php spark db:seed ProductCategorySeeder
php spark db:seed ProductSeeder
php spark db:seed RuteSeeder
```

### 6. Jalankan Development Server
```bash
php spark serve
```

Buka browser: `http://localhost:8080`

## Struktur Project

```
PT_Eshokita/
├── app/
│   ├── Config/          # Konfigurasi aplikasi
│   │   ├── Routes.php   # Routing
│   │   ├── Database.php # Database config
│   │   ├── Filters.php  # Middleware
│   │   └── ...
│   ├── Controllers/     # Controller (MVC)
│   │   ├── DashboardController.php
│   │   ├── CustomerController.php
│   │   ├── InvoiceController.php
│   │   ├── PaymentController.php
│   │   ├── PengirimanController.php
│   │   ├── SuratJalanController.php
│   │   ├── ProductController.php
│   │   ├── StockController.php
│   │   ├── UserController.php
│   │   └── ...
│   ├── Models/          # Model (MVC)
│   │   ├── CustomerModel.php
│   │   ├── InvoiceModel.php
│   │   ├── PaymentModel.php
│   │   ├── PengirimanModel.php
│   │   ├── ProductModel.php
│   │   ├── StockModel.php
│   │   ├── UserModel.php
│   │   └── ...
│   ├── Views/           # View (MVC)
│   │   ├── components/  # Reusable components
│   │   ├── pages/       # Page views
│   │   │   ├── auth/
│   │   │   ├── dashboard/
│   │   │   ├── customer/
│   │   │   ├── invoice/
│   │   │   ├── pengiriman/
│   │   │   ├── surat_jalan/
│   │   │   ├── rekap/
│   │   │   └── ...
│   │   └── layouts/     # Layout templates
│   ├── Filters/         # Auth & Role filters
│   │   ├── AuthFilter.php
│   │   └── RoleFilter.php
│   ├── Helpers/         # Helper functions
│   │   └── auth_helper.php
│   └── Database/
│       ├── Migrations/  # Database migrations
│       └── Seeds/       # Database seeders
├── public/
│   ├── assets/
│   │   ├── css/         # Stylesheets
│   │   ├── js/          # JavaScript
│   │   │   ├── tables/  # DataTables configs
│   │   │   └── script.js
│   │   ├── img/         # Images
│   │   └── vendor/      # Frontend libraries
│   ├── uploads/         # User uploads
│   └── index.php        # Entry point
├── writable/            # Logs, cache, session
│   ├── logs/
│   ├── cache/
│   ├── session/
│   └── uploads/
├── vendor/              # Composer dependencies
├── .env                 # Environment config (buat dari env)
├── composer.json        # PHP dependencies
└── README.md            # Dokumentasi ini
```

## Database Schema

### Tabel Utama:
1. **users** - User accounts
2. **customer** - Data pelanggan
3. **product_category** - Kategori produk (KB, KK, SRT)
4. **product** - Produk dengan unit weight
5. **invoice** - Invoice penjualan
6. **transaction** - Detail items invoice
7. **payment** - History pembayaran
8. **pengiriman** - Data pengiriman
9. **surat_jalan** - Surat jalan pengiriman
10. **stock** - Stock produk
11. **stock_transaction** - Riwayat stock
12. **rute** - Rute pengiriman
13. **shipment_tracking** - Tracking pengiriman

## Workflow Bisnis

### Proses Penjualan:
1. **Buat Customer** → Customer Management
2. **Buat Invoice** → Pilih customer, tambah items (produk + qty)
3. **Catat Pembayaran** → Cash/Kredit, bisa sebagian
4. **Buat Surat Jalan** → Generate dari invoice
5. **Kirim Barang** → Update status pengiriman
6. **Tracking** → Monitor status pengiriman
7. **Laporan** → Rekap penjualan bulanan

### Status Flow:
- **Invoice**: Draft → Pending → Lunas
- **Pengiriman**: Siap → Mengirim → Diterima/Gagal
- **Pembayaran**: Belum Bayar → Sebagian → Lunas