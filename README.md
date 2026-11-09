# EtikaSosial

**Etika Bermedia Sosial yang Baik dan Benar**

Situs WordPress sederhana yang membahas etika bermedia sosial, privasi, dan literasi digital. Tampilan memakai identitas gelap dengan aksen emas, tanpa animasi atau transisi, serta seluruh CSS dan JavaScript dimuat dari berkas eksternal.

| Keterangan | Isi |
| --- | --- |
| Nama | **Fadil Anugrahadi** |
| NIM | **09010626010** |
| Topik | **Etika Bermedia Sosial** |
| Tema | EtikaSosial 1.2.1 |
| WordPress | 7.1.4 |
| PHP | 7.4 atau lebih baru |
| Database | MySQL 5.5.5+ / MariaDB |

---

## Tentang situs

EtikaSosial adalah ruang baca tentang cara berinteraksi, menjaga privasi, dan memahami informasi di internet. Situs memiliki halaman publik Beranda, Artikel, Tentang Saya, dan Kontak, serta halaman Masuk yang juga menyediakan pendaftaran akun.

### Fitur utama

- **Beranda** berupa halaman pengantar: hero, tiga kartu topik, panel ringkasan situs, tiga artikel terbaru, dan ajakan menghubungi penulis.
- **Artikel** berisi daftar lengkap artikel dengan kategori, tag, tanggal, dan gambar.
- **Tentang Saya** memuat profil penulis.
- **Kontak** menyediakan formulir yang pesannya tersimpan di panel WordPress.
- **Masuk dan Buat akun** dalam satu halaman di `/masuk/`.
- **Administrator dan Editor** dapat menambah atau mengedit artikel dan halaman melalui `wp-admin`.
- **Subscriber** hanya dapat membaca. Akun baru otomatis berperan Subscriber, diarahkan ke beranda, tidak melihat admin bar, dan tidak dapat membuka `wp-admin`.
- **Tanpa animasi atau transisi**, tanpa `<style>` inline, dan tanpa `<script>` inline.
- **Responsif** untuk layar ponsel, tablet, dan desktop.

---

## Cara menjalankan

### 1. Siapkan XAMPP

1. Pasang [XAMPP](https://www.apachefriends.org/) dengan PHP 7.4 atau lebih baru.
2. Buka **XAMPP Control Panel**, lalu jalankan **Apache** dan **MySQL**.

### 2. Unduh repositori

```bash
cd C:\xampp\htdocs
git clone https://github.com/fadil441/EtikaSosial.git EtikaSosial
```

Jika tidak memakai Git, unduh repositori sebagai ZIP lalu ekstrak ke `C:\xampp\htdocs\EtikaSosial`.

### 3. Buat database

1. Buka `http://localhost/phpmyadmin/`.
2. Buat database baru bernama `etikasosial_wp`.
3. Pilih collation `utf8mb4_general_ci`.

### 4. Buat konfigurasi WordPress

1. Salin `wp-config-sample.php` menjadi `wp-config.php`:

   ```bash
   copy wp-config-sample.php wp-config.php
   ```

2. Buka `wp-config.php`, lalu sesuaikan bagian database:

   ```php
   define( 'DB_NAME', 'etikasosial_wp' );
   define( 'DB_USER', 'root' );
   define( 'DB_PASSWORD', '' );
   define( 'DB_HOST', 'localhost' );
   ```

   Pada XAMPP bawaan, pengguna `root` biasanya tidak memakai kata sandi. Sesuaikan bila konfigurasi MySQL Anda berbeda.

### 5. Pasang WordPress

1. Buka `http://localhost/EtikaSosial/`.
2. Ikuti proses pemasangan: isi judul situs **EtikaSosial**, buat akun administrator, lalu selesaikan.
3. Masuk ke `http://localhost/EtikaSosial/wp-admin/`.

> Repositori ini tidak menyertakan dump database. Proses pemasangan WordPress akan membuat tabel yang diperlukan.

### 6. Aktifkan tema

1. Di `wp-admin`, buka **Appearance → Themes**.
2. Aktifkan tema **EtikaSosial**.
3. Saat tema aktif pertama kali, tema otomatis membuat:
   - halaman **Beranda**, **Artikel**, **Tentang Saya**, **Kontak**, dan **Masuk**;
   - menu utama dan menu footer;
   - tiga kategori dan delapan artikel contoh;
   - pengaturan beranda dan halaman daftar artikel.

### 7. Atur permalink

1. Buka **Settings → Permalinks**.
2. Pilih **Post name**, lalu klik **Save Changes**.
3. WordPress akan membuat berkas `.htaccess` yang diperlukan.

### 8. Buka situs

- Beranda: `http://localhost/EtikaSosial/`
- Artikel: `http://localhost/EtikaSosial/artikel/`
- Tentang Saya: `http://localhost/EtikaSosial/tentang-saya/`
- Kontak: `http://localhost/EtikaSosial/kontak/`
- Masuk / Buat akun: `http://localhost/EtikaSosial/masuk/`

---

## Gambar

Foto artikel berasal dari [Pexels](https://www.pexels.com/) dan digunakan sesuai [lisensi Pexels](https://www.pexels.com/license/). Tautan sumber setiap gambar disimpan sebagai metadata artikel dan ditampilkan di halaman artikel.

---

## Lisensi

Tema EtikaSosial dilisensikan di bawah GNU General Public License v2 atau lebih baru. WordPress juga menggunakan lisensi yang sama. Lihat `license.txt` untuk lisensi inti WordPress.

---

## Penulis

**Fadil Anugrahadi**
NIM 09010626010
GitHub: [@fadil441](https://github.com/fadil441)
