# Sistem Pelaporan Kerusakan Jalan Terintegrasi ELK Stack

Sistem berbasis web untuk pelaporan kerusakan jalan oleh masyarakat, dilengkapi panel admin untuk pengelolaan laporan dan integrasi ELK Stack (Elasticsearch, Logstash, Kibana) untuk monitoring log aplikasi secara real-time.

---

## Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Fitur Utama](#fitur-utama)
3. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
4. [Arsitektur Sistem](#arsitektur-sistem)
5. [Struktur Folder](#struktur-folder)
6. [Cara Setup](#cara-setup)
7. [Kredensial Default](#kredensial-default)
8. [URL Akses](#url-akses)
9. [Fitur Query Recommendation](#fitur-query-recommendation)
10. [Integrasi ELK Stack](#integrasi-elk-stack)
11. [Dokumentasi Tambahan](#dokumentasi-tambahan)

---

## Gambaran Umum

Sistem ini dibangun sebagai solusi digital untuk mempermudah masyarakat dalam melaporkan kerusakan jalan di wilayahnya. Masyarakat tidak perlu membuat akun — cukup isi formulir, unggah foto bukti, dan dapatkan kode tiket untuk memantau status laporan.

Di sisi admin, petugas dapat mengelola laporan, memberikan tanggapan beserta bukti foto, dan memantau aktivitas sistem melalui log yang terintegrasi dengan ELK Stack.

**Keunikan project ini** terletak pada fitur **Query Recommendation** yang secara otomatis menganalisis log aplikasi, mendeteksi pola error/warning, dan menghasilkan query Elasticsearch DSL siap pakai untuk investigasi di Kibana.

---

## Fitur Utama

### Sisi Publik (Masyarakat)
- Formulir pelaporan kerusakan jalan (tanpa registrasi)
- Upload foto bukti kerusakan
- Pemilihan lokasi berdasarkan kecamatan & kelurahan
- Kode tiket unik untuk tracking status laporan
- Halaman cek status laporan
- Riwayat tanggapan dari petugas (termasuk foto bukti tanggapan)
- Formulir saran & aspirasi

### Sisi Admin (Petugas)
- Dashboard dengan grafik pelaporan (filter per tahun)
- Kelola pengaduan — lihat detail, beri tanggapan, update status, upload foto bukti tanggapan
- Kelola wilayah — CRUD kecamatan & kelurahan
- Kelola saran — lihat dan hapus saran masyarakat
- Log aktivitas admin — dengan filter search dan tanggal
- **Query Recommendation** — deteksi otomatis pola error dari log, generate query Elasticsearch

### Integrasi ELK Stack
- Logging otomatis dalam format JSON (NDJSON)
- Pipeline Logstash untuk ingest log ke Elasticsearch
- Visualisasi dan pencarian log di Kibana
- Query Recommendation dengan dynamic time range

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.x (Native MVC, tanpa framework) |
| Database | MySQL / MariaDB |
| Frontend | Tailwind CSS (CDN), Lucide Icons, Chart.js |
| Web Server | Apache (XAMPP) |
| Logging | Custom JSON Logger → ELK Stack |
| Elasticsearch | 8.x / 9.x |
| Logstash | 8.x / 9.x |
| Kibana | 8.x / 9.x |

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                         ARSITEKTUR                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐     ┌──────────┐     ┌──────────┐               │
│  │Masyarakat│────▶│  Apache  │────▶│  PHP     │               │
│  │(Browser) │◀────│  (XAMPP) │◀────│  (MVC)   │               │
│  └──────────┘     └──────────┘     └────┬─────┘               │
│                                         │                       │
│                          ┌──────────────┼──────────────┐       │
│                          ▼              ▼              ▼        │
│                    ┌──────────┐   ┌──────────┐  ┌──────────┐  │
│                    │  MySQL   │   │ app.log  │  │ uploads/ │  │
│                    │(Database)│   │ (NDJSON) │  │ (Foto)   │  │
│                    └──────────┘   └────┬─────┘  └──────────┘  │
│                                        │                       │
│                                        ▼                       │
│                                  ┌──────────┐                  │
│                                  │ Logstash │                  │
│                                  └────┬─────┘                  │
│                                       ▼                        │
│                                 ┌───────────┐                  │
│                                 │Elasticsearch│                │
│                                 └─────┬─────┘                  │
│                                       ▼                        │
│                                  ┌──────────┐                  │
│                                  │  Kibana  │                  │
│                                  └──────────┘                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Struktur Folder

```
sistem_pelaporan_kerusakan_jalan/
├── app/
│   ├── Config/             # Konfigurasi app & database
│   ├── Controllers/
│   │   ├── Admin/          # Controller panel admin
│   │   └── Public/         # Controller halaman publik
│   ├── Core/               # Base classes (Controller, Database, Logger, Router)
│   ├── Models/             # Model database (Pengaduan, Kecamatan, dll)
│   └── Views/
│       ├── admin/          # View panel admin
│       ├── errors/         # Halaman error (404)
│       ├── layouts/        # Layout templates (admin, public, minimal)
│       └── public/         # View halaman publik
├── database/
│   ├── schema.sql          # Struktur tabel database
│   └── seeder.sql          # Data awal (admin, wilayah)
├── elk/
│   └── logstash-pipeline.conf  # Konfigurasi pipeline Logstash
├── logs/
│   └── app.log             # File log aplikasi (NDJSON)
├── public/                 # Document root (entry point)
│   ├── index.php           # Front controller & routing
│   ├── .htaccess           # URL rewriting
│   ├── assets/             # CSS, JS, gambar
│   └── uploads/            # File upload (foto pengaduan & tanggapan)
├── elk_integration_guide.md
├── query_recommend_documentation.md
├── test_case_query_recommend.md
└── README.md
```

---

## Cara Setup

### 1. Database

Buat database dan import schema + seeder:

```sql
-- Via phpMyAdmin atau MySQL CLI
CREATE DATABASE db_pelaporan_jalan;
```

Import file:
- `database/schema.sql` — struktur tabel
- `database/seeder.sql` — data awal (admin, kecamatan, kelurahan)

### 2. Konfigurasi

Edit file sesuai environment:

- `app/Config/database.php` — host, username, password, dbname
- `app/Config/app.php` — base_url, upload settings

### 3. Folder Upload

Pastikan folder berikut writable:
```
public/uploads/pengaduan/
public/uploads/pengaduan/tanggapan/
```

### 4. Apache (XAMPP)

- Pastikan Apache dan MySQL running di XAMPP
- Project harus berada di `htdocs/sistem_pelaporan_kerusakan_jalan/`
- Module `mod_rewrite` harus aktif

### 5. ELK Stack (Opsional)

Ikuti panduan lengkap di `elk_integration_guide.md`.

Ringkasan:
1. Download Elasticsearch, Logstash, Kibana (versi sama)
2. Konfigurasi masing-masing komponen
3. Jalankan berurutan: Elasticsearch → Kibana → Logstash
4. Sesuaikan path di `elk/logstash-pipeline.conf`

---

## Kredensial Default

| Username | Password | Jabatan |
|----------|----------|---------|
| admin | admin123 | Superadmin |
| petugas1 | admin123 | Petugas |

---

## URL Akses

| Halaman | URL |
|---------|-----|
| Beranda (Publik) | `http://localhost/sistem_pelaporan_kerusakan_jalan/` |
| Buat Laporan | `http://localhost/sistem_pelaporan_kerusakan_jalan/pengaduan` |
| Cek Status | `http://localhost/sistem_pelaporan_kerusakan_jalan/pengaduan/tracking` |
| Saran | `http://localhost/sistem_pelaporan_kerusakan_jalan/saran` |
| Login Admin | `http://localhost/sistem_pelaporan_kerusakan_jalan/admin/login` |
| Dashboard Admin | `http://localhost/sistem_pelaporan_kerusakan_jalan/admin/dashboard` |

---

## Fitur Query Recommendation

Fitur inti yang membedakan project ini. Sistem secara otomatis:

1. Membaca file `logs/app.log`
2. Mendeteksi pola error/warning (database failure, upload error, login gagal, pesan berulang)
3. Menghitung time range dinamis berdasarkan log tertua
4. Menghasilkan query Elasticsearch DSL yang siap dijalankan di Kibana Console

### Pola yang Dideteksi

| Pola | Trigger | Severity |
|------|---------|----------|
| Database Connection Failure | Pesan mengandung "database" atau exception PDO | ERROR |
| File Upload Error | Pesan mengandung "upload" atau "foto" | ERROR |
| Authentication Failure | Warning mengandung "login" | WARNING |
| Repeated Error | Pesan ERROR/WARNING yang muncul ≥3 kali | ERROR |
| Semua ERROR | Selalu muncul jika ada error | ERROR |

Dokumentasi lengkap: `query_recommend_documentation.md`
Test case: `test_case_query_recommend.md`

---

## Integrasi ELK Stack

### Format Log

Setiap aktivitas di aplikasi dicatat dalam format NDJSON (satu baris JSON per entry):

```json
{"timestamp":"2026-05-14T10:30:00+07:00","level":"INFO","message":"Admin login berhasil","context":{"admin_id":1,"username":"admin"},"ip_address":"127.0.0.1","endpoint":"POST /admin/login","user_agent":"Mozilla/5.0..."}
```

### Aktivitas yang Di-log

| Aktivitas | Level |
|-----------|-------|
| Login admin berhasil | INFO |
| Login admin gagal | WARNING |
| Logout admin | INFO |
| Pengaduan baru dibuat | INFO |
| Tanggapan admin diberikan | INFO |
| CRUD kecamatan/kelurahan | INFO |
| Hapus data wilayah | WARNING |
| Upload foto gagal | ERROR |
| Koneksi database gagal | ERROR |
| Validasi form gagal | WARNING |

### Alur Data ke ELK

```
PHP Logger → app.log → Logstash (pipeline) → Elasticsearch (index) → Kibana (visualisasi)
```

Panduan setup lengkap: `elk_integration_guide.md`

---

## Dokumentasi Tambahan

| File | Isi |
|------|-----|
| `elk_integration_guide.md` | Panduan lengkap setup ELK Stack dari nol |
| `query_recommend_documentation.md` | Dokumentasi teknis fitur Query Recommendation |
| `test_case_query_recommend.md` | Skenario pengujian fitur Query Recommendation |

---

## Status Pengaduan

Sistem menggunakan 3 status:

| Status | Warna | Keterangan |
|--------|-------|------------|
| Diproses | Biru | Laporan sedang ditangani petugas |
| Selesai | Hijau | Perbaikan sudah selesai |
| Ditolak | Merah | Laporan tidak valid / di luar wewenang |

Default status saat pengaduan baru masuk: **Diproses**

---

## Lisensi

Project ini dibuat untuk kepentingan akademis (skripsi).
