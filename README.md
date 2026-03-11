# ProjectHub (Submission for JavaS Challenge)

ProjectHub adalah aplikasi web manajemen proyek dan tugas internal berbasis Laravel, dilengkapi dengan kontrol akses berbasis peran (Role-Based Access Control). Dibangun menggunakan Blade templating, Tailwind CSS, dan Alpine.js.


## Prerequisites

Sebelum melakukan instalasi, pastikan sudah install :

- PHP versi 8.2 atau lebih
- Composer
- Node.js versi 18 atau lebih baru

---

## Instalasi

### Langkah 1: Clone Repository

```bash
git clone https://github.com/MastayY/projecthub.git
cd challenge-javas
```

### Langkah 2: Instal Dependensi PHP

```bash
composer install
```

### Langkah 3: Instal Dependensi JavaScript

```bash
npm install
```

### Langkah 4: Konfigurasi Environment

Salin file `.env.example` menjadi `.env`, lalu generate application key:

```bash
cp .env.example .env
php artisan key:generate
```

### Langkah 5: Siapkan Database

Jalankan migrasi dan seeding untuk membuat tabel dan mengisi data dummy:

```bash
php artisan migrate --seed
```

### Langkah 6: Jalankan Server

```bash
composer run dev
```

Buka `http://localhost:8000` di browser.

## Akun Bawaan

Setelah run seeder, ada akun-akun berikut untuk testing:

| Role   | Email                | Password |
|---------|----------------------|------------|
| Admin   | admin@example.com    | admin123   |
| Member  | jokogemink@example.com    | user123    |
| Member  | prabogemink@example.com      | user123    |
| Member  | gibrun@example.com  | user123    |

Akun dengan peran Admin memiliki akses penuh ke seluruh proyek dan tugas. Akun dengan peran Member hanya dapat mengakses proyek yang dimiliki atau yang menjadi anggotanya.

---

## Fitur Utama

### Autentikasi

- Registrasi akun baru
- Login dan logout
- Manajemen profil (ubah nama, email, password, dan hapus akun)

### RBAC (Role-Based Access Control)

Ada dua role User:

- **Admin** -- Punya akses penuh ke seluruh proyek dan tugas di sistem tanpa batasan.
- **Member** -- Hanya dapat melihat dan mengelola proyek yang dimiliki atau yang menjadi anggotanya.

### Dashboard

Halaman dashboard menampilkan ringkasan workspace:

- Jumlah total proyek dan proyek aktif
- Jumlah total tugas dan tugas terbuka milik User
- Breakdown status tugas (To Do, In Progress, Done) dengan progress bar
- Daftar proyek terbaru
- Daftar tugas dengan deadline terdekat

### Manajemen Proyek (CRUD)

- Membuat proyek baru dengan judul, deskripsi, status, deadline, dan anggota tim
- Melihat daftar proyek dengan fitur pencarian dan filter status
- Melihat detail proyek beserta daftar tugas terkait
- Mengedit informasi proyek
- Menghapus proyek (beserta seluruh tugasnya)
- Mengelola anggota tim proyek melalui checkbox

### Manajemen Tugas (CRUD)

- Membuat tugas baru dalam konteks proyek tertentu
- Setiap tugas memiliki atribut: judul, deskripsi, status, prioritas, assignee, dan deadline
- Mengubah status tugas secara inline (klik tombol lingkaran pada daftar tugas)
- Siklus status: To Do -> In Progress -> Done -> To Do
- Mengedit dan menghapus tugas
- Filter tugas berdasarkan status, prioritas, dan pencarian teks

### Pencarian dan Filter

- Pencarian proyek berdasarkan judul dan deskripsi
- Filter proyek berdasarkan status (active, completed, archived)
- Pencarian tugas dalam halaman detail proyek
- Filter tugas berdasarkan status (todo, in_progress, done)
- Filter tugas berdasarkan prioritas (low, medium, high)

### Paginasi

Daftar proyek dan tugas menggunakan paginasi dengan 10 item per halaman. Parameter filter dipertahankan saat berpindah halaman.

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan arsitektur berlapis (layered architecture) untuk memisahkan tanggung jawab:

### Controller Layer

Controller bertanggung jawab untuk menerima HTTP request, mendelegasikan proses bisnis ke Service layer, dan mengembalikan response (view).

- `DashboardController` -- Menampilkan halaman dashboard dengan statistik
- `ProjectController` -- Menangani operasi CRUD untuk proyek
- `TaskController` -- Menangani operasi CRUD untuk tugas (nested resource di bawah proyek)

### Service Layer

Service layer berisi logika bisnis utama, terpisah dari controller:

- `DashboardService` -- Mengambil dan menghitung data ringkasan untuk dashboard
- `ProjectService` -- Menangani logika bisnis pembuatan, pembaruan, penghapusan proyek, dan sinkronisasi anggota tim
- `TaskService` -- Menangani logika bisnis pembuatan, pembaruan, penghapusan tugas, dan perubahan status inline

### Policy Layer

Policy mengatur otorisasi akses pada level model:

- `ProjectPolicy` -- Mengatur siapa yang boleh melihat, membuat, mengedit, dan menghapus proyek
- `TaskPolicy` -- Mengatur siapa yang boleh membuat, mengedit, dan menghapus tugas

### Form Request Layer

Form Request digunakan untuk validasi data input secara terpisah dari controller:

- `StoreProjectRequest` / `UpdateProjectRequest` -- Validasi data proyek
- `StoreTaskRequest` / `UpdateTaskRequest` -- Validasi data tugas

---

## Skema Database

### Tabel users

| Kolom              | Tipe      | Keterangan                              |
|--------------------|-----------|-----------------------------------------|
| id                 | bigint    | Primary key, auto increment             |
| name               | string    | Nama lengkap User                   |
| email              | string    | Alamat email (unik)                     |
| role               | string    | Peran User: "admin" atau "member"   |
| email_verified_at  | timestamp | Waktu verifikasi email (nullable)       |
| password           | string    | Kata sandi (hashed)                     |
| remember_token     | string    | Token "Remember Me" (nullable)          |
| created_at         | timestamp | Waktu pembuatan                         |
| updated_at         | timestamp | Waktu pembaruan terakhir                |

### Tabel projects

| Kolom       | Tipe      | Keterangan                                         |
|-------------|-----------|-----------------------------------------------------|
| id          | bigint    | Primary key, auto increment                         |
| title       | string    | Judul proyek                                        |
| description | text      | Deskripsi proyek (nullable)                         |
| status      | string    | Status: "active", "completed", atau "archived"      |
| owner_id    | bigint    | Foreign key ke tabel users (cascade on delete)      |
| deadline    | date      | Tenggat waktu proyek (nullable)                     |
| created_at  | timestamp | Waktu pembuatan                                     |
| updated_at  | timestamp | Waktu pembaruan terakhir                            |

### Tabel project_members (Pivot)

| Kolom      | Tipe      | Keterangan                                        |
|------------|-----------|---------------------------------------------------|
| project_id | bigint    | Foreign key ke tabel projects (cascade on delete)  |
| user_id    | bigint    | Foreign key ke tabel users (cascade on delete)     |
| created_at | timestamp | Waktu penambahan anggota                           |
| updated_at | timestamp | Waktu pembaruan terakhir                           |

Primary key gabungan: (project_id, user_id)

### Tabel tasks

| Kolom       | Tipe      | Keterangan                                          |
|-------------|-----------|------------------------------------------------------|
| id          | bigint    | Primary key, auto increment                          |
| project_id  | bigint    | Foreign key ke tabel projects (cascade on delete)    |
| title       | string    | Judul tugas                                          |
| description | text      | Deskripsi tugas (nullable)                           |
| status      | string    | Status: "todo", "in_progress", atau "done"           |
| priority    | string    | Prioritas: "low", "medium", atau "high"              |
| assigned_to | bigint    | Foreign key ke tabel users (null on delete, nullable)|
| deadline    | date      | Tenggat waktu tugas (nullable)                       |
| created_at  | timestamp | Waktu pembuatan                                      |
| updated_at  | timestamp | Waktu pembaruan terakhir                             |

---

## Access Policy

### ProjectPolicy

| Aksi      | Admin | Owner Proyek | Anggota Proyek | User Lain |
|-----------|-------|-------------|----------------|---------------|
| viewAny   | Ya    | Ya          | Ya             | Ya            |
| view      | Ya    | Ya          | Ya             | Tidak         |
| create    | Ya    | Ya          | Ya             | Ya            |
| update    | Ya    | Ya          | Ya             | Tidak         |
| delete    | Ya    | Ya          | Tidak          | Tidak         |

### TaskPolicy

| Aksi      | Admin | Owner Proyek | Anggota Proyek | Assignee Tugas | User Lain |
|-----------|-------|-------------|----------------|----------------|---------------|
| create    | Ya    | Ya          | Ya             | -              | Tidak         |
| update    | Ya    | Ya          | Ya             | -              | Tidak         |
| delete    | Ya    | Ya          | Tidak          | Ya             | Tidak         |

Keterangan:
- "Owner Proyek" adalah User yang membuat proyek tersebut.
- "Anggota Proyek" adalah User yang ditambahkan sebagai anggota tim proyek.
- "Assignee Tugas" adalah User yang diassign di tugas tertentu.
---
