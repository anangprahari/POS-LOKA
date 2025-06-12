<p align="center">
    <h1 align="center">POS System KOPI LOKA</h1>
</p>

Sistem Point of Sale (POS) khusus untuk produk kopi KOPI LOKA yang dibangun menggunakan Laravel 10 dengan integrasi React untuk pengalaman pengguna yang modern dan responsif.

## 🚀 Fitur Utama

- **Manajemen Produk Kopi** - Kelola berbagai varian kopi KOPI LOKA
- **Sistem Kasir** - Interface yang mudah digunakan untuk transaksi
- **Manajemen Pelanggan** - Database pelanggan dan riwayat pembelian
- **Laporan Penjualan** - Analytics dan reporting lengkap
- **Multi Payment** - Dukungan berbagai metode pembayaran
- **Responsive Design** - Dapat diakses dari berbagai perangkat

## 📋 Requirements

Untuk menjalankan sistem ini, pastikan server Anda memenuhi [Laravel 10 Requirements](https://laravel.com/docs/10.x/deployment#server-requirements):

- PHP >= 8.1    
- Composer
- Node.js & NPM
- MySQL
- Web Server (Apache/Nginx)

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/anangprahari/POS-LOKA.git
cd POS-LOKA
```

### 2. Install Dependencies

Install PHP dependencies menggunakan Composer:

```bash
composer install
```

### 3. Environment Configuration

Buat file konfigurasi environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Database Setup

Atur konfigurasi database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_loka
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Atur juga `APP_URL` sesuai dengan domain Anda:

```env
APP_URL=http://localhost:8000
```

### 5. Database Migration & Seeding

Jalankan migrasi database:

```bash
php artisan migrate
```

Jalankan seeder untuk data awal:

```bash
php artisan db:seed
```

Seeder akan membuat:
- Admin user default: `anangpraf04@gmail.com` / `anangpraf123`
- Data produk kopi KOPI LOKA
- Pengaturan sistem dasar

### 6. Install Node Dependencies

Install dependencies untuk React dan asset compilation:

```bash
npm install
```

Untuk development:
```bash
npm run dev
```

Untuk production:
```bash
npm run build
```

### 7. Storage Link

Buat symbolic link untuk storage:

```bash
php artisan storage:link
```

### 8. Jalankan Server

Start Laravel development server:

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

**Login Credentials:**
- Email: `anangpraf04@gmail.com`
- Password: `anangpraf123`


## 🏗️ Struktur Teknologi

- **Backend**: Laravel 10
- **Frontend**: React.js
- **Database**: MySQL
- **Styling**: Bootstrap
- **Package Manager**: Composer (PHP) & NPM (Node.js)

## 📂 Struktur Project

```
POS-LOKA/
├── app/                    # Laravel application logic
├── database/               # Migrations, seeders, factories
├── public/                 # Public assets
├── resources/              # Views, React components, CSS
├── routes/                 # Application routes
├── storage/                # File storage
└── README.md              # This file
```

## 🔧 Development

Untuk development mode:

```bash
# Start Laravel server
php artisan serve

# Watch for changes (in another terminal)
npm run dev
```

## 🚀 Production Deployment

1. Atur environment variables untuk production
2. Run asset compilation: `npm run build`
3. Optimize Laravel: `php artisan optimize`
4. Set proper permissions untuk storage dan bootstrap/cache directories

## 🤝 Contributing

Kontribusi sangat diterima! Silakan buat issue atau pull request untuk perbaikan dan fitur baru.

## 📞 Support

Jika mengalami masalah atau memiliki pertanyaan:
- Email: anangpraf04@gmail.com
- GitHub Issues: [Create Issue](https://github.com/anangprahari/POS-LOKA/issues)

## 📄 License

Project ini menggunakan [MIT License](LICENSE).

---

<p align="center">
    <strong>Dibuat dengan ❤️ untuk KOPI LOKA</strong>
</p>
