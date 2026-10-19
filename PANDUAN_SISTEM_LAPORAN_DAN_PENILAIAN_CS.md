# Panduan Lengkap Sistem Laporan & Penilaian Kinerja Customer Support (CS)
**Probus System Implementator 2026**

Dokumen ini merupakan panduan teknis dan operasional resmi mengenai modul **Laporan, Penilaian Kinerja CS, Rapor Digital, dan Cetak PDF**. Dokumen ini disusun agar tim pengembang (*developer*), *team lead*, dan manajemen dapat memahami seluruh alur kerja, logika perhitungan, skema database, serta aspek teknis yang diterapkan.

---

## Daftar Isi
1. [Ikhtisar & Tujuan Sistem](#1-ikhtisar--tujuan-sistem)
2. [Alur Kerja Sistem (End-to-End Workflow)](#2-alur-kerja-sistem-end-to-end-workflow)
3. [Panduan Antarmuka & Halaman](#3-panduan-antarmuka--halaman)
   - [A. Halaman Daftar Laporan CS (`/laporan`)](#a-halaman-daftar-laporan-cs-laporan)
   - [B. Form Penilaian Kinerja CS (`/penilaian/{id}`)](#b-form-penilaian-kinerja-cs-penilaianid)
   - [C. Halaman Rapor Digital Web (`/rapor/{id}`)](#c-halaman-rapor-digital-web-raporid)
   - [D. Dokumen Cetak / Download PDF (`/rapor/{id}/download`)](#d-dokumen-cetak--download-pdf-raporiddownload)
4. [Formula & Logika Perhitungan Nilai (Scoring Engine)](#4-formula--logika-perhitungan-nilai-scoring-engine)
   - [A. Nilai Tiket (Bobot 30%)](#a-nilai-tiket-bobot-30)
   - [B. Nilai Soft Skill (Bobot 40%)](#b-nilai-soft-skill-bobot-40)
   - [C. Nilai Kepuasan Klien (Bobot 30%)](#c-nilai-kepuasan-klien-bobot-30)
   - [D. Nilai Akhir & Predikat](#d-nilai-akhir--predikat)
   - [E. Simulasi Contoh Perhitungan Nyata](#e-simulasi-contoh-perhitungan-nyata)
5. [Arsitektur Database & Relasi Tabel](#5-arsitektur-database--relasi-tabel)
6. [Rekayasa Teknis & Kompatibilitas](#6-rekayasa-teknis--kompatibilitas)
7. [FAQ & Troubleshooting](#7-faq--troubleshooting)

---

## 1. Ikhtisar & Tujuan Sistem

Modul Penilaian Kinerja dirancang untuk mengukur performa staf Customer Support (CS) secara **objektif, adil, transparan, dan terukur**. 

Sistem ini tidak hanya melihat seberapa banyak tiket yang diselesaikan, namun juga memperhitungkan:
1. **Tingkat Kesulitan Modul Sistem**: Tiket modul kompleks (seperti *Accounting* atau *Front Desk*) bernilai lebih tinggi daripada modul sederhana.
2. **Benchmarking Terhadap Tim**: Produktivitas CS dinormalisasi terhadap capaian tertinggi di tim pada bulan yang sama.
3. **Kualitas Soft Skill**: Evaluasi sikap, kecepatan respon, ketelitian, dan kerjasama.
4. **Kepuasan Pelanggan (CSAT)**: Ulasan kepuasan langsung dari klien atas penyelesaian masalah.

---

## 2. Alur Kerja Sistem (End-to-End Workflow)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                               ALUR SISTEM                               │
└─────────────────────────────────────────────────────────────────────────┘

  [1. DAFTAR LAPORAN] (/laporan)
         │
         ├── CS Belum Dinilai ────> Klik "Nilai" ─────────────┐
         └── CS Sudah Dinilai ────> Klik "Lihat Rapor" ──┐   │
                                                         │   │
                                                         ▼   ▼
  [2. FORM PENILAIAN] (/penilaian/{id}) ◄────────────────┘
         │
         ├── Tarik otomatis tiket yang diselesaikan di periode berjalan
         ├── Hitung skor tiket per modul: (Bobot Modul × Jumlah Tiket)
         ├── Evaluasi Soft Skill (5 aspek skala 1–5)
         ├── Input rating kepuasan klien (1.0–5.0)
         └── Simpan sebagai "DRAFT" atau "FINAL"
                 │
                 ▼
  [3. RAPOR DIGITAL WEB] (/rapor/{id})
         │
         ├── Pratinjau interaktif di browser
         └── Tombol "Download PDF"
                 │
                 ▼
  [4. DOKUMEN RESMI PDF] (/rapor/{id}/download)
         └── File PDF A4 Portrait presisi 1 lembar (untuk arsip & evaluasi)
```

---

## 3. Panduan Antarmuka & Halaman

### A. Halaman Daftar Laporan CS (`/laporan`)
- **Route**: `route('laporan')`
- **Controller**: `HomeController@laporan`
- **View**: `resources/views/laporan.blade.php`
- **Fungsi Utama**:
  - Menampilkan seluruh pengguna dengan peran Customer Support.
  - Menampilkan avatar profil, nama lengkap, dan email.
  - Menampilkan **Periode Penilaian Terakhir** (misal: *Okt 2026*).
  - Menampilkan **Skor Akhir & Badge Predikat**:
    - Hijau: *Sangat Baik* (skor $\ge 80$)
    - Kuning: *Baik* (skor $70 - 79.99$)
    - Merah: *Cukup* (skor $< 70$)
  - Menampilkan **Status Penilaian**:
    - `Sudah Dinilai` (badge hijau)
    - `Belum Dinilai` (badge kuning)
  - **Aksi Cepat**:
    - Tombol **"Nilai"** (biru) jika belum dinilai.
    - Tombol **"Lihat Rapor"** dan ikon **Pensil (Nilai Ulang)** jika sudah dinilai.

---

### B. Form Penilaian Kinerja CS (`/penilaian/{id}`)
- **Route**: `route('penilaian', $id)`
- **Controller**: `HomeController@penilaian` (tampilan) & `HomeController@storePenilaian` (penyimpanan)
- **View**: `resources/views/penilaian.blade.php`
- **Komponen**:
  1. **Header & Periode**: Tombol kembali ke daftar dan dropdown filter periode bulan/tahun.
  2. **Profil Singkat**: Foto CS, nama, jabatan, serta indikator rating bintang biru interaktif.
  3. **2 Kartu Statistik Atas**:
     - *Ringkasan Tiket*: Menampilkan total tiket selesai, skor mentah CS, skor benchmark tim, dan % porsi kontribusi tim.
     - *Komposisi Bobot Nilai*: Pengingat rasio bobot (30% Tiket, 40% Soft Skill, 30% Kepuasan Klien).
  4. **Tabel Rincian Tiket per Modul**:
     - Menampilkan kolom: *Nama Modul*, *Beban Bobot*, *Jumlah Tiket*, dan *Nilai Akhir Modul*.
     - Baris total yang menjumlahkan seluruh tiket dan skor mentah.
     - Kotak Skor Tiket Ternormalisasi (skala 0–100) di pojok kanan bawah tabel.
  5. **Form Soft Skill (5 Aspek)**:
     - Radio button pilihan nilai bulat: `1`, `2`, `3`, `4`, `5`.
     - 5 Aspek: *Komunikasi*, *Kecepatan Penanganan*, *Ketelitian*, *Kerjasama Tim*, *Sikap & Profesionalisme*.
  6. **Catatan & Saran**: Dua kotak teks untuk evaluasi kualitatif dan arahan perbaikan.
  7. **Tombol Simpan**:
     - **Simpan Draft**: Menyimpan tanpa mempublikasikan rapor final.
     - **Simpan Nilai**: Menyimpan evaluasi berstatus `FINAL`.

---

### C. Halaman Rapor Digital Web (`/rapor/{id}`)
- **Route**: `route('rapor', $id)`
- **Controller**: `HomeController@rapor`
- **View**: `resources/views/rapor.blade.php`
- **Fungsi Utama**:
  - Menampilkan rapor penilaian yang telah tersimpan secara elegan di browser.
  - Memuat rincian evaluasi lengkap: profil, komposisi nilai, rincian modul tiket, capaian soft skill, serta catatan atasan.
  - Menyediakan tombol navigasi untuk kembali ke laporan atau mencetak/download PDF.

---

### D. Dokumen Cetak / Download PDF (`/rapor/{id}/download`)
- **Route**: `route('rapor.download', $id)`
- **Controller**: `HomeController@downloadRapor`
- **View**: `resources/views/rapor_pdf.blade.php`
- **Karakteristik Desain**:
  - Berukuran **A4 Portrait** (210mm $\times$ 297mm).
  - Didesain secara presisi agar **tepat 1 lembar** tanpa ada halaman kedua kosong (*zero overflow*).
  - **4 Kartu Ringkasan Bawah**:
    1. *Nilai Tiket*: Ikon clipboard biru + Skor `/ 100`.
    2. *Nilai Soft Skill*: Ikon shield hijau + Skor `/ 100`.
    3. *Kepuasan Klien*: Ikon user ungu + Skor `/ 100`.
    4. *Nilai Akhir*: Semi-circle *speedometer arc gauge* visual dengan nilai akhir di tengahnya.

---

## 4. Formula & Logika Perhitungan Nilai (Scoring Engine)

Komposisi **Nilai Akhir (100%)** didasarkan pada 3 pilar:

$$\mathbf{\text{Nilai Akhir}} = (\mathbf{\text{Nilai Tiket}} \times 30\%) + (\mathbf{\text{Nilai Soft Skill}} \times 40\%) + (\mathbf{\text{Kepuasan Klien}} \times 30\%)$$

```
  ┌──────────────────────────────────────────────────────────────┐
  │                 KOMPOSISI NILAI AKHIR (100%)                 │
  ├──────────────────┬──────────────────────┬────────────────────┤
  │   Nilai Tiket    │   Nilai Soft Skill   │   Kepuasan Klien   │
  │     ( 30% )      │        ( 40% )       │       ( 30% )      │
  └──────────────────┴──────────────────────┴────────────────────┘
```

---

### A. Nilai Tiket (Bobot 30%)

#### 1. Skor Mentah per Modul
$$\text{Skor Modul} = \text{Bobot Modul} \times \text{Jumlah Tiket Selesai}$$

#### 2. Total Skor Mentah CS (`raw_ticket_score`)
$$\text{Raw Ticket Score} = \sum (\text{Bobot Modul} \times \text{Jumlah Tiket Selesai})$$

#### 3. Nilai Tiket Ternormalisasi (Skala 0–100) (`ticket_score`)
Nilai tiket tidak menggunakan angka mentah, melainkan dinormalisasi terhadap capaian tertinggi di tim pada bulan yang sama (`max_ticket_score`):

$$\text{Nilai Tiket (0--100)} = \left(\frac{\text{Raw Ticket Score CS}}{\text{Max Raw Score Tim}}\right) \times 100$$

> *Catatan*: Jika seorang CS memiliki skor mentah tertinggi di tim, maka Nilai Tiketnya otomatis mendapat nilai sempurna yaitu **100**.

#### 4. Porsi Kontribusi Tim (%) (`team_portion`)
Menunjukkan persentase kontribusi CS terhadap total beban penanganan tiket seluruh tim:

$$\text{Porsi Kontribusi Tim} = \left(\frac{\text{Raw Ticket Score CS}}{\sum \text{Raw Score Seluruh CS di Tim}}\right) \times 100\%$$

---

### B. Nilai Soft Skill (Bobot 40%)

Atasan memberikan nilai 1 sampai 5 untuk 5 kriteria:
1. Komunikasi
2. Kecepatan
3. Ketelitian
4. Kerjasama
5. Sikap & Profesionalisme

Total skor maksimal adalah $5 \times 5 = 25$. Dikonversikan ke skala 0–100:

$$\text{Nilai Soft Skill (0--100)} = \left(\frac{\text{Skor 1} + \text{Skor 2} + \text{Skor 3} + \text{Skor 4} + \text{Skor 5}}{25}\right) \times 100$$

---

### C. Nilai Kepuasan Klien (Bobot 30%)

Diambil dari rata-rata ulasan bintang klien untuk tiket yang ditangani (skala 1.0 s/d 5.0 bintang). Dikonversikan ke skala 0–100:

$$\text{Nilai Kepuasan Klien (0--100)} = \left(\frac{\text{Rating Bintang}}{5.0}\right) \times 100$$

---

### D. Nilai Akhir & Predikat

$$\text{Nilai Akhir} = (\text{Nilai Tiket} \times 0.30) + (\text{Nilai Soft Skill} \times 0.40) + (\text{Nilai Kepuasan Klien} \times 0.30)$$

#### Tabel Kategori Predikat:
| Rentang Nilai Akhir | Predikat Kinerja | Warna Indikator | Tindak Lanjut |
| :---: | :---: | :---: | :--- |
| **80.00 – 100.00** | **Sangat Baik** | 🟢 Hijau Emerald | Dipertahankan / Apresiasi |
| **70.00 – 79.99** | **Baik** | 🟡 Kuning Amber | Capaian standar terpenuhi |
| **< 70.00** | **Cukup** | 🔴 Merah Rose | Memerlukan evaluasi & bimbingan |

---

### E. Simulasi Contoh Perhitungan Nyata

Misalkan staf CS bernama **Budi** dinilai untuk periode **Oktober 2026**:

1. **Kinerja Tiket**:
   - Modul Accounting (bobot 90): 5 tiket $= 450$
   - Modul Front Desk (bobot 85): 8 tiket $= 680$
   - Modul POS (bobot 80): 6 tiket $= 480$
   - Modul General (bobot 60): 4 tiket $= 240$
   - $\text{Total Raw Score Budi} = 450 + 680 + 480 + 240 = \mathbf{1.850}$
   - Di tim CS pada bulan tersebut, skor tertinggi diraih oleh Siti sebesar **2.000**.
   - Total seluruh skor tim $= \mathbf{6.000}$.

   *Kalkulasi*:
   - $\text{Nilai Tiket} = \left(\frac{1.850}{2.000}\right) \times 100 = \mathbf{92.5}$
   - $\text{Porsi Kontribusi Tim} = \left(\frac{1.850}{6.000}\right) \times 100\% = \mathbf{30.83\%}$

2. **Soft Skill**:
   - Komunikasi: 4, Kecepatan: 5, Ketelitian: 4, Kerjasama: 5, Sikap: 5
   - Total $= 23$ dari 25
   - $\text{Nilai Soft Skill} = \left(\frac{23}{25}\right) \times 100 = \mathbf{92.0}$

3. **Kepuasan Klien**:
   - Rating rata-rata klien: 4.5 bintang
   - $\text{Nilai Kepuasan Klien} = \left(\frac{4.5}{5.0}\right) \times 100 = \mathbf{90.0}$

4. **Kalkulasi Nilai Akhir**:
   - Porsi Tiket: $92.5 \times 30\% = 27.75$
   - Porsi Soft Skill: $92.0 \times 40\% = 36.80$
   - Porsi Kepuasan Klien: $90.0 \times 30\% = 27.00$
   - **Total Nilai Akhir**: $27.75 + 36.80 + 27.00 = \mathbf{91.55}$
   - **Predikat**: 🟢 **Sangat Baik**

---

## 5. Arsitektur Database & Relasi Tabel

```
┌──────────────────┐          1:N         ┌──────────────────────────────────────┐
│  module_systems  │ ───────────────────> │  performance_review_module_details   │
│  - id            │                      │  - id                                │
│  - name          │                      │  - performance_review_id (FK)        │
│  - weight        │                      │  - module_name                       │
└──────────────────┘                      │  - weight                            │
                                          │  - ticket_count                      │
┌──────────────────┐          1:N         │  - score                             │
│      users       │ ──────────────────┐  └──────────────────────────────────────┘
│  - id            │                   │                    ▲
│  - name          │                   │                    │ N:1
│  - role          │                   ▼                    │
└──────────────────┘          ┌──────────────────────────────────────┐
                              │         performance_reviews          │
                              │  - id                                │
                              │  - user_id (FK ke users)             │
                              │  - reviewer_id (FK ke users)         │
                              │  - periode                           │
                              │  - total_tickets                     │
                              │  - client_satisfaction               │
                              │  - raw_ticket_score                  │
                              │  - max_ticket_score                  │
                              │  - team_portion                      │
                              │  - ticket_score                      │
                              │  - soft_skill_score                  │
                              │  - total_score                       │
                              │  - status (DRAFT / FINAL)            │
                              │  - notes                             │
                              │  - suggestions                       │
                              └──────────────────────────────────────┘
```

### Kolom Penting Hasil Migrasi Terbaru:
- `raw_ticket_score` (`DECIMAL(10,2)`): Menyimpan total poin mentah hasil kali bobot $\times$ kuantitas.
- `max_ticket_score` (`DECIMAL(10,2)`): Menyimpan angka benchmark tim saat penilaian disimpan.
- `team_portion` (`DECIMAL(5,2)`): Menyimpan persentase kontribusi CS terhadap tim.
- `score` pada tabel `performance_review_module_details` diubah menjadi `DECIMAL(10,2)` untuk mencegah error batas integer saat jumlah tiket/bobot bernilai tinggi.

---

## 6. Rekayasa Teknis & Kompatibilitas

### 1. Kompatibilitas PHP 7.4.4
Server operasional saat ini menggunakan **PHP 7.4.4**.
- **Larangan**: Tidak boleh menggunakan operator *nullsafe* PHP 8 (`?->`) di file Blade atau Controller karena akan memicu `ParseError: syntax error, unexpected '->'`.
- **Standar Solusi**: Menggunakan helper bawaan Laravel `optional(...)`:
  ```blade
  {{-- BENAR (Kompatibel PHP 7.4) --}}
  {{ optional($penilaian)->total_score ?? '-' }}

  {{-- SALAH (Memicu Fatal ParseError di PHP 7.4) --}}
  {{ $penilaian?->total_score ?? '-' }}
  ```

### 2. Optimasi DomPDF (Presisi 1 Lembar A4)
DomPDF merupakan library rendering PDF berbasis konversi HTML. Agar layout tampil identik dengan mockup tanpa merusak halaman:
- **Hindari CSS Modern**: DomPDF **tidak mendukung** Flexbox (`display: flex`), CSS Grid, dan `box-shadow` atau `linear-gradient`.
- **Gunakan CSS Tabel Murni**: Seluruh kolom dibagi menggunakan struktur `<table width="100%">` dan `<td>` dengan persentase lebar yang pasti.
- **Speedometer Arc Gauge SVG**:
  Arc setengah lingkaran dibuat menggunakan elemen `<svg>` dengan koordinat lengkungan yang dihitung langsung melalui fungsi matematika PHP:
  ```php
  $angle = 180 + ($persen * 180);
  $rad = deg2rad($angle);
  $x = $cx + $r * cos($rad);
  $y = $cy + $r * sin($rad);
  ```
  Hal ini menjamin jarum/busur indikator ter-render 100% instan tanpa bergantung pada library JavaScript luar.

---

## 7. FAQ & Troubleshooting

**Q: Mengapa skor tiket seorang CS bisa bernilai 100 padahal tiketnya tidak mencapai target tertentu?**
> **A**: Sistem menggunakan metode *benchmarking relatif tim*. Jika CS tersebut menangani skor mentah paling banyak di tim pada bulan tersebut, ia otomatis menjadi tolok ukur (skor 100). Anggota tim lain akan dinilai secara proporsional terhadap capaian tertinggi tersebut.

**Q: Bagaimana jika belum ada data tiket yang diselesaikan di bulan berjalan?**
> **A**: Form penilaian tetap dapat diakses dengan nilai tiket default 0. Atasan tetap dapat mengisi penilaian soft skill dan kepuasan klien.

**Q: Apakah atasan bisa mengubah nilai yang sudah tersimpan?**
> **A**: Bisa. Pada halaman `/laporan`, atasan cukup mengklik ikon pensil (Nilai Ulang) di baris CS yang bersangkutan, menyesuaikan evaluasi, dan menyimpan kembali. Sistem menggunakan metode `updateOrCreate` sehingga data lama di periode yang sama akan diperbarui tanpa membuat duplikat.

**Q: Bagaimana memastikan download PDF tetap 1 halaman jika catatan atasan sangat panjang?**
> **A**: CSS pada `rapor_pdf.blade.php` telah disetel dengan margin kompak (`margin: 10px 14px;`) dan ukuran font dinamis. Jika catatan sangat panjang, kotak textarea akan menampung teks secara teratur tanpa mendorong kartu ringkasan bawah ke halaman baru.

---
*Dokumen ini disusun dan disinkronkan ke repositori pada 15 Oktober 2026.*
