# Aplikasi Manajemen Kos

Aplikasi web untuk mengelola data penghuni, kamar, barang, tagihan, dan pembayaran pada sebuah kos. Dirancang untuk kebutuhan administrasi kos secara efisien dan mudah digunakan oleh admin.

---

## Fitur Utama

- **CRUD Data Master:** Penghuni, Kamar, Barang
- **Manajemen Hunian:** Penempatan penghuni ke kamar
- **Manajemen Barang Bawaan:** Barang milik penghuni
- **Generate & Kelola Tagihan:** Otomatis di awal bulan, sesuai harga kamar + barang bawaan
- **Manajemen Pembayaran:** Input dan monitoring pembayaran tagihan
- **Dashboard & Monitoring:** Kamar kosong, kamar yang akan/telat bayar, rekap tagihan & pembayaran
- **Landing Page Publik:** Untuk monitoring, bukan aksi

---

## Struktur Folder

```
app_kost/
│
├── index.php                # Landing page publik
├── admin/                   # Halaman admin (dashboard, CRUD, transaksi)
├── inc/                     # File koneksi database & fungsi umum
├── per_database_an/         # Kumpulan file SQL untuk database
│   ├── app_kost.sql
│   ├── tabeldatabase.sql
│   ├── contoh_database.sql
│   └── reset_database.sql
└── ...
```

---

## Instalasi & Cara Menjalankan

### 1. Persiapan
- Pastikan sudah terinstall **XAMPP/Laragon** atau web server dengan PHP & MySQL/MariaDB.
- Clone/copy seluruh folder `app_kost` ke direktori `htdocs` (XAMPP) atau `www` (Laragon).

### 2. Setup Database

#### Pilih salah satu opsi berikut:

#### **A. Impor Database Lengkap (dengan data contoh)**
- File: `per_database_an/app_kost.sql`
- **Kelebihan:** Sudah berisi struktur tabel + data contoh (penghuni, kamar, barang, tagihan, pembayaran, dsb).
- **Langkah:**
  1. Buka phpMyAdmin.
  2. Buat database baru, misal: `app_kost`.
  3. Import file `app_kost.sql` ke database tersebut.

#### **B. Impor Hanya Struktur Tabel (tanpa data)**
- File: `per_database_an/tabeldatabase.sql`
- **Kelebihan:** Hanya berisi struktur tabel, cocok untuk mulai dari data kosong.
- **Langkah:**
  1. Buka phpMyAdmin.
  2. Buat database baru, misal: `app_kost`.
  3. Import file `tabeldatabase.sql` ke database tersebut.

#### **C. (Opsional) Tambah Data Contoh Saja**
- Setelah impor struktur, bisa tambahkan data contoh dengan `contoh_database.sql`.

#### **D. (Opsional) Reset Data**
- Untuk menghapus seluruh data dan reset auto-increment, gunakan `reset_database.sql`.

---

### 3. Konfigurasi Koneksi Database
- Edit file `inc/db.php` jika perlu menyesuaikan nama database, user, atau password.

```php
// Contoh isi db.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "app_kost";
$conn = mysqli_connect($host, $user, $pass, $db);
```

---

### 4. Menjalankan Aplikasi
- Akses melalui browser:  
  `http://localhost/app_kost/` (landing page publik)  
  `http://localhost/app_kost/admin/` (dashboard admin)

---

## Penggunaan CSS & JS

- **Bootstrap** digunakan melalui CDN di seluruh halaman utama untuk tampilan modern & responsif.
- Tidak ada folder `assets` karena seluruh style/script diambil dari CDN.

---

## Daftar File SQL di Folder `per_database_an/`

| File                  | Isi/Tujuan                                                                 |
|-----------------------|----------------------------------------------------------------------------|
| app_kost.sql          | Struktur + data contoh lengkap (langsung siap pakai)                       |
| tabeldatabase.sql     | Hanya struktur tabel (tanpa data)                                          |
| contoh_database.sql   | Data contoh saja (bisa diimpor setelah struktur)                           |
| reset_database.sql    | Script untuk menghapus seluruh data & reset auto-increment (bukan truncate)|

---

## Catatan Penting

- **Tidak ada login user/tenant** (hanya admin, sesuai soal).
- **Landing page** hanya untuk monitoring, bukan aksi.
- **Tagihan** otomatis digenerate di awal bulan, sesuai harga kamar + barang bawaan.
- **Reset data** gunakan `reset_database.sql` (bukan truncate, agar aman dari foreign key error).

---

## Kontribusi & Pengembangan

- Untuk pengembangan lebih lanjut (misal: fitur login, notifikasi, dsb), struktur sudah siap dikembangkan.

---

Aplikasi ini dibuat untuk kebutuhan pembelajaran dan tugas akhir.  
Silakan gunakan, modifikasi, dan kembangkan sesuai kebutuhan.

---
