# Task Management System

Sistem manajemen proyek dan tugas internal berbasis web, dirancang untuk memudahkan koordinasi tim dengan dilengkapi kontrol akses berbasis peran (Role-Based Access Control). Aplikasi ini dibangun di atas ekosistem **Laravel** dan **Livewire**, serta memanfaatkan **Tailwind CSS v4** untuk antarmuka yang reaktif dan modern.

---

## Prerequisites

Sebelum melakukan instalasi, pastikan lingkungan pengembangan Anda sudah memenuhi persyaratan berikut:

- PHP **8.2** atau lebih baru
- Composer
- Bun **v1.2.22** (sebagai package manager dan bundler)

---

## Instalasi

### Langkah 1: Clone Repository

```bash
git clone https://github.com/Team-Quadro/App-Project-Management.git
cd App-Project-Management
```

### Langkah 2: Instal Dependensi Backend (PHP)

```bash
composer install
```

### Langkah 3: Instal Dependensi Frontend

```bash
bun install
```

### Langkah 4: Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

> **Catatan:** Sesuaikan konfigurasi koneksi database di file `.env` Anda sebelum melanjutkan.

### Langkah 5: Siapkan Database

```bash
php artisan migrate --seed
```

### Langkah 6: Jalankan Server Lokal

```bash
# Buka dua terminal terpisah

# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend Build
bun run dev
```

Buka [http://localhost:8000](http://localhost:8000) di browser Anda.

---

## Akun Bawaan (Testing Environment)

Setelah menjalankan seeder, akun-akun berikut tersedia untuk testing:

| Role   | Email                     | Password |
|--------|---------------------------|----------|
| Admin  | admin@example.com         | admin123 |
| Member | jokogemink@example.com    | user123  |
| Member | prabogemink@example.com   | user123  |
| Member | gibrun@example.com        | user123  |

> **Catatan Otorisasi:**  
> - **Admin** — Akses penuh ke seluruh proyek dan tugas.  
> - **Member** — Hanya dapat mengelola proyek yang dimiliki atau proyek di mana mereka ditugaskan sebagai anggota.

---

## Fitur Utama

### Autentikasi & Keamanan
- Registrasi akun pengguna baru
- Sistem Login dan Logout yang aman
- Manajemen profil (nama, email, password, penghapusan akun)

### RBAC (Role-Based Access Control)
- **Admin** — Akses absolut tanpa batasan visibilitas
- **Member** — Akses terisolasi, hanya pada proyek yang relevan

### Interactive Dashboard
- Total proyek & rasio proyek aktif
- Total tugas & rincian tugas terbuka milik pengguna
- Visualisasi progress bar breakdown status tugas (To Do, In Progress, Done)
- Umpan aktivitas proyek terbaru
- Sorotan tugas dengan deadline terdekat

### Manajemen Proyek (CRUD)
- Buat proyek dengan judul, deskripsi, status, deadline, dan alokasi tim
- Daftar proyek dengan fitur pencarian & filter status
- Detail proyek beserta hierarki tugas terkait
- Edit informasi proyek & manajemen anggota tim
- Hapus proyek (cascade ke tugas terkait)

### Manajemen Tugas (CRUD)
- Delegasi tugas di dalam ruang lingkup proyek
- Atribut lengkap: judul, deskripsi, status, prioritas, assignee, deadline
- Update status inline (To Do → In Progress → Done)
- Filter berlapis: status, prioritas, dan teks
- Edit & hapus tugas

### Pencarian & Paginasi
- Pencarian responsif untuk proyek dan tugas
- Filter ganda (status aktif/arsip, skala prioritas)
- Paginasi 10 item per halaman dengan retensi parameter pencarian

---

## Arsitektur Aplikasi

Aplikasi mengadopsi **layered architecture** untuk pemisahan tanggung jawab yang bersih:

| Layer | Deskripsi |
|-------|-----------|
| **Controller / Component** | Mencegat HTTP request, mendelegasikan ke Service layer, menyajikan response |
| **Service** | Enkapsulasi logika bisnis (Dashboard, Project, Task) |
| **Policy** | Otorisasi pada level model (ProjectPolicy, TaskPolicy) |
| **Form Request** | Isolasi validasi input dari logika eksekusi |

---

## Skema Database

| Tabel | Kolom Utama |
|-------|-------------|
| `users` | `id`, `name`, `email`, `role`, `password`, `timestamps` |
| `projects` | `id`, `title`, `description`, `status`, `owner_id`, `deadline`, `timestamps` |
| `project_members` *(pivot)* | `project_id`, `user_id`, `timestamps` |
| `tasks` | `id`, `project_id`, `title`, `description`, `status`, `priority`, `assigned_to`, `deadline`, `timestamps` |

---

## Matriks Kebijakan Akses

### Project Policy

| Aksi      | Admin | Owner Proyek | Anggota Proyek | User Lain |
|-----------|:-----:|:------------:|:--------------:|:---------:|
| viewAny   | ✅    | ✅           | ✅             | ✅        |
| view      | ✅    | ✅           | ✅             | ❌        |
| create    | ✅    | ✅           | ✅             | ✅        |
| update    | ✅    | ✅           | ✅             | ❌        |
| delete    | ✅    | ✅           | ❌             | ❌        |

### Task Policy

| Aksi   | Admin | Owner Proyek | Anggota Proyek | Assignee Tugas | User Lain |
|--------|:-----:|:------------:|:--------------:|:--------------:|:---------:|
| create | ✅    | ✅           | ✅             | —              | ❌        |
| update | ✅    | ✅           | ✅             | —              | ❌        |
| delete | ✅    | ✅           | ❌             | ✅             | ❌        |

> **Keterangan:** Otoritas Admin melampaui batasan kepemilikan. Owner adalah kreator awal proyek. Anggota adalah tim yang diundang. Assignee adalah pelaksana spesifik sebuah tugas.
