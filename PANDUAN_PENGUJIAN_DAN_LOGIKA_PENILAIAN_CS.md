# Panduan Komprehensif: Logika Perhitungan & Langkah Pengujian Sistem Penilaian CS
**Probus System Implementator 2026**

Dokumen ini disusun sebagai panduan praktis dan mudah dipahami bagi atasan (*Supervisor* / *Admin*), pengembang (*developer*), dan tim QA untuk memahami **cara kerja logika penilaian**, **simulasi perhitungan**, serta **panduan langkah demi langkah dalam melakukan pengujian (*testing*) sistem**.

---

## Daftar Isi
1. [Mengapa Sistem Penilaian Dibuat Seperti Ini?](#1-mengapa-sistem-penilaian-dibuat-seperti-ini)
2. [Bedah Formula Perhitungan Nilai (Scoring Logic)](#2-bedah-formula-perhitungan-nilai-scoring-logic)
   - [Pilar 1: Nilai Tiket (Bobot 30%)](#pilar-1-nilai-tiket-bobot-30)
   - [Pilar 2: Nilai Soft Skill (Bobot 40%)](#pilar-2-nilai-soft-skill-bobot-40)
   - [Pilar 3: Nilai Kepuasan Klien (Bobot 30%)](#pilar-3-nilai-kepuasan-klien-bobot-30)
   - [Kalkulasi Nilai Akhir & Predikat](#kalkulasi-nilai-akhir--predikat)
3. [Simulasi Nyata Contoh Perhitungan (Studi Kasus Jaden)](#3-simulasi-nyata-contoh-perhitungan-studi-kasus-jaden)
4. [Skenario & Langkah Pengujian Sistem (Step-by-Step QA Guide)](#4-skenario--langkah-pengujian-sistem-step-by-step-qa-guide)
   - [Skenario 1: Verifikasi Hak Akses (Admin vs Supervisor)](#skenario-1-verifikasi-hak-akses-admin-vs-supervisor)
   - [Skenario 2: Filter & Pemilihan Periode Bulan (Cut-Off Akhir Bulan)](#skenario-2-filter--pemilihan-periode-bulan-cut-off-akhir-bulan)
   - [Skenario 3: Penilaian Kinerja CS di Form Evaluasi](#skenario-3-penilaian-kinerja-cs-di-form-evaluasi)
   - [Skenario 4: Pengujian Simpan DRAFT vs FINAL](#skenario-4-pengujian-simpan-draft-vs-final)
   - [Skenario 5: Pengujian Nilai Ulang (updateOrCreate)](#skenario-5-pengujian-nilai-ulang-updateorcreate)
   - [Skenario 6: Pengujian Rapor Digital Web](#skenario-6-pengujian-rapor-digital-web)
   - [Skenario 7: Pengujian Ekspor / Cetak Dokumen PDF 1 Halaman A4](#skenario-7-pengujian-ekspor--cetak-dokumen-pdf-1-halaman-a4)
   - [Skenario 8: Pengujian Top 3 CS Spotlight](#skenario-8-pengujian-top-3-cs-spotlight)
5. [Tabel Matriks Checklist Pengujian](#5-tabel-matriks-checklist-pengujian)
6. [FAQ & Hal-Hal yang Sering Membingungkan](#6-faq--hal-hal-yang-sering-membingungkan)

---

## 1. Mengapa Sistem Penilaian Dibuat Seperti Ini?

### Kelemahan Sistem Lama
Pada sistem konvensional, performa staf Customer Support hanya diukur dari **kuantitas tiket** (misalnya: *"Siapa yang menyelesaikan tiket paling banyak, dialah yang terbaik"*). Ini menimbulkan ketidakadilan nyata:
- CS yang menyelesaikan **50 tiket gampang** (misal: *reset password* atau modul umum) akan terlihat jauh lebih rajin daripada CS yang menyelesaikan **10 tiket akuntansi rumit** (misal: rekonsiliasi jurnal *balance* / modul *Front Desk*).
- Kualitas komunikasi, kesopanan (*soft skill*), dan kepuasan klien yang sebenarnya sering kali terabaikan.

### Solusi Sistem Penilaian Probus 2026
Sistem ini menggunakan pendekatan **3 Pilar Berimbang**:
1. **Tingkat Kesulitan Modul (Bobot $\times$ Qty)**: Tiket modul kompleks memiliki poin lebih besar dibanding modul ringan.
2. **Benchmarking Relatif Tim**: Menghindari target angka kaku. CS dengan poin modul tertinggi di bulan tersebut otomatis menjadi tolok ukur (nilai 100), dan CS lain dinilai secara proporsional terhadap capaian rekan terbaiknya.
3. **Keseimbangan Kuantitas & Kualitas**: Tiket hanya berbobot 30%, sementara Soft Skill (40%) dan Kepuasan Klien (30%) memegang porsi terbesar (70%) demi menjamin layanan berkualitas prima.

---

## 2. Bedah Formula Perhitungan Nilai (Scoring Logic)

Komposisi total nilai 100% dibagi menjadi 3 pilar:

$$\mathbf{\text{Nilai Akhir}} = (\mathbf{\text{Nilai Tiket}} \times 30\%) + (\mathbf{\text{Nilai Soft Skill}} \times 40\%) + (\mathbf{\text{Kepuasan Klien}} \times 30\%)$$

```
┌────────────────────────────────────────────────────────────────────────┐
│                      KOMPOSISI NILAI AKHIR (100%)                      │
├───────────────────────┬────────────────────────┬───────────────────────┤
│   Nilai Tiket (30%)   │ Nilai Soft Skill (40%) │ Kepuasan Klien (30%)  │
│  (Produktivitas &     │ (Komunikasi, Sikap,    │  (Rating Bintang CSAT │
│   Tingkat Kesulitan)  │  Kecepatan, Kerjasama) │   Ulasan Pelanggan)   │
└───────────────────────┴────────────────────────┴───────────────────────┘
```

---

### Pilar 1: Nilai Tiket (Bobot 30%)

#### Langkah 1.1: Menghitung Skor Mentah per Modul
Setiap modul di sistem Probus memiliki **bobot kesulitan** (antara 50 sampai 90):
- Modul *Accounting* / *Back Office*: Bobot **90** (Paling Kompleks)
- Modul *Front Desk* / *Reservasi*: Bobot **85**
- Modul *Point of Sales (POS)*: Bobot **80**
- Modul *Inventory* / *Purchasing*: Bobot **75**
- Modul *General* / *Setup*: Bobot **60**

$$\text{Skor Baris Modul} = \text{Bobot Modul} \times \text{Jumlah Tiket Selesai}$$

#### Langkah 1.2: Menghitung Total Skor Mentah CS (`raw_ticket_score`)
Total seluruh poin modul yang diselesaikan oleh CS tersebut di bulan berjalan:
$$\text{Raw Ticket Score} = \sum (\text{Bobot Modul} \times \text{Jumlah Tiket Selesai})$$

#### Langkah 1.3: Normalisasi Relatif terhadap Tim (`ticket_score`) (Skala 0–100)
Sistem mencari siapa CS yang meraih poin tertinggi di tim pada bulan berjalan (`max_ticket_score`):
$$\text{Nilai Tiket (0--100)} = \left(\frac{\text{Raw Ticket Score CS}}{\text{Max Raw Score Tim}}\right) \times 100$$
> *Catatan*: CS dengan performa tertinggi di bulan tersebut otomatis memperoleh nilai **100**.

#### Langkah 1.4: Porsi Kontribusi Tim (%) (`team_portion`)
Mengetahui seberapa besar persentase beban kerja tim yang dipikul oleh CS ini:
$$\text{Porsi Kontribusi Tim} = \left(\frac{\text{Raw Ticket Score CS}}{\sum \text{Total Raw Score Seluruh Tim}}\right) \times 100\%$$

---

### Pilar 2: Nilai Soft Skill (Bobot 40%)

Atasan mengevaluasi 5 aspek penting dengan skala bulat **1 sampai 5 bintang**:
1. **Komunikasi**: Kemampuan menjelaskan solusi secara ramah, jelas, dan santun.
2. **Kualitas Solusi**: Ketepatan akar masalah dan solusi yang tuntas tanpa komplain berulang.
3. **Kecepatan Respon**: Ketanggapan merespon chat atau tiket masuk klien.
4. **Sikap & Profesionalisme**: Etika kerja, kesabaran menangani klien sulit, dan disiplin.
5. **Kerjasama Tim (Teamwork)**: Kerelaan membantu mendampingi atau alih tiket (*takeover*) rekan CS lain.

Total skor maksimal adalah: $5 \text{ aspek} \times 5 \text{ poin} = 25 \text{ poin}$.

Dikonversi ke skala 0–100:
$$\text{Nilai Soft Skill (0--100)} = \left(\frac{\text{Skor 1} + \text{Skor 2} + \text{Skor 3} + \text{Skor 4} + \text{Skor 5}}{25}\right) \times 100$$

---

### Pilar 3: Nilai Kepuasan Klien (Bobot 30%)

Diambil dari rating kepuasan langsung yang diberikan klien setelah tiket dinyatakan selesai (*closed*).
- Skala input: **1.0 s/d 5.0 bintang**.
- Dikonversi ke skala 0–100:
$$\text{Nilai Kepuasan Klien (0--100)} = \left(\frac{\text{Rating Bintang}}{5.0}\right) \times 100$$

---

### Kalkulasi Nilai Akhir & Predikat

$$\text{Nilai Akhir} = (\text{Nilai Tiket} \times 0.30) + (\text{Nilai Soft Skill} \times 0.40) + (\text{Nilai Kepuasan} \times 0.30)$$

#### Kategori Predikat:
| Rentang Nilai Akhir | Predikat Kinerja | Warna Badge | Keterangan & Tindak Lanjut |
| :---: | :---: | :---: | :--- |
| **80.00 – 100.00** | **Sangat Baik** | 🟢 Hijau Emerald | Sangat memuaskan, menjadi teladan tim, layak apresiasi/bonus. |
| **70.00 – 79.99** | **Baik** | 🟡 Kuning Amber | Memenuhi target standar operasional CS Probus. |
| **< 70.00** | **Cukup** | 🔴 Merah Rose | Di bawah target standar, memerlukan pembinaan atau pelatihan modul. |

---

## 3. Simulasi Nyata Contoh Perhitungan (Studi Kasus Jaden)

Berikut simulasi perhitungan riil akun **Jaden** untuk periode **Oktober 2026**:

### 1. Data Tiket Modul Jaden:
- Modul *Accounting* (Bobot 90): 3 tiket $= 3 \times 90 = 270$
- Modul *Front Desk* (Bobot 85): 4 tiket $= 4 \times 85 = 340$
- Modul *POS* (Bobot 80): 3 tiket $= 3 \times 80 = 240$
- Modul *General* (Bobot 60): 3 tiket $= 3 \times 60 = 180$
- **Total Raw Ticket Score Jaden** $= 270 + 340 + 240 + 180 = \mathbf{1.030 \text{ poin}}$.
- Poin tertinggi tim di bulan Oktober kebetulan adalah Jaden sendiri ($1.030$).
- Total poin tiket yang ditangani seluruh tim $= 3.100 \text{ poin}$.

*Hasil Pilar 1*:
- $\text{Nilai Tiket} = \left(\frac{1.030}{1.030}\right) \times 100 = \mathbf{100.00}$
- $\text{Porsi Kontribusi Tim} = \left(\frac{1.030}{3.100}\right) \times 100\% = \mathbf{33.2\%}$

### 2. Evaluasi Soft Skill Jaden:
Atasan memberikan nilai:
- Komunikasi: 5
- Kualitas Solusi: 4
- Kecepatan: 5
- Sikap: 5
- Kerjasama: 4
- Total Skor $= 5 + 4 + 5 + 5 + 4 = 23 \text{ poin}$.

*Hasil Pilar 2*:
- $\text{Nilai Soft Skill} = \left(\frac{23}{25}\right) \times 100 = \mathbf{92.00}$

### 3. Kepuasan Klien (CSAT):
- Rata-rata bintang ulasan tiket Jaden: **4.8 bintang** (dari 5.0).

*Hasil Pilar 3*:
- $\text{Nilai Kepuasan} = \left(\frac{4.8}{5.0}\right) \times 100 = \mathbf{96.00}$

### 4. Perhitungan Nilai Akhir:
- Porsi Tiket (30%): $100.00 \times 30\% = \mathbf{30.00}$
- Porsi Soft Skill (40%): $92.00 \times 40\% = \mathbf{36.80}$
- Porsi Kepuasan (30%): $96.00 \times 30\% = \mathbf{28.80}$
- **Total Nilai Akhir**: $30.00 + 36.80 + 28.80 = \mathbf{95.60}$
- **Predikat**: 🟢 **Sangat Baik**

---

## 4. Skenario & Langkah Pengujian Sistem (Step-by-Step QA Guide)

Berikut adalah panduan langkah demi langkah saat Anda menguji sistem di browser:

### Skenario 1: Verifikasi Hak Akses (Admin vs Supervisor)
1. **Login sebagai Supervisor (contoh: Dewi)**:
   - Akses [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login).
   - Perhatikan menu sidebar di sebelah kiri:
     - Menu **Laporan** harus muncul.
     - Menu **Master Data Modul** harus muncul.
     - Di bawah menu *Advanced Settings*, menu **User List** sekarang **harus muncul** dan dapat dibuka tanpa pesan *403 Unauthorized*.
2. **Login sebagai User CS biasa (contoh: Jaden)**:
   - Menu **Laporan** dan menu **User List** tidak boleh terlihat di sidebar.
   - Jika CS mencoba mengetik URL `/laporan` di address bar, sistem otomatis mengalihkan (*redirect*) ke Dashboard dengan pesan peringatan akses ditolak.

---

### Skenario 2: Filter & Pemilihan Periode Bulan (Cut-Off Akhir Bulan)
1. Buka halaman **Laporan** (`/laporan`).
2. Perhatikan bagian kanan atas (di samping judul halaman):
   - Terdapat dropdown periode: `[ 📅 Oktober 2026 (Bulan Berjalan) ⌄ ]`.
   - Di bawah judul terdapat label: `Cut-off Akhir Bulan: 31 Oktober 2026`.
3. **Uji Coba Ganti Bulan**:
   - Klik dropdown dan ubah ke **September 2026**.
   - Halaman otomatis me-refresh dan memuat data tiket/laporan periode September 2026.
   - Label otomatis berubah menjadi `Cut-off Akhir Bulan: 30 September 2026`.

---

### Skenario 3: Penilaian Kinerja CS di Form Evaluasi
1. Pada tabel daftar CS di halaman `/laporan`, cari CS yang berstatus **"Belum Dinilai"** (badge kuning).
2. Klik tombol biru **"Nilai"**.
3. Sistem akan membuka halaman Form Penilaian (`/penilaian/{id}?month=XX&year=YYYY`).
4. **Verifikasi Komponen Form**:
   - **Kartu Kiri Atas**: Menampilkan tiket ditangani, tiket selesai, rata-rata respon time, dan persentase penyelesaian.
   - **Tabel Modul**: Menampilkan rincian tiket per modul yang dikerjakan CS pada periode tersebut beserta perkalian `Bobot x Qty`.
   - **Kotak Skor Tiket Ternormalisasi**: Menampilkan skor 0–100 dan % porsi kontribusi tim secara otomatis.
   - **Input Nilai Soft Skill**: Klik radio button bintang 1 s/d 5 untuk masing-masing kriteria. Perhatikan nilai akhir yang dihitung secara *live* (*real-time reactive*) tanpa perlu me-refresh halaman!

---

### Skenario 4: Pengujian Simpan DRAFT vs FINAL
1. Isi form evaluasi dan ketik catatan serta saran untuk CS.
2. **Uji Tombol "Simpan Draft"**:
   - Klik tombol **Simpan Draft**.
   - Sistem kembali ke `/laporan` dengan notifikasi sukses.
   - Status CS tersebut tetap dapat diedit kembali kapan saja.
3. **Uji Tombol "Simpan Nilai" (FINAL)**:
   - Klik tombol **Simpan Nilai**.
   - Sistem otomatis mengalihkan (*redirect*) langsung ke halaman **Rapor Digital CS** (`/rapor/{id}`).

---

### Skenario 5: Pengujian Nilai Ulang (updateOrCreate)
1. Kembali ke halaman `/laporan`.
2. CS yang sudah dinilai sekarang berstatus **"Sudah Dinilai"** (badge hijau) dengan nilai akhir dan predikat terpampang.
3. Klik ikon **Pensil (Nilai Ulang)** pada baris CS tersebut.
4. Form penilaian akan terbuka dengan nilai-nilai yang sebelumnya telah disimpan (tidak reset ke kosong).
5. Ubah salah satu nilai soft skill atau catatan, lalu klik **Simpan Nilai**.
6. **Verifikasi Database**:
   - Data diperbarui langsung pada baris yang sama tanpa membuat duplikasi baris baru (*no redundant rows*).

---

### Skenario 6: Pengujian Rapor Digital Web
1. Pada halaman `/laporan`, klik tombol **"Lihat Rapor"** pada CS yang sudah dinilai.
2. Halaman Rapor Digital (`/rapor/{id}`) terbuka dengan tampilan elegan:
   - Header memuat foto, nama CS, jabatan, serta periode evaluasi yang sesuai.
   - 4 Kartu Metrik atas: Nilai Tiket, Nilai Soft Skill, Kepuasan Klien, dan Nilai Akhir dengan lencana predikat.
   - Tabel rincian pencapaian modul dan perolehan bintang soft skill.
   - Kolom catatan & saran dari atasan yang telah tersimpan.

---

### Skenario 7: Pengujian Ekspor / Cetak Dokumen PDF 1 Halaman A4
1. Pada halaman Rapor Digital, klik tombol unduh (ikon download) di pojok kanan atas, atau akses URL `/rapor/{id}/download`.
2. Browser akan mengunduh atau membuka file PDF resmi.
3. **Poin Penting Pemeriksaan PDF**:
   - **Presisi 1 Lembar**: Pastikan dokumen PDF **tepat 1 halaman A4 Portrait** (tidak ada halaman 2 kosong yang terbuang).
   - **Speedometer Arc Gauge SVG**: Pastikan indikator busur setengah lingkaran di kartu kanan bawah ter-render dengan tajam dan jarum/nilainya berada di tengah.
   - **Kerapian Tipografi**: Logo, tabel modul, dan tanda tangan digital tertata rapi tanpa teks yang terpotong.

---

### Skenario 8: Pengujian Top 3 CS Spotlight
1. Buka halaman `/laporan`.
2. Di atas tabel data, perhatikan 3 kartu podium penghargaan:
   - **Best Performance** (Emas/Amber): Diberikan kepada CS dengan total skor kinerja tertinggi.
   - **Client's Favorite** (Rose/Pink): Diberikan kepada CS dengan tingkat kepuasan pelanggan (CSAT) tertinggi.
   - **Best Team Player** (Indigo/Biru): Diberikan kepada CS yang paling aktif mendampingi tiket rekan tim (*advice / takeover*).
3. **Pemeriksaan Visual**:
   - Foto CS tampil besar (`w-20 h-20` / 80px) dalam bingkai kotak modern (`rounded-2xl`) berbingkai warna tema.
   - Tidak ada tombol aksi yang canggung/salah tempat di bawah kartu; fokus kartu murni sebagai trofi penghargaan.

---

## 5. Tabel Matriks Checklist Pengujian

Gunakan tabel ini saat melakukan pengujian formal sebelum rilis (*acceptance test*):

| No | Komponen Pengujian | Langkah Aksi | Hasil yang Diharapkan (Expected Result) | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | **Akses Supervisor ke User List** | Login sebagai Supervisor, buka sidebar | Menu *User List* muncul di sidebar dan halaman `/user` dapat dibuka normal tanpa error 403 | [ ] |
| 2 | **Dropdown Periode Laporan** | Pilih bulan lain pada dropdown pojok kanan atas `/laporan` | Data laporan & cut-off akhir bulan berubah sesuai bulan yang dipilih | [ ] |
| 3 | **Perkalian Modul Tiket** | Buka form `/penilaian/{id}` untuk CS yang memiliki tiket | Kolom Nilai Akhir Modul menampilkan hasil kali: $\text{Bobot} \times \text{Qty}$ | [ ] |
| 4 | **Benchmark Tim** | Bandingkan skor tiket CS tertinggi dan CS lainnya | CS poin tertinggi mendapat Nilai Tiket 100, CS lain mendapat proporsional | [ ] |
| 5 | **Perhitungan Real-Time** | Klik pilihan radio bintang 1 s/d 5 di form | Skor soft skill dan estimasi Nilai Akhir langsung berubah seketika | [ ] |
| 6 | **Penyimpanan Draft** | Klik tombol *Simpan Draft* | Status tersimpan sebagai `DRAFT`, data tidak hilang saat dibuka lagi | [ ] |
| 7 | **Penyimpanan Final** | Klik tombol *Simpan Nilai* | Data berstatus `FINAL`, otomatis redirect ke halaman Rapor Digital | [ ] |
| 8 | **Fitur Nilai Ulang** | Klik ikon pensil pada CS yang sudah dinilai di `/laporan` | Form memuat data lama dan memperbarui data tanpa membuat duplikat di DB | [ ] |
| 9 | **Rapor Web Interaktif** | Buka halaman `/rapor/{id}` | Komposisi nilai 30%, 40%, 30% dan rincian modul tampil rapi | [ ] |
| 10 | **Cetak PDF 1 Lembar A4** | Buka atau unduh `/rapor/{id}/download` | Dokumen PDF pas 1 halaman A4 Portrait tanpa sisa halaman kedua kosong | [ ] |
| 11 | **Spotlight Top 3 CS** | Buka `/laporan` | Menampilkan 3 CS terbaik dengan foto kotak besar 80px dan bingkai bertema | [ ] |

---

## 6. FAQ & Hal-Hal yang Sering Membingungkan

### Q1: Mengapa CS dengan tiket sedikit bisa memperoleh skor tiket 100?
> **Jawaban**: Sistem menggunakan metode **relatif tim (benchmarking)**, bukan kuota mutlak. Jika pada bulan tersebut seluruh CS lain hanya menyelesaikan tiket modul bernilai rendah, maka CS dengan total poin bobot tertinggi di tim otomatis menjadi standar tertinggi (skor 100). Anggota tim lainnya akan dinilai secara adil terhadap standar pencapaian rekan terbaiknya di bulan yang sama.

### Q2: Bagaimana jika seorang CS sama sekali tidak memiliki tiket di bulan berjalan?
> **Jawaban**: Form penilaian tetap dapat diisi. Skor tiket CS tersebut otomatis bernilai **0**, namun atasan tetap dapat mengevaluasi aspek *Soft Skill* (maksimal 40 poin) dan *Kepuasan Pelanggan* (maksimal 30 poin).

### Q3: Apakah atasan boleh menilai periode bulan yang sudah lewat?
> **Jawaban**: **Bisa**. Karena laporan evaluasi kinerja sering kali diadakan di akhir bulan atau awal bulan berikutnya, atasan cukup memilih bulan yang dimaksud (misalnya *September 2026*) pada dropdown filter di pojok kanan atas, lalu klik tombol **Nilai**. Data tiket yang ditarik dan rapor yang tersimpan akan otomatis terkunci pada periode bulan tersebut.

### Q4: Di mana atasan bisa menyesuaikan bobot kesulitan sebuah modul?
> **Jawaban**: Supervisor dan Admin dapat membuka menu **Master Data Modul** pada sidebar ticketing (`/modul`). Di halaman tersebut, atasan dapat mengubah nilai bobot (*weight*) modul (misalnya menaikkan bobot modul POS dari 80 menjadi 85 jika modul tersebut baru saja merilis fitur yang lebih kompleks).

### Q5: Mengapa cetak PDF didesain tidak menggunakan flexbox atau CSS modern?
> **Jawaban**: Library renderer PDF berbasis PHP (*DomPDF*) memiliki keterbatasan parsing CSS modern. Menggunakan Flexbox atau CSS Grid pada DomPDF sering menyebabkan elemen berantakan atau teks meluber (*overflow*) ke halaman kedua. Oleh karena itu, template PDF menggunakan struktur **Tabel HTML Murni (`<table>`, `<tr>`, `<td>`)** yang menjamin dokumen ter-render presisi tepat 1 lembar A4 di semua sistem operasi dan printer.

---
*Dokumen panduan ini aktif dan tersinkronisasi pada sistem Probus Implementator 2026.*
