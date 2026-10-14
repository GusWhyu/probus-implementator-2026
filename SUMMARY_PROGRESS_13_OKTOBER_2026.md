# Ringkasan Pekerjaan Hari Ini (13 Oktober 2026)

Hari ini kita fokus menyelesaikan beberapa kebutuhan penting pada aplikasi Probus Implementator, mulai dari pembaruan database lokal, penataan ulang alur navigasi menu agar tidak membingungkan pengguna, pengamanan hak akses halaman pengaturan, hingga perbaikan bug akun pengguna dan penambahan fitur kecil pada sistem tiket.

---

### 1. Sinkronisasi Database Lokal
Pekerjaan kita awali dengan mengamankan data yang ada melalui proses backup database lokal ke dalam file `backup_sebelum_import_13okt.sql`. Setelah itu, kita mengimpor file SQL pembaruan dari rekan tim. Langkah ini dilakukan agar struktur tabel, data modul, serta relasi data di komputer lokal kita sinkron dan sama persis dengan versi database tim.

---

### 2. Penataan dan Perapian Menu Request System
Sebelumnya, penamaan menu di bagian Request System terasa membingungkan karena nama *Request Board*, *My Request*, dan *History* fungsinya belum terdefinisi dengan jelas. Hari ini kita merapikannya dengan format dua baris di sidebar:
* **Daftar Request** (dengan keterangan *Semua Request* di bawahnya): Halaman ini menampilkan seluruh papan kerja request pengerjaan sistem pelanggan secara global lintas divisi.
* **My Request** (dengan keterangan *Antrean Masuk* di bawahnya): Halaman ini berfungsi sebagai kotak masuk pengerjaan untuk divisi/tim user yang sedang login, lengkap dengan badge angka jika ada tugas aktif yang belum selesai.
* **History** (dengan keterangan *Pengajuan Saya* di bawahnya): Halaman ini khusus mencatat riwayat permohonan request yang diajukan sendiri oleh user tersebut.

Judul halaman dan header atas aplikasi kini juga otomatis menampilkan nama yang sama sehingga alur kerja terasa rapi dan tidak membingungkan pengguna lama maupun baru.

---

### 3. Pengamanan Hak Akses Modul Pengaturan
Kita juga memperbaiki celah akses pada modul Pengaturan (khususnya menu Penilaian Soft Skill). Sebelumnya, menu ini sempat bisa terlihat oleh pengguna biasa (*role user*). Kita telah menguncinya menggunakan izin khusus (`can:aspv`) sehingga modul ini sekarang benar-benar hanya muncul dan bisa diakses oleh akun dengan peran **Admin** atau **Supervisor**. Jika pengguna biasa mencoba masuk lewat tautan URL langsung, sistem akan otomatis menolak akses tersebut.

---

### 4. Perbaikan Bug Tag Pengguna pada Tambah & Edit Akun
Saat mencoba mendaftarkan akun baru di menu Pengguna, ditemukan masalah di mana tag pengguna selalu tersimpan sebagai *PROGRAMMER*, meskipun admin sudah memilih tag lain seperti *Trainer* atau *Support*. 

Masalah ini terjadi karena pada model `User.php`, kolom `tag_id`, `usertype`, dan `departemen_id` belum didaftarkan ke dalam daftar pengisian massal (*fillable*). Akibatnya, Laravel mengabaikan input yang dikirim dan MySQL otomatis mengisi nilai default yaitu `1` (*PROGRAMMER*). Kita sudah memperbaiki ini dengan mendaftarkan kolom-kolom tersebut ke model `User.php` serta melengkapi form input agar pilihan pengguna tidak hilang saat terjadi kesalahan validasi. Sekarang admin bebas menentukan tag dan role pengguna baru maupun saat mengedit akun.

---

### 5. Penyempurnaan pada Sistem Tiket Helpdesk CS
Selain sistem request, kita juga memberikan sentuhan pembaruan pada alur tiket pelanggan. Tiket-tiket yang baru masuk kini memiliki label penanda **NEW** dengan animasi berkedip lembut agar tim CS langsung tahu ada tiket baru yang belum disentuh. Kita juga mengaktifkan pengaturan batas waktu operasional alih tugas tiket secara fleksibel, serta merapikan kotak pencarian pada menu Inbox tiket agar nyaman digunakan di berbagai ukuran layar.

---

### Penutup
Seluruh perubahan di atas sudah diuji, cache aplikasi telah dibersihkan, dan semua kodenya sudah diunggah dengan aman ke GitHub pada branch `feature/mobile-inbox-and-ticket-improvements`.
