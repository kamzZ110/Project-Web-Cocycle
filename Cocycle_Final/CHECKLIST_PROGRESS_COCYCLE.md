# Checklist penyelesaian CoCycle Laravel

## Pemulihan error MySQL XAMPP — 3 November 2026

- [x] Log mengonfirmasi InnoDB `ERROR 1932` pada semua tabel CoCycle; file `.frm/.ibd` masih ada tetapi tidak terdaftar pada data dictionary aktif. `ibdata1` saat ini tidak cocok dengan tablespace. Tidak menjalankan repair di tempat.
- [x] Server dimatikan normal dan seluruh `C:\xampp\mysql\data` dicadangkan utuh sebagai `C:\xampp\mysql\data_recovery_20261103`.
- [x] Database pemulihan terpisah dibuat dari schema dump + migration, lalu 21 `.ibd` dipulihkan dari salinan data melalui import tablespace; 23 index dan 10 foreign key dibuat ulang. Tidak menimpa `cocycle2`.
- [x] Verifikasi data pemulihan: 8 user, 3 toko, 21 produk, 8 pesanan, 17 detail pesanan, 4 transaksi saldo; 9 migration berstatus Ran, 23 secondary index, dan 10 foreign key. `CHECK TABLE` untuk users/stores/products/orders/order_items/messages/wallet_transactions berstatus OK. Nomor auto-increment untuk tabel inti diselaraskan melewati ID maksimum yang ada.
- [x] `.env` lokal Laravel dialihkan ke `cocycle2_recovered_20261103`. Halaman publik memberi HTTP 200; route customer/partner yang perlu login mengembalikan redirect login (bukan 500). MySQL kembali `mysqld is alive`.
- [x] Database `cocycle2` rusak dan backup lengkap tetap disimpan. Jangan impor dump lama atau mengarahkan `.env` kembali ke `cocycle2` pada komputer ini.
- [x] Tidak mencoba login atau mengganti kata sandi akun asli selama verifikasi pemulihan. Password hash lama ikut dipertahankan dari file tablespace; user dapat memakai kredensial yang sama.

## Verifikasi lanjutan 3 November 2026

- [x] Pemeriksaan penamaan final: semua 25 file dalam `Cocycle_Final/resources/views` berakhiran `.blade.php`; tidak ada file view aktif dengan ekstensi lain. `php artisan view:cache` berhasil mengompilasi Blade.
- [x] Status siap: checklist tidak memiliki item terbuka; tes terakhir 16/207 lulus, semua 9 migration berstatus Ran, kompilasi Blade dan build Vite berhasil.
- [x] Membaca ulang checklist dan kode terbaru; perubahan saldo, penarikan, serta sesi dari pekerjaan 2 November dipertahankan.
- [x] MySQL awalnya mati (koneksi ditolak). Menyalakan kembali MySQL XAMPP tanpa impor, penghapusan, atau penyiapan ulang database utama.
- [x] Memperbarui migration hanya di database uji yang sudah ada `cocycle_test_20261101_114855_2822dc`; tidak membuat database tambahan. Skrip `tools/migrate-browser-test.php` menolak nama database di luar awalan uji.
- [x] Browser memverifikasi upload foto customer pada lebar 390 px, logo partner, dan gambar produk; file tersimpan dan gambar tampil lagi setelah navigasi/reload, termasuk pada katalog customer.
- [x] Menemukan kegagalan CDN Bootstrap/jQuery di halaman partner. Semua referensi JavaScript Bootstrap dan jQuery pada Blade kini memakai file lokal dengan versi yang sama. CSS, font, ikon, dan Chart.js masih menggunakan CDN.
- [x] Memperbaiki cabang Blade `Ready` ganda pada detail pesanan agar tombol pembatalan yang didukung backend tampil. Tes memeriksa tombol dan pengembalian stok sekali setelah pembatalan Ready.
- [x] Regresi Laravel: 16 tes / 207 assertion lulus pada SQLite in-memory; termasuk penarikan melebihi saldo dan pembatalan pengajuan dua kali. Kompilasi Blade dan build frontend berhasil.
- [x] Uji browser lengkap lulus tanpa error JavaScript: pesanan Rp10.000 dikreditkan Rp9.000 sekali; pengajuan Rp4.000 menyisakan Rp5.000; pembatalan mengembalikan saldo Rp9.000. Layout halaman penarikan tidak overflow pada lebar 390 px. Bukti: `Cocycle_Final/storage/testing/uploads-wallet-results.json` (`passed: true`).
- [x] README diperbarui dengan cara mengulang pengujian pada database yang sama serta batas dependensi CDN. Server uji dihentikan setelah pemeriksaan; MySQL tetap aktif agar aplikasi dapat dijalankan.

Bagian bertanggal di bawah adalah riwayat pemeriksaan; angka jumlah migration/tes/data lama tidak menggantikan hasil terbaru di atas.

## Perbaikan saldo partner dan pengajuan penarikan

- [x] Penyebab saldo nol teridentifikasi: status pesanan Completed hanya menyimpan status/waktu; tidak ada proses yang menambahkan hasil penjualan ke `users.balance`.
- [x] Aturan komisi disamakan dengan detail pesanan versi sebelum framework: biaya aplikasi 10%, pendapatan partner 90%. Analisis partner juga memakai 10% (sebelumnya keliru memakai 5%).
- [x] Saldo dikreditkan satu kali saat pesanan menjadi Completed. Rekonsiliasi idempoten juga memasukkan pesanan selesai lama yang belum punya transaksi saldo.
- [x] Halaman tarik dana menampilkan saldo tersedia dan riwayat transaksi nyata, serta menerima pengajuan dengan detail bank/e-wallet. Pengajuan menahan saldo; partner dapat membatalkan permintaan tertunda untuk mengembalikan saldo.
- [x] Migration `2026_11_01_000003_create_partner_wallet_transactions.php` berhasil dijalankan pada database aktif `cocycle2`; migration ini hanya menambah tabel transaksi saldo.
- [x] Batasan diverifikasi dan ditampilkan di UI: belum ada payment gateway/panel admin; permintaan tercatat pending dan transfer harus diproses manual.
- [x] Verifikasi alur terisolasi: 16 tes / 194 assertion lulus di SQLite `:memory:`, termasuk saldo 90%, kredit satu kali, rekonsiliasi pesanan selesai lama, pengajuan penarikan, penahanan saldo, dan pengembalian saldo saat dibatalkan. Database `cocycle2` tidak dipakai oleh tes.
- [x] User mengonfirmasi aplikasi berjalan baik di browser setelah perbaikan sesi/role pada 2 November 2026. Pengujian akun nyata dilakukan pengguna; tidak membuat transaksi tambahan pada `cocycle2`.

## Perbaikan sesi dan Forbidden lintas role

- [x] Sesi idle diperpanjang dari 120 menjadi 480 menit melalui `.env` dan `.env.example`.
- [x] Jika tab customer/partner membuka halaman role lain, akses tetap ditolak tetapi browser diarahkan ke dashboard role yang sedang login dengan pesan; update yang salah role tidak dijalankan.
- [x] Token CSRF/form yang basi kini menghasilkan pesan sesi kedaluwarsa dan mengarah ke login, tanpa mengirim ulang form lama.
- [x] Tes fitur terisolasi lulus setelah perubahan: 16 tes / 198 assertion pada SQLite `:memory:`; data `cocycle2` tidak dipakai tes.
- [x] Tidak ada server yang sedang listen di port 8000 saat pemeriksaan; server berikutnya akan membaca konfigurasi `.env` terbaru ketika dinyalakan.
- [x] User mengonfirmasi alur browser sudah berjalan baik pada 2 November 2026 setelah sesi idle diperpanjang dan respons role mismatch diperjelas. Satu sesi browser tetap hanya boleh aktif untuk satu role pada satu waktu.

## Revisi terbaru: keranjang, produk ganda, dan pesanan

- [x] Memeriksa database aktif: aplikasi memakai `cocycle2`. Produk awal yang sama ada di dua toko partner karena proses inisialisasi menyalin tujuh produk untuk setiap toko; produk tiap toko tetap disimpan agar pesanan dapat dikaitkan ke pemilik yang benar.
- [x] Marketplace mengelompokkan penawaran yang identik dalam satu kartu dan meminta customer memilih toko jika tersedia lebih dari satu. Produk yang berbeda pada nama, kategori, harga, gambar, atau deskripsi tetap ditampilkan terpisah. Toko baru tetap mendapat produk awal.
- [x] Keranjang menampilkan nama toko; setelah checkout pesan keberhasilan menyebut toko tujuan. Pesanan baru disimpan pada toko yang dipilih dan hanya terlihat oleh partner pemiliknya.
- [x] Halaman pesanan partner menampilkan username jika nama lengkap customer kosong, bukan selalu “Guest”.
- [x] Ketika sesi aktif adalah partner dan membuka GET `/cart`, aplikasi mengarahkan ke halaman partner dengan penjelasan dan tombol untuk ganti akun. Permintaan checkout lintas peran tetap ditolak dengan pesan yang menjelaskan sesi aktif; hak akses tidak dibuka.
- [x] Item lama dalam keranjang yang merujuk toko tanpa pemilik partner diberi penjelasan agar customer menghapus item itu dan memilih produk serta toko lagi. Tidak menghapus item otomatis.
- [x] Browser dengan akun dan database uji yang sudah ada memverifikasi kartu terkumpul, pilihan toko, checkout, pesanan tiba pada partner pilihan, dan pesan lintas peran. Tidak membuat database pengujian baru.
- [x] PHPUnit menggunakan SQLite `:memory:`: 15 tes / 175 assertion lulus. Build Vite berhasil.
- [x] Identifikasi sembilan database `cocycle_test_...` sebagai database pengujian yang dibuat skrip `tools/setup-browser-test.php`; sebagian berisi data uji. Semuanya dipertahankan bersama `cocycle2` dan database sistem.
- [x] Pesanan historis tetap dipertahankan dan tidak digandakan/dipindahkan karena pemilik partnernya tidak terbukti. Snapshot SQL memuat tiga pesanan pada toko 1 milik akun customer; audit database aktif sebelumnya mencatat empat, jadi jumlah dapat berbeda antara dump dan database berjalan. Satu pesanan baru yang ada saat audit tercatat pada toko partner 3.
- [x] Pengguna mengonfirmasi alur browser berjalan baik setelah perbaikan Forbidden pada 2 November 2026. Jika error muncul lagi, catat URL dan tindakan pemicu untuk diagnosis spesifik.

## Revisi lanjutan: homepage, Forbidden, dan pesanan partner

- [x] Carousel homepage customer dibuat seragam: tiga gambar 400 px pada lebar 1440 px dan 220 px pada lebar 390 px; ketiganya dimuat dan diuji perpindahannya di browser.
- [x] Penyebab Forbidden yang berhasil direproduksi diperbaiki: tujuan login tersimpan dari peran berbeda menyebabkan 403. Tes browser kedua arah dan tujuan se-peran lulus; akses lintas peran tetap 403. Catatan: belum diketahui apakah semua kejadian pengguna berasal dari penyebab sama; jika berulang, perlu alamat halaman/tindakan pemicunya.
- [x] Audit awal: seluruh 7 produk dan 4 pesanan menunjuk toko 1 milik akun customer `firkam`; akun `zptr` mempunyai toko 2 kosong dan `tokodonut` belum mempunyai toko. Versi lama memakai fallback toko pertama untuk pesanan sehingga relasi yang salah tersamarkan.
- [x] Sesuai arahan lanjutan pengguna, setiap partner baru mendapat toko sendiri dengan 7 produk awal dari snapshot dump. Partner lama bertoko kosong diinisialisasi sekali; katalog yang sudah berisi produk/pesanan tidak ditimpa. Penanda persisten mencegah duplikasi dan pengembalian produk yang sudah dihapus.
- [x] Database utama: `zptr` toko 2 dan `tokodonut` toko 3 masing-masing 7 produk. Total 21 produk/3 toko; 8 akun, 7 produk asli, 4 pesanan, 8 detail pesanan, dan data kepemilikan toko asli tidak berubah. Penyiapan dijalankan ulang: jumlah tetap, semua baris asli sama.
- [x] Katalog hanya menawarkan produk dari toko aktif milik partner. Produk toko lama milik customer tetap tersimpan tetapi tidak bisa dibeli, termasuk jika sudah ada di keranjang. Checkout baru diuji masuk ke partner pemilik produk; partner lain mendapat 404 untuk detail pesanan tersebut.
- [x] Uji browser menemukan jQuery CDN gagal dimuat (`$ is not defined`). Layout katalog/keranjang kini menggunakan jQuery 3.6.0 lokal. Uji ulang registrasi partner, katalog mobile, tambah keranjang, checkout, penerimaan pesanan, dan isolasi antarpartner lulus tanpa error JavaScript.
- [x] Audit Blade terakhir: 25 template aktif sudah `.blade.php`; tidak ditemukan mysqli, session_start, atau superglobal PHP dalam view aktif. Salinan login lama yang tidak dirujuk dipertahankan di `Cocycle_Final/docs/legacy-reference/login.php.php.txt`. Folder sebelum framework tidak diubah.
- [x] Pemeriksaan akhir: 14 tes / 158 assertion lulus di SQLite terisolasi, kompilasi Blade dan build Vite berhasil, 8 migration Ran. README diperbarui dengan penyiapan produk aman dan penjelasan Blade.
- [x] Migration baru juga lulus pada MySQL kosong (`cocycle_test_20261101_115350_cb2b56`), sintaks service/perintah PHP valid, dan pemeriksaan editor tidak menemukan masalah pada file yang diperiksa.

Hasil browser revisi produk: `Cocycle_Final/storage/testing/starter-catalog-results.json`. Pengujian menggunakan database terpisah `cocycle_test_20261101_114855_2822dc`, bukan akun/transaksi asli. Harga dan stok awal salinan mengikuti dump, statistik penjualan/tayangan dimulai nol; rating bawaan tetap data contoh. Empat pesanan historis tetap di toko 1 sampai pemilik partnernya dapat dibuktikan. Item keranjang dari toko lama perlu dihapus oleh customer lalu dipilih kembali dari katalog terbaru.

Analisis acuan: endpoint riwayat partner versi sebelum framework memakai toko pertama sebagai fallback demo jika partner tidak mempunyai toko. Ini tidak membuktikan kepemilikan toko tersebut dan tidak dipakai sebagai alasan untuk memindahkan produk atau pesanan ke akun yang belum dipastikan.

Pembaruan: 1 November 2026. Status di bawah didasarkan pada source, database, tes aplikasi, dan smoke test browser yang dijalankan pada tanggal ini.

## Lingkungan dan database

- [x] Baca analisis migrasi dan checklist lama sebelum mengubah implementasi.
- [x] Verifikasi PHP 8.4.0, Composer 2.8.12, Laravel 12.43.1, Node 24.22.0, npm 11.19.1, dan koneksi MySQL.
- [x] Audit database `cocycle2` sebelum migration: 6 pengguna, 1 toko, 7 produk, 3 pesanan, 6 detail pesanan; belum ada tabel `messages`; tabel donasi/komunitas/item donasi belum berisi data.
- [x] Buat migration aman untuk instalasi kosong dan skema impor: melengkapi users/tabel lama yang belum ada, menambah `messages`, `password_reset_tokens`, dan `orders.stock_reserved` tanpa menghapus tabel atau baris yang sudah ada.
- [x] Uji migration pada schema baru dan clone schema impor, lalu jalankan pada `cocycle2`. Semua 7 migration berstatus Ran.
- [x] Tidak menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, DROP, seeder demo, atau impor ulang ke database berisi data.
- [x] Data hitungan pengguna, toko, produk, pesanan, dan detail pesanan tetap sama setelah migration.

## Fitur dan alur

- [x] Registrasi/login customer dan partner memakai route Laravel; role diperiksa dan partner memperoleh toko baru yang terikat pada akun itu sendiri.
- [x] Tidak memindahkan toko lama yang terhubung ke customer tanpa bukti identitas pemilik. Partner lama tanpa toko memperoleh toko miliknya sendiri saat alur partner dipakai.
- [x] Profil customer/partner, pemetaan `store_name`, validasi nama/telepon, dan penyimpanan logo toko diperbaiki.
- [x] Katalog/pencarian, gambar produk, tambah/hapus/ubah jumlah keranjang, harga dari database, stok, checkout per toko, dan pembatasan akses kepemilikan diperbaiki.
- [x] Partner dapat membuat, mengubah, dan menghapus produk (produk bersejarah dinonaktifkan agar referensi transaksi tetap ada); validasi harga, stok, kategori, status, dan gambar diterapkan.
- [x] Partner dapat melihat detail pesanan yang terkait tokonya dan mengubah status sesuai transisi. Waktu selesai/batal dicatat; stok yang dipesan oleh checkout baru dikembalikan sekali saat batal.
- [x] Migration dan fitur chat tersedia; pencarian kontak, kirim, terima, dan isolasi pesan antarperan diuji.
- [x] Donasi memakai item/biaya dari database, validasi jumlah dan komunitas, keranjang session, checkout transaksional, pengurangan stok, pencatatan riwayat dan dampak.
- [x] Reset password memakai token password broker Laravel; tes memastikan token hanya dapat dipakai sekali.
- [x] Rujukan frontend yang ditemukan ke file PHP lama dialihkan ke route Laravel; pencarian lanjutan tidak menemukan link/form/fetch PHP lama pada Blade dan JS frontend.
- [x] JavaScript landing diperbaiki; aset gambar fallback, CSS keranjang/donasi, struktur halaman partner, dan overflow mobile yang ditemukan diperbaiki.

## Pemeriksaan yang lulus

- [x] `php artisan test --compact`: 11 tes, 127 assertion lulus pada SQLite `:memory:`.
- [x] `php artisan view:cache` berhasil.
- [x] `npm run build` berhasil dengan Vite 7.3.7.
- [x] Browser smoke test pada 21 route, desktop 1440 px dan mobile 390 px: seluruh 42 respons HTTP 200, tanpa error JavaScript, gambar rusak, atau overflow horizontal.
- [x] Browser membandingkan route dan screenshot dengan salinan versi lama; versi lama memiliki overflow mobile pada katalog dan beberapa halaman partner yang sudah diperbaiki pada Laravel.
- [x] Alur browser pada database MySQL uji baru: registrasi sesi customer, tambah produk, ubah kuantitas, checkout, checkout donasi dan impact, pencarian/kirim chat, pembuatan produk partner, status pesanan Preparing → Ready → Completed, serta penerimaan pesan oleh partner.
- [x] Akun, transaksi, dan stok uji hanya dibuat pada database baru berawalan `cocycle_test_`; database tersebut tidak dihapus otomatis.

## Batasan yang mengikuti source lama

- Checkout toko dan donasi belum terhubung ke payment gateway. Status pembayaran donasi dan ringkasan transaksi merupakan simulasi lokal.
- Tombol Tarik versi lama hanya tampilan contoh. Versi Laravel kini mencatat pendapatan pesanan dan permintaan penarikan lokal; transfer uang nyata dan panel pencairan tetap belum tersedia.
- Balasan otomatis chat lama adalah dummy. Chat sekarang menyimpan pesan yang benar-benar dikirim dan tidak membuat balasan palsu.
- Tabel item/komunitas/donasi utama kosong saat audit; tidak mengarang atau mengisi data awal. Komunitas yang diizinkan dibuat saat transaksi donasi pertamanya.
- Reset kata sandi memerlukan SMTP yang dikonfigurasi pada `.env`; uji otomatis memalsukan notifikasi dan tidak menguji pengiriman email eksternal.
- Upload foto profil/logo/produk telah diuji lewat browser pada 3 November 2026 menggunakan akun dan database uji; SMTP/reset email eksternal tetap belum diverifikasi.
- Nilai harga yang sudah tersimpan tidak diubah oleh perbaikan kode.

## Menjalankan proyek

- [x] README Windows menjelaskan persyaratan, konfigurasi `.env`, skema baru/impor, migration aman, build, server, reset password, fitur simulasi, dan cara menjalankan ulang tanpa perintah yang menghapus data.
- [x] README secara tegas melarang import ulang, seeder demo, `migrate:fresh`, `migrate:refresh`, dan `db:wipe` pada database berisi data.

Rujukan: [ANALISIS_MIGRASI_COCYCLE.md](./ANALISIS_MIGRASI_COCYCLE.md) dan [README.md](./Cocycle_Final/README.md).
