# CoCycle Laravel

Aplikasi kampus CoCycle (Laravel 12). Antarmuka mempertahankan stylesheet dan aset versi PHP sebelum framework yang tersedia di `public/`.

## Persyaratan Windows

- Windows 10/11 64-bit.
- PHP 8.2 atau lebih baru (diuji pada PHP 8.4.0) dengan ekstensi `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, dan `curl`. Instalasi Herd Lite yang tersedia dapat dipakai.
- Composer 2.x.
- Node.js 20.19+ atau 22.12+ dan npm.
- MySQL 8+ atau MariaDB versi yang kompatibel.
- Akun email SMTP hanya diperlukan untuk mengirim tautan reset kata sandi.

Jalankan perintah dari PowerShell pada folder proyek:

```powershell
cd 'D:\Cocyle web\Cocycle_Final'
php -v
composer --version
node --version
npm --version
```

Pastikan `php` menggunakan PHP 8.2+. Jika Herd Lite tidak ada di PATH, panggil executable secara langsung:

```powershell
& 'C:\Users\Lenovo\.config\herd-lite\bin\php.exe' artisan --version
```

## Menyiapkan konfigurasi

1. Salin `.env.example` ke `.env` hanya bila `.env` belum ada. Pertahankan `.env` lokal yang sudah berisi konfigurasi:

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

2. Atur `APP_URL`, `APP_KEY`, `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file lokal tersebut. Jangan commit atau membagikan `.env`.
3. Buat database MySQL kosong dengan nama yang dipilih. Jangan gunakan nama database produksi atau database yang berisi data lain untuk mengikuti instruksi database baru.
4. Instal dependency bila belum terpasang:

```powershell
composer install
npm ci
```

Bila `APP_KEY` kosong pada instalasi baru, jalankan `php artisan key:generate` sekali. Jangan membuat ulang key pada instalasi yang sudah berjalan karena dapat membatalkan cookie/session terenkripsi.

## Skema dan data

### Catatan pemulihan lokal XAMPP (3 November 2026)

Pada komputer pengembangan ini, folder data XAMPP sebelumnya mengalami ketidakcocokan InnoDB (`ERROR 1932`). Salinan lengkap folder saat itu disimpan di `C:\xampp\mysql\data_recovery_20261103`. Seluruh 21 tablespace CoCycle berhasil dipulihkan ke database baru `cocycle2_recovered_20261103`; akun, toko, produk, pesanan, detail pesanan, saldo, index, dan relasi kembali terbaca. Konfigurasi `.env` lokal sekarang menunjuk database hasil pemulihan tersebut. Database `cocycle2` yang rusak tetap ada sebagai sumber pemulihan dan jangan dihapus/ditimpa. Password tidak direset; kredensial yang dipakai sebelumnya tetap berlaku.

**Jangan impor `cocycle2.sql` ke database pemulihan atau mengganti `DB_DATABASE` ke `cocycle2` pada komputer ini.** Dump itu lebih lama daripada data folder aktif. Untuk menjalankan, hidupkan MySQL dari XAMPP Control Panel, pastikan `mysqladmin ping` menjawab `mysqld is alive`, lalu jalankan Laravel seperti biasa. Jika memakai instalasi ini setelah menyalin proyek, pertahankan `.env` lokal yang mengarah ke database pemulihan.

Database baru untuk komputer lain tetap dapat disiapkan dengan prosedur impor dump pada bagian berikut; itu menghasilkan snapshot yang lebih lama dan tidak menggantikan database pemulihan komputer ini.

Migration aman dijalankan pada instalasi baru maupun skema bisnis hasil impor dump:

```powershell
php artisan migrate --force
php artisan migrate:status
```

Untuk menjalankan CoCycle dengan katalog kampus dari dump, impor `cocycle2.sql` hanya ke database baru yang masih kosong, menggunakan MySQL Workbench atau klien MySQL. Atur `.env` ke nama database tersebut, kemudian jalankan `php artisan migrate --force` untuk membuat tabel Laravel, messages, reset kata sandi, dan penanda stok reservasi. Periksa nama database aktif sebelum impor.

Untuk database yang telah berisi pengguna/transaksi, cukup jalankan migration biasa. Jangan impor dump ke sana. Jangan jalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, menghapus tabel, atau `db:seed` sebagai langkah start-up. Migrasi pembuatan messages yang telah berjalan akan membuat chat dapat dipakai; pesan lama tidak dapat dipulihkan karena dump memang tidak menyertakan tabel messages.

Seeder/demo data tidak diperlukan. Jangan sediakan route publik seeder. Pesanan atau barang uji harus dibuat pada database uji tersendiri.

## Menjalankan aplikasi

Buat bundle frontend:

```powershell
npm run build
```

Jalankan server pada terminal PowerShell:

```powershell
php artisan serve
```

Buka [http://127.0.0.1:8000](http://127.0.0.1:8000). Untuk pengembangan dengan hot reload, jalankan `npm run dev` pada terminal kedua. Situs harus disajikan dari `public/` melalui `artisan serve` atau konfigurasi virtual host web server yang setara; jangan sajikan root repository.

## Akun, email, dan alur yang disimulasikan

Daftar customer atau partner melalui landing page. Partner mendaftarkan nama toko; toko baru terkait langsung hanya kepada akun tersebut. Login menggunakan email dan kata sandi. Jalur reset password menggunakan password broker Laravel dan memerlukan konfigurasi SMTP; aplikasi tidak menampilkan OTP di layar.

Checkout toko mencatat pesanan, mengurangi stok, dan mengelompokkan keranjang per toko. Pembatalan hanya diizinkan sebelum pesanan selesai dan mengembalikan stok yang memang dicadangkan oleh checkout baru.

Donasi mencatat komunitas, nilai biaya penanganan item, kuantitas, dan dampak yang dihitung oleh prototipe. Pembayaran donasi adalah **simulasi lokal**, bukan debit e-wallet/transfer. Pendapatan partner masuk ke saldo sebesar 90% saat pesanan selesai (potongan aplikasi 10%). Pengajuan penarikan dicatat sebagai pending dan menahan saldo sampai dibatalkan atau diproses manual; tidak ada transfer otomatis karena payment gateway dan panel admin pencairan belum terhubung. Chat menyimpan pesan yang benar-benar dikirim antar akun, tetapi balasan otomatis acak pada versi lama adalah dummy dan tidak dijalankan.

## Produk awal partner dan database lama

Setiap registrasi partner mendapat toko sendiri dan tujuh produk awal dari katalog dump lama. Akun partner lama yang tokonya kosong mendapat produk awal saat membuka halaman partner. Harga, gambar, deskripsi, rating contoh, dan stok awal berasal dari snapshot [starter-products.json](database/data/starter-products.json); statistik penjualan/tayangan dimulai dari nol. Ini data awal untuk demonstrasi kampus, bukan persediaan atau penilaian pelanggan nyata.

Di marketplace, penawaran yang identik dari beberapa toko ditampilkan dalam satu kartu. Pilih toko sebelum menambahkan produk ke keranjang; nama toko ditampilkan pada keranjang dan pesan checkout. Pesanan hanya masuk ke partner pemilik toko yang dipilih. Empat pesanan lama yang pemilik toko partnernya belum diketahui tetap disimpan pada toko lama; pesanan tersebut tidak diduplikasi ke partner lain.

Tab dalam satu browser berbagi sesi login. Gunakan profil browser terpisah atau jendela privat untuk menggunakan akun customer dan partner bersamaan. Jika membuka keranjang ketika sesi sedang memakai akun partner, halaman akan menjelaskan cara berganti akun.

Sesi lokal bertahan hingga 8 jam tanpa aktivitas. Jika halaman dibuka memakai akun dengan role berbeda, aplikasi mengarahkan ke dashboard akun aktif dan memberi penjelasan; perubahan tidak dijalankan. Jika token form kedaluwarsa, login akan menampilkan pesan untuk masuk kembali dan membuka ulang halaman. Setelah mengubah `SESSION_LIFETIME` di `.env`, restart server Laravel.

Setelah memperbarui kode, jalankan `php artisan migrate` sebelum membuka aplikasi. Untuk menyiapkan semua partner lama sekaligus, jalankan `php artisan cocycle:starter-products`. Perintah ini hanya mengisi toko kosong yang belum pernah diinisialisasi dan belum mempunyai pesanan. Katalog yang sudah ada tidak ditimpa; login ulang tidak menggandakan produk atau mengembalikan produk yang telah dihapus.

Katalog customer hanya menawarkan produk dari toko aktif yang pemiliknya partner. Produk lama di toko milik customer tetap tersimpan, tetapi tidak dapat dibeli; hapus item tersebut dari keranjang dan pilih produk dari katalog terbaru. Checkout mencatat toko produk yang dipilih. Empat pesanan lama di toko 1 tidak dipindahkan karena pemilik partnernya belum dapat dipastikan.

## Template Blade untuk tugas kampus

Seluruh 25 template aktif di [resources/views](resources/views) menggunakan `.blade.php`, termasuk login/register, customer, katalog, keranjang, donasi, dan partner. Route/controller memanggil view Blade; akses database dan sesi berada di backend Laravel. Beberapa halaman memakai layout Blade dan beberapa mempertahankan dokumen mandiri untuk menjaga tampilan lama.


## Menjalankan pemeriksaan aplikasi

Pengujian aplikasi memakai SQLite in-memory agar tidak menyentuh MySQL lokal:

```powershell
php artisan test --compact
php artisan view:cache
npm run build
```

Gunakan server dan akun uji/database khusus untuk pengujian browser. Hindari memakai kata sandi akun asli. Folder kerja sementara browser di `storage/testing/` tidak berisi database pengguna asli; database uji MySQL memiliki awalan `cocycle_test_` dan dapat dihapus manual setelah dipastikan tidak dipakai. Jangan menjalankan pembersihan otomatis terhadap database.

Untuk melanjutkan pengujian pada database uji yang sudah tersedia di `storage/testing/runtime.json`:

```powershell
php tools/migrate-browser-test.php
php -S 127.0.0.1:8011 -t public tools/test-router.php
```

Pada terminal kedua jalankan `node tools/check-uploads-wallet.mjs`. Skrip membuat akun/file/produk/transaksi sementara di database uji tersebut dan menulis hasil ke `storage/testing/uploads-wallet-results.json`. Tidak membuat database baru, tidak memakai akun utama, dan tidak membersihkan data otomatis. Hentikan server uji dengan Ctrl+C setelah selesai. Skrip memerlukan Microsoft Edge dan dependensi Playwright yang sudah terpasang.

Bootstrap JavaScript dan jQuery dimuat dari `public/vendor` agar interaksi tidak tergantung CDN. Versi dan komentar lisensi aslinya dipertahankan. CSS Bootstrap, ikon, font, dan grafik Chart.js masih membutuhkan akses internet.

## Menjalankan lagi setelah penyiapan

Untuk penggunaan berikutnya, cukup hidupkan MySQL, dari folder ini jalankan `php artisan serve` (dan `npm run dev` bila sedang mengubah frontend), lalu buka URL server. Migrasi hanya dijalankan sesudah memeriksa target database saat memperbarui versi. Tidak perlu mengimpor ulang dump, membuat ulang APP_KEY, seeding, atau menjalankan perintah yang menghapus data.
