# 📋 Ringkasan Perkembangan Proyek (13 Oktober 2026)

Dokumen ini merangkum seluruh pekerjaan, pembaruan fitur, dan perbaikan bug yang telah diselesaikan hari ini pada repositori **Probus Implementator 2026**.

---

## 🌟 1. Ikhtisar Utama Hari Ini

Hari ini fokus pada penyempurnaan alur kerja, integrasi database, pembersihan bug krusial pada hak akses & data user, serta penataan sistem navigasi agar mudah dipahami oleh seluruh tim:

| No | Modul / Bagian | Status | Ringkasan Pekerjaan |
|---|---|---|---|
| 1 | **Database & Sinkronisasi** | Selesai | Backup database lokal & impor pembaruan database SQL dari tim. |
| 2 | **Navigasi Request System** | Selesai | Reorganisasi nama menu agar transparan: *Daftar Request*, *My Request (Antrean Masuk)*, dan *History (Pengajuan Saya)*. |
| 3 | **Keamanan Modul Pengaturan** | Selesai | Pembatasan akses modul pengaturan agar hanya dapat dibuka oleh **Admin** dan **Supervisor**. |
| 4 | **Bug Akun Pengguna (Tag User)** | Selesai | Memperbaiki bug di mana tag user selalu terkunci ke *PROGRAMMER* saat tambah/edit akun. |
| 5 | **Sistem Tiket & Inbox CS** | Selesai | Penambahan penanda tiket baru (**NEW**), pengaturan batas alih tiket dinamis, dan pencarian responsif. |

---

## 🛠️ 2. Detail Pekerjaan yang Dilakukan

### A. Sinkronisasi & Impor Database Lokal
* Dilakukan backup database aktif terlebih dahulu ke `backup_sebelum_import_13okt.sql`.
* Impor pembaruan database `.sql` dari tim untuk memastikan skema tabel, relasi, dan data modul terbaru sinkron dengan lingkungan kerja lokal.

---

### B. Penataan & Klarifikasi Menu "Request System"
Sebelumnya terdapat kerancuan arti antara menu *Request Board*, *My Request*, dan *History*. Hari ini struktur menu distandarisasi dengan format dua baris di sidebar:

1. **Daftar Request** *(Sub-label: Semua Request)*
   * **Route:** `/request` (`request.board`)
   * **Fungsi:** Menampilkan seluruh daftar request pengerjaan sistem pelanggan secara global (lintas divisi).
2. **My Request** *(Sub-label: Antrean Masuk)*
   * **Route:** `/myreq` (`myreq`)
   * **Fungsi:** Menampilkan antrean pengerjaan khusus untuk divisi/tim user yang sedang login (`tag_id`), dilengkapi badge counter tugas aktif.
3. **History** *(Sub-label: Pengajuan Saya)*
   * **Route:** `/history` (`history`)
   * **Fungsi:** Menampilkan riwayat permohonan yang dibuat sendiri oleh user yang sedang login (`user_id`).
4. **Header & Judul Halaman**
   * Header atas otomatis menampilkan nama lengkap yang mudah dipahami:
     * *Daftar Request (Semua Request)*
     * *My Request (Antrean Masuk)*
     * *History (Pengajuan Saya)*

---

### C. Perbaikan Bug Hak Akses Modul Pengaturan
* **Masalah:** Menu dan fitur *Pengaturan* sempat bisa dilihat oleh akun dengan role umum (*user*).
* **Solusi:**
  * Memberikan proteksi `@can('aspv')` pada [sidebar.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/layouts/sidebar.blade.php) sehingga menu Pengaturan tersembunyi untuk role reguler.
  * Menambahkan middleware `can:aspv` pada rute pengaturan di [routes/web.php](file:///d:/Project_Magang/probus-implementator-2026/routes/web.php) agar halaman diblokir jika diakses via URL langsung.

---

### D. Perbaikan Bug Tag User pada Tambah & Edit Akun
* **Masalah:**
  Saat admin/supervisor membuat akun baru di halaman `Registeracc` dan memilih tag non-programmer (misal: *Trainer* atau *Support*), tag akun yang tersimpan di database tetap menjadi *PROGRAMMER*.
* **Penyebab:**
  Di model [app/Models/User.php](file:///d:/Project_Magang/probus-implementator-2026/app/Models/User.php), kolom `tag_id`, `usertype`, dan `departemen_id` belum didaftarkan di dalam `$fillable`. Akibatnya, Laravel Eloquent mengabaikan kolom tersebut saat proses `create()` / `update()`, dan MySQL otomatis memakai nilai default kolom yaitu `1` (*PROGRAMMER*).
* **Solusi:**
  1. Menambahkan `tag_id`, `usertype`, dan `departemen_id` ke dalam `$fillable` di [User.php](file:///d:/Project_Magang/probus-implementator-2026/app/Models/User.php).
  2. Menambahkan helper `old('tag')` dan `old('usertype')` pada form [registeracc.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tableuser/registeracc.blade.php) dan [editacc.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tableuser/editacc.blade.php) agar data input tidak hilang jika terjadi error validasi.

---

### E. Penyelarasan Sistem Tiket Helpdesk CS
* Menambahkan penanda badge **NEW** berkedip (*animate-pulse*) untuk tiket pelanggan yang baru masuk dan belum pernah diproses.
* Implementasi konfigurasi dinamis batas waktu penugasan tiket pelanggan sebelum dialihkan ke agen lain.
* Optimasi pencarian responsif pada halaman Inbox CS.

---

## 📂 3. Daftar File yang Diubah Hari Ini

| File | Keterangan Perubahan |
|---|---|
| [app/Models/User.php](file:///d:/Project_Magang/probus-implementator-2026/app/Models/User.php) | Menambahkan `usertype`, `tag_id`, `departemen_id` ke `$fillable`. |
| [resources/views/layouts/sidebar.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/layouts/sidebar.blade.php) | Tata letak 2 baris menu Request System + proteksi hak akses menu Pengaturan. |
| [resources/views/layouts/header.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/layouts/header.blade.php) | Penyelarasan judul dinamis halaman Request System. |
| [resources/views/tablemyreq/myreq.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablemyreq/myreq.blade.php) | Judul board: *My Request (Antrean Masuk)* vs *Daftar Request (Semua Request)*. |
| [resources/views/tablemyreq/myproc.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablemyreq/myproc.blade.php) | Tooltip navigasi kembali ke Antrean Masuk. |
| [resources/views/tablemyrequs/myrequs.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablemyrequs/myrequs.blade.php) | Judul riwayat: *History (Pengajuan Saya)*. |
| [resources/views/tablemyrequs/myrequest.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablemyrequs/myrequest.blade.php) | Judul dan tautan kembali ke *History (Pengajuan Saya)*. |
| [resources/views/tablemyrequs/myupdatesystem.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablemyrequs/myupdatesystem.blade.php) | Tautan kembali ke *History (Pengajuan Saya)*. |
| [resources/views/tablerq/mrq.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tablerq/mrq.blade.php) | Tautan kembali ke *Daftar Request (Semua Request)*. |
| [resources/views/dashboard_request.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/dashboard_request.blade.php) | Tombol pintas tabel diperbarui menjadi *Lihat Daftar Request*. |
| [resources/views/tableuser/registeracc.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tableuser/registeracc.blade.php) | Dukungan `old()` value pada tag dan user type. |
| [resources/views/tableuser/editacc.blade.php](file:///d:/Project_Magang/probus-implementator-2026/resources/views/tableuser/editacc.blade.php) | Dukungan `old()` value pada user type saat update data. |

---

## 🌿 4. Status Git & Remote Branch

* **Branch Aktif:** `feature/mobile-inbox-and-ticket-improvements`
* **Commit Terakhir:** `e9c70eb` (*feat: perbarui navigasi menu request system dan perbaiki tag user pada register/edit acc*)
* **Status Kerja:** Bersih (*Clean Working Tree*), semua perubahan lokal telah tersinkronisasi ke remote GitHub.
