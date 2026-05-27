# 🧴 Parfum Store API

RESTful API untuk **Sistem Toko Online Parfum (Mini E-Commerce)** — Proyek Akhir Web Service & API.

Dibangun menggunakan **Laravel 12**, **MySQL**, **Laravel Sanctum** untuk autentikasi, dan **Knuckles Scribe** untuk dokumentasi API otomatis.

---

## 📋 Daftar Isi

- [Tech Stack](#-tech-stack)
- [Fitur Utama](#-fitur-utama)
- [Prasyarat](#-prasyarat)
- [Instalasi & Setup](#-instalasi--setup)
- [Menjalankan Project](#-menjalankan-project)
- [Struktur Database](#-struktur-database)
- [Daftar API Endpoints](#-daftar-api-endpoints)
- [Panduan Pengujian (Postman)](#-panduan-pengujian-postman)
- [Dokumentasi API (Scribe)](#-dokumentasi-api-scribe)
- [Akun Default](#-akun-default)
- [Struktur Folder](#-struktur-folder)

---

## 🛠 Tech Stack

| Teknologi | Keterangan |
|---|---|
| **Laravel 12** | Framework PHP utama |
| **MySQL / MariaDB** | Database relasional |
| **Laravel Sanctum** | Token-based Authentication |
| **Knuckles Scribe** | Auto-generate API Documentation |
| **Faker** | Generate data dummy |
| **Postman** | Pengujian API |

---

## ✨ Fitur Utama

1. **Autentikasi & Keamanan** — Register, Login (Token), Logout menggunakan Laravel Sanctum
2. **CRUD Lengkap** — Kategori, Produk, dan Transaksi
3. **File Upload** — Upload & hapus gambar produk ke storage lokal
4. **Eager Loading** — Detail kategori muncul nested di response produk
5. **Pagination & Filtering** — Paginate data produk + pencarian (search) + filter kategori
6. **Database Seeder & Faker** — Auto-generate data dummy saat `migrate:fresh --seed`
7. **Dokumentasi Interaktif** — Halaman `/docs` otomatis dari Scribe

---

## 📌 Prasyarat

Pastikan software berikut sudah terinstal di komputer Anda:

| Software | Versi Minimum | Cek Versi |
|---|---|---|
| **PHP** | ≥ 8.2 | `php -v` |
| **Composer** | ≥ 2.x | `composer -V` |
| **MySQL / MariaDB** | ≥ 5.7 | `mysql --version` |
| **XAMPP** (opsional) | Terbaru | Bisa digunakan untuk PHP & MySQL |

> **💡 Tips:** Jika menggunakan XAMPP, pastikan **Apache** dan **MySQL** sudah dijalankan melalui XAMPP Control Panel.

---

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd UAS-WSA
```

> Atau jika sudah memiliki folder project, langsung masuk ke direktori project.

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parfum_store
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL Anda. Default XAMPP biasanya username `root` tanpa password.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat Database

Buat database `parfum_store` di MySQL. Bisa melalui:

**Opsi A — phpMyAdmin (XAMPP):**
1. Buka `http://localhost/phpmyadmin`
2. Klik **"New"** di sidebar kiri
3. Ketik `parfum_store` → klik **Create**

**Opsi B — Command Line:**

```bash
mysql -u root -e "CREATE DATABASE parfum_store;"
```

> Jika MySQL tidak ada di PATH, gunakan path lengkap XAMPP:
> ```bash
> C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE parfum_store;"
> ```

### 6. Jalankan Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
- Membuat semua tabel (users, categories, products, transactions, personal_access_tokens)
- Mengisi database dengan data dummy:
  - 1 akun Admin
  - 1 akun Customer Demo
  - 5 Kategori Parfum
  - 30 Produk Parfum
  - 5 Transaksi Contoh

### 7. Buat Storage Link

```bash
php artisan storage:link
```

> Ini membuat symbolic link dari `public/storage` ke `storage/app/public` agar file gambar yang diupload bisa diakses via URL.

### 8. Generate Dokumentasi API

```bash
php artisan scribe:generate
```

---

## ▶ Menjalankan Project

```bash
php artisan serve
```

Server akan berjalan di:

| URL | Keterangan |
|---|---|
| `http://localhost:8000` | Base URL |
| `http://localhost:8000/docs` | 📖 Dokumentasi API (Scribe) |
| `http://localhost:8000/api/products` | Contoh endpoint produk |

> Tekan `Ctrl + C` di terminal untuk menghentikan server.

---

## 🗄 Struktur Database

### Entity Relationship Diagram

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────────┐
│    users     │       │    categories    │       │    products      │
├──────────────┤       ├──────────────────┤       ├──────────────────┤
│ id (PK)      │       │ id (PK)          │       │ id (PK)          │
│ name         │       │ name             │       │ category_id (FK) │──→ categories.id
│ email        │       │ slug             │       │ name             │
│ password     │       │ description      │       │ slug             │
│ timestamps   │       │ timestamps       │       │ description      │
└──────┬───────┘       └──────────────────┘       │ price            │
       │                                          │ stock            │
       │                                          │ image            │
       │                                          │ timestamps       │
       │                                          └──────┬───────────┘
       │         ┌──────────────────┐                     │
       │         │  transactions    │                     │
       │         ├──────────────────┤                     │
       └────────→│ user_id (FK)     │                     │
                 │ product_id (FK)  │←────────────────────┘
                 │ quantity         │
                 │ total_price      │
                 │ status           │
                 │ payment_method   │
                 │ shipping_address │
                 │ notes            │
                 │ timestamps       │
                 └──────────────────┘
```

### Relasi Antar Tabel

| Relasi | Tipe | Keterangan |
|---|---|---|
| `User` → `Transaction` | One to Many | 1 user punya banyak transaksi |
| `Category` → `Product` | One to Many | 1 kategori punya banyak produk |
| `Product` → `Transaction` | One to Many | 1 produk bisa ada di banyak transaksi |

---

## 📡 Daftar API Endpoints

### 🔓 Public Endpoints (Tanpa Autentikasi)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/api/register` | Registrasi user baru |
| `POST` | `/api/login` | Login & dapatkan token |
| `GET` | `/api/categories` | Lihat semua kategori |
| `GET` | `/api/categories/{id}` | Detail kategori + produknya |
| `GET` | `/api/products` | Lihat semua produk (paginated) |
| `GET` | `/api/products/{id}` | Detail produk + kategorinya |

### 🔒 Protected Endpoints (Butuh Token)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/api/logout` | Logout & hapus token |
| `GET` | `/api/user` | Lihat profil user yang login |
| `POST` | `/api/categories` | Tambah kategori baru |
| `PUT` | `/api/categories/{id}` | Update kategori |
| `DELETE` | `/api/categories/{id}` | Hapus kategori |
| `POST` | `/api/products` | Tambah produk + upload gambar |
| `POST` | `/api/products/{id}` | Update produk + ganti gambar |
| `DELETE` | `/api/products/{id}` | Hapus produk + gambarnya |
| `GET` | `/api/transactions` | Lihat transaksi user |
| `GET` | `/api/transactions/{id}` | Detail transaksi |
| `POST` | `/api/transactions` | Buat transaksi baru |
| `PUT` | `/api/transactions/{id}` | Update status transaksi |
| `DELETE` | `/api/transactions/{id}` | Hapus transaksi (pending only) |

### Query Parameters (Produk)

| Parameter | Tipe | Contoh | Keterangan |
|---|---|---|---|
| `search` | string | `?search=Midnight` | Cari produk berdasarkan nama/deskripsi |
| `category_id` | integer | `?category_id=1` | Filter produk berdasarkan kategori |
| `per_page` | integer | `?per_page=10` | Jumlah data per halaman (default: 10) |
| `page` | integer | `?page=2` | Nomor halaman |

**Contoh kombinasi:**
```
GET /api/products?search=Rose&category_id=2&per_page=5&page=1
```

---

## 🧪 Panduan Pengujian (Postman)

### Langkah 1: Login untuk Mendapatkan Token

**Request:**
```
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "admin@parfumstore.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin Parfum Store",
            "email": "admin@parfumstore.com"
        },
        "token": "1|abcdef123456..."
    }
}
```

> **📌 Salin nilai `token` dari response ini!**

### Langkah 2: Setup Authorization di Postman

Untuk mengakses endpoint yang dilindungi (protected):

1. Buka tab **Authorization** di Postman
2. Pilih Type: **Bearer Token**
3. Paste token yang didapat dari login
4. Atau tambahkan header manual:
   ```
   Authorization: Bearer 1|abcdef123456...
   ```

### Langkah 3: Contoh Request

#### 📦 Lihat Produk (dengan Pagination & Search)
```
GET http://localhost:8000/api/products?search=Oud&per_page=5
```
> Tidak perlu token (public endpoint)

#### ➕ Tambah Produk Baru (dengan Upload Gambar)
```
POST http://localhost:8000/api/products
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body (form-data):
- category_id: 1
- name: Luxury Amber
- description: Parfum mewah dengan aroma amber
- price: 1500000
- stock: 20
- image: [pilih file gambar]
```

#### 🛒 Buat Transaksi
```
POST http://localhost:8000/api/transactions
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2,
    "payment_method": "transfer_bank",
    "shipping_address": "Jl. Sudirman No. 1, Jakarta Pusat"
}
```

#### 🔄 Update Status Transaksi
```
PUT http://localhost:8000/api/transactions/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "processing"
}
```

#### 🚪 Logout
```
POST http://localhost:8000/api/logout
Authorization: Bearer {token}
```

---

## 📖 Dokumentasi API (Scribe)

Dokumentasi interaktif otomatis tersedia di:

```
http://localhost:8000/docs
```

Fitur dokumentasi:
- **Try It Out** — Test endpoint langsung dari browser
- **Contoh Request** — Dalam format Bash dan JavaScript
- **Grouped by Feature** — Authentication, Categories, Products, Transactions
- **Postman Collection** — Auto-generated di `storage/app/private/scribe/collection.json`
- **OpenAPI Spec** — Auto-generated di `storage/app/private/scribe/openapi.yaml`

Untuk regenerate dokumentasi setelah ada perubahan kode:
```bash
php artisan scribe:generate
```

---

## 👤 Akun Default

Setelah menjalankan `php artisan migrate:fresh --seed`:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@parfumstore.com` | `password` |
| **Customer** | `customer@example.com` | `password` |

---

## 📂 Struktur Folder

```
UAS-WSA/
├── app/
│   ├── Http/Controllers/API/
│   │   ├── AuthController.php          # Register, Login, Logout
│   │   ├── CategoryController.php      # CRUD Kategori
│   │   ├── ProductController.php       # CRUD Produk + Upload
│   │   └── TransactionController.php   # CRUD Transaksi
│   └── Models/
│       ├── User.php                    # + HasApiTokens, hasMany Transaction
│       ├── Category.php                # hasMany Product
│       ├── Product.php                 # belongsTo Category, hasMany Transaction
│       └── Transaction.php             # belongsTo User, belongsTo Product
├── database/
│   ├── factories/
│   │   ├── CategoryFactory.php         # 5 kategori parfum
│   │   └── ProductFactory.php          # 36 nama parfum unik
│   ├── migrations/
│   │   ├── create_categories_table
│   │   ├── create_products_table
│   │   └── create_transactions_table
│   └── seeders/
│       └── DatabaseSeeder.php          # Admin + Kategori + Produk + Transaksi
├── routes/
│   └── api.php                         # Semua API routes
├── config/
│   └── scribe.php                      # Konfigurasi Scribe
├── storage/app/public/products/        # Folder upload gambar produk
└── .env                                # Konfigurasi environment
```

---

## 🔧 Perintah Artisan Penting

```bash
# Jalankan server development
php artisan serve

# Reset database & isi data dummy
php artisan migrate:fresh --seed

# Generate dokumentasi API
php artisan scribe:generate

# Buat symbolic link storage
php artisan storage:link

# Lihat daftar semua route
php artisan route:list

# Masuk ke Laravel Tinker (debugging)
php artisan tinker
```

---

## 📝 Catatan Tambahan

- **File Upload:** Gambar produk disimpan di `storage/app/public/products/`. Validasi format: `jpeg, png, jpg, gif` (maks 2MB).
- **Eager Loading:** Semua endpoint produk menggunakan `Product::with('category')` sehingga response JSON menyertakan data kategori secara nested.
- **Stock Management:** Saat transaksi dibuat, stok produk otomatis berkurang. Saat transaksi dibatalkan/dihapus, stok dikembalikan.
- **Pagination Default:** 10 item per halaman. Bisa diubah via query parameter `?per_page=`.

---

**Dibuat untuk Proyek Akhir UAS — Web Service & API** 🎓
