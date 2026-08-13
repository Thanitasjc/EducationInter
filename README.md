# Education Interntions

แพลตฟอร์มที่ปรึกษาเรียนต่อต่างประเทศ — monorepo ประกอบด้วย Frontend (Next.js) + Backend (Laravel/Filament) + ฐานข้อมูล Supabase

> **คำเตือนเรื่องความลับ:** repo นี้อยู่บน GitHub แบบ public  
> ส่วนรหัสผ่านด้านล่างเก็บตามที่ขอไว้ใน README เพื่อใช้งานภายในทีม  
> ถ้า repo เปิดสาธารณะ แนะนำให้ **หมุนรหัส (rotate)** หลัง deploy แล้ว และอย่าแชร์ลิงก์ repo ให้คนนอก

---

## สารบัญ

1. [ภาพรวมระบบ](#1-ภาพรวมระบบ)
2. [บัญชีและลิงก์ที่ใช้](#2-บัญชีและลิงก์ที่ใช้)
3. [ความลับ / รหัสฐานข้อมูล (เก็บไว้ที่นี่)](#3-ความลับ--รหัสฐานข้อมูล-เก็บไว้ที่นี่)
4. [โครงสร้างโปรเจกต์](#4-โครงสร้างโปรเจกต์)
5. [รันแบบ local](#5-รันแบบ-local)
6. [วิธี Deploy ทีละขั้น](#6-วิธี-deploy-ทีละขั้น)
7. [เช็คหลัง Deploy](#7-เช็คหลัง-deploy)
8. [API / Admin หลักๆ](#8-api--admin-หลักๆ)

---

## 1) ภาพรวมระบบ

| ชั้น | เทคโนโลยี | แพลตฟอร์ม Deploy |
|------|-----------|------------------|
| Frontend | Next.js + Tailwind + next-intl (`th` / `en`) | **Vercel** |
| Backend API + Admin | Laravel 12 + Filament 3 + Sanctum | **Render** (Docker) |
| Database | PostgreSQL | **Supabase** (โปรเจกต์ใหม่ `education-inter`) |
| Source | Git | **GitHub** `Thanitasjc/EducationInter` |

```text
ผู้ใช้เว็บ  →  Vercel (frontend)  →  Render (Laravel /api)  →  Supabase Postgres
แอดมิน     →  Render /admin (Filament)
```

---

## 2) บัญชีและลิงก์ที่ใช้

| บริการ | บัญชี (อ้างอิง) | ลิงก์ |
|--------|-----------------|------|
| GitHub | `Thanitasjc` | https://github.com/Thanitasjc/EducationInter |
| Vercel | `39479@sjc.ac.th` | https://vercel.com/ |
| Render | `39479@sjc.ac.th` | https://dashboard.render.com/ |
| Supabase | `thanitabackup01@gmail.com` | https://supabase.com/dashboard |

### URL โปรดักชัน (ตั้งค่าแล้ว / ตั้งตามชื่อ)

| บริการ | URL |
|--------|-----|
| API (Render) | https://education-inter-api.onrender.com |
| API home | https://education-inter-api.onrender.com/api/home |
| Admin Filament | https://education-inter-api.onrender.com/admin |
| Frontend (Vercel) | https://education-inter.vercel.app *(หลัง deploy สำเร็จ)* |
| Supabase dashboard | https://supabase.com/dashboard/project/velpsbmfvdydkhuitizo |

---

## 3) ความลับ / รหัสฐานข้อมูล (เก็บไว้ที่นี่)

### Supabase — โปรเจกต์ `education-inter`

| รายการ | ค่า |
|--------|-----|
| Project name | `education-inter` |
| Project ref | `velpsbmfvdydkhuitizo` |
| Region | Singapore (`ap-southeast-1`) |
| Database | `postgres` |
| **DB Password** | `X6wGpBGin68DNfD3` |
| Pooler host (Session) | `aws-0-ap-southeast-1.pooler.supabase.com` |
| Pooler user | `postgres.velpsbmfvdydkhuitizo` |
| Direct host (ทางเลือก) | `db.velpsbmfvdydkhuitizo.supabase.co` |
| Direct user | `postgres` |

### Connection string ที่ใช้กับ Render / Laravel 12

Laravel 12 อ่าน **`DB_URL`** (ไม่ใช่แค่ `DATABASE_URL`)

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.velpsbmfvdydkhuitizo
DB_PASSWORD=X6wGpBGin68DNfD3
DB_SSLMODE=require

DB_URL=postgresql://postgres.velpsbmfvdydkhuitizo:X6wGpBGin68DNfD3@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require
DATABASE_URL=postgresql://postgres.velpsbmfvdydkhuitizo:X6wGpBGin68DNfD3@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require
```

### Laravel APP_KEY (production)

```env
APP_KEY=base64:cB9DqceR15gcH8iTxYR6NwTf9sLGe3cP+ilBNndwHYA=
```

### Env เต็มชุดที่ใส่บน Render

```env
APP_NAME=Education Interntions
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:cB9DqceR15gcH8iTxYR6NwTf9sLGe3cP+ilBNndwHYA=
APP_URL=https://education-inter-api.onrender.com
FRONTEND_URL=https://education-inter.vercel.app
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.velpsbmfvdydkhuitizo
DB_PASSWORD=X6wGpBGin68DNfD3
DB_SSLMODE=require
DB_URL=postgresql://postgres.velpsbmfvdydkhuitizo:X6wGpBGin68DNfD3@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require
DATABASE_URL=postgresql://postgres.velpsbmfvdydkhuitizo:X6wGpBGin68DNfD3@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=s3
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=<จาก Supabase Storage → S3 → New access key>
AWS_SECRET_ACCESS_KEY=<secret ที่โชว์ครั้งเดียว>
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=media
AWS_ENDPOINT=https://velpsbmfvdydkhuitizo.storage.supabase.co/storage/v1/s3
AWS_URL=https://velpsbmfvdydkhuitizo.supabase.co/storage/v1/object/public/media
AWS_USE_PATH_STYLE_ENDPOINT=true
SANCTUM_STATEFUL_DOMAINS=education-inter.vercel.app
```

> **รูปภาพถาวร (Supabase Storage):**  
> 1. สร้าง bucket สาธารณะชื่อ `media` (ทำแล้วในโปรเจกต์นี้)  
> 2. สร้าง S3 access key ที่ Storage → S3 แล้วใส่ `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` บน Render  
> 3. Filament อัปโหลดจะเก็บ **URL สาธารณะ** ใน DB → ทน redeploy  
> 4. กู้รูป demo เก่าที่พัง: `php artisan media:restore-demo-urls`

### Env บน Vercel (frontend)

```env
NEXT_PUBLIC_API_URL=https://education-inter-api.onrender.com/api
NEXT_PUBLIC_SITE_URL=https://education-inter.vercel.app
NEXT_PUBLIC_MEDIA_BASE_URL=https://velpsbmfvdydkhuitizo.supabase.co/storage/v1/object/public/media
```

---

## 4) โครงสร้างโปรเจกต์

```text
EducationInter/
├── README.md                 # เอกสารนี้ (ภาษาไทย + ความลับ deploy)
├── DEPLOY.md                 # คู่มือ deploy แบบสั้น (อังกฤษ)
├── render.yaml               # Blueprint Render (Docker API)
├── frontend/                 # เว็บ Next.js
│   ├── app/                  # App Router + locale th/en
│   ├── components/           # UI (Hero, Partner, Apply, …)
│   ├── lib/api.ts            # เรียก Laravel API
│   ├── messages/             # คำแปล th / en
│   ├── vercel.json           # ตั้งค่า Vercel (region sin1)
│   └── package.json
└── backend/                  # Laravel API + Filament
    ├── app/
    │   ├── Filament/         # CRUD แอดมิน
    │   ├── Http/Controllers/ # API
    │   └── Models/
    ├── database/migrations/
    ├── database/seeders/
    ├── routes/api.php
    ├── Dockerfile            # ใช้บน Render (PHP 8.3)
    ├── docker-entrypoint.sh  # migrate + map DB_URL + serve
    └── .dockerignore
```

### Frontend (`frontend/`) ไล่โฟลเดอร์สำคัญ

| พาธ | หน้าที่ |
|------|---------|
| `app/[locale]/(website)/` | หน้าเว็บสาธารณะ (home, about, apply, universities, …) |
| `app/[locale]/(auth)/` | login / register |
| `app/[locale]/(student)/` | พอร์ทัลนักเรียน |
| `components/hero/` | Hero สไลด์จาก CMS |
| `components/home/` | Section มหาวิทยาลัย / คอร์ส / พาร์ทเนอร์ |
| `lib/api.ts` | ดึงข้อมูลจาก `/api/*` |
| `messages/th.json`, `en.json` | ข้อความ i18n |

### Backend (`backend/`) ไล่โฟลเดอร์สำคัญ

| พาธ | หน้าที่ |
|------|---------|
| `app/Filament/Resources/` | จัดการ CMS / CRM ใน `/admin` |
| `routes/api.php` | REST API ให้ frontend |
| `database/seeders/` | ข้อมูลตัวอย่าง (หลัง migrate) |
| `config/database.php` | อ่าน `DB_URL` + `DB_*` |
| `Dockerfile` | image สำหรับ Render |

### ดีไซน์ที่ล็อกไว้แล้ว

1. Admin = Filament ที่ `/admin`
2. Student auth = อีเมล/รหัส + Facebook + LINE
3. ภาษา = `th` / `en`
4. Deploy = Vercel + Render (Docker) + Supabase

---

## 5) รันแบบ local

### Backend

```bash
cd backend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --port=8001
```

- API: http://127.0.0.1:8001/api  
- Admin: http://127.0.0.1:8001/admin  

- https://education-inter-api.onrender.com/admin

- ผู้ใช้ seed:  
  - Admin: `admin@wineducation.local` / `password`  
  - Consultant: `consultant@wineducation.local` / `password`  
  - Student: `student@wineducation.local` / `password`

ถ้าจะชี้ local ไป Supabase จริง ให้ใส่ค่าในหัวข้อ [3] ลง `.env` ของ backend

### Frontend

```bash
cd frontend
cp .env.local.example .env.local
# NEXT_PUBLIC_API_URL=http://127.0.0.1:8001/api
# NEXT_PUBLIC_SITE_URL=http://localhost:3000
npm install
npm run dev
```

- เปิด: http://localhost:3000/th  
  (บน Windows แนะนำ `localhost` / `127.0.0.1` ตามที่ตั้งใน `package.json`)

---

## 6) วิธี Deploy ทีละขั้น

### ขั้นที่ 0 — Push โค้ดขึ้น GitHub

```bash
git add .
git commit -m "Deploy Education Interntions"
git push origin main
```

Repo: https://github.com/Thanitasjc/EducationInter

---

### ขั้นที่ 1 — Supabase (ฐานข้อมูล) ✅ ทำแล้ว

1. เข้า https://supabase.com/ ด้วย `thanitabackup01@gmail.com`
2. สร้างโปรเจกต์ใหม่ชื่อ `education-inter` (อย่าใช้ DB เก่า)
3. Region: **Singapore**
4. เก็บรหัสผ่านไว้ในหัวข้อ [3] ของ README นี้
5. ใช้ **Session pooler** สำหรับ Render (ค่าอยู่ในหัวข้อ [3])

---

### ขั้นที่ 2 — Render (Laravel API)

> หมายเหตุ: หน้า New Web Service ของ Render **ไม่มี PHP native** แล้ว  
> โปรเจกต์นี้ใช้ **Language = Docker** + `backend/Dockerfile`

1. เข้า https://dashboard.render.com/
2. **New → Web Service** → เลือก repo `Thanitasjc/EducationInter`
3. ตั้งค่า:
   - **Name:** `education-inter-api`
   - **Language:** Docker
   - **Branch:** `main`
   - **Region:** Singapore
   - **Root Directory:** `backend`
   - **Dockerfile Path:** `./Dockerfile`
   - **Instance:** Free (หรือ Starter ถ้าว่างช้า)
4. **Environment** → วาง env จากหัวข้อ [3]  
   - อย่าให้มี **key ซ้ำ** (ถ้ามีแถวว่างซ้ำ ให้ลบแถวว่างก่อน Save)
   - ต้องมีอย่างน้อย `DB_HOST` / `DB_USERNAME` / `DB_PASSWORD` หรือ `DB_URL` ที่ถูกต้อง  
   - Laravel 12 ใช้ `DB_URL` — อย่าใส่แค่ `DATABASE_URL` อย่างเดียว
5. กด **Save, rebuild, and deploy** (หรือ Deploy ครั้งแรก)
6. รอสถานะ **Live**
7. ทดสอบ: https://education-inter-api.onrender.com/api/home
8. (ทางเลือก) seed ข้อมูลครั้งแรกผ่าน Render Shell:

```bash
php artisan db:seed --force
```

**Free tier:** service จะ sleep ตอนไม่มีคนใช้ — คำขอแรกอาจช้า ~50 วินาที

---

### ขั้นที่ 3 — Vercel (Next.js frontend)

1. เข้า https://vercel.com/
2. **Add New Project** → Import `Thanitasjc/EducationInter`
3. ตั้งค่า:
   - **Root Directory:** `frontend` *(อย่าเลือกทั้ง monorepo / Services)*
   - Framework: Next.js
4. Environment Variables:

```env
NEXT_PUBLIC_API_URL=https://education-inter-api.onrender.com/api
NEXT_PUBLIC_SITE_URL=https://education-inter.vercel.app
```

5. Deploy
6. ถ้าโดเมนจริงไม่ใช่ `education-inter.vercel.app` ให้แก้:
   - `NEXT_PUBLIC_SITE_URL` บน Vercel
   - `FRONTEND_URL` + `SANCTUM_STATEFUL_DOMAINS` บน Render แล้ว Redeploy

---

### ขั้นที่ 4 — โยงสองฝั่งให้ตรงกัน

| ตัวแปร | อยู่ที่ | ค่า |
|--------|--------|-----|
| `NEXT_PUBLIC_API_URL` | Vercel | `https://education-inter-api.onrender.com/api` |
| `APP_URL` | Render | `https://education-inter-api.onrender.com` |
| `FRONTEND_URL` | Render | URL ของ Vercel |
| `SANCTUM_STATEFUL_DOMAINS` | Render | โฮสต์ Vercel (ไม่มี `https://`) |

---

## 7) เช็คหลัง Deploy

- [ ] `GET https://education-inter-api.onrender.com/api/home?locale=th` ได้ JSON
- [ ] เปิด `/admin` ล็อกอินได้
- [ ] เปิด frontend `/th` เห็น hero / sections
- [ ] ไม่มี env key ซ้ำบน Render
- [ ] `DB_*` หรือ `DB_URL` ชี้ Supabase ไม่ใช่ `127.0.0.1`
- [ ] Seed แล้ว (ถ้าต้องการข้อมูลตัวอย่าง)

---

## 8) API / Admin หลักๆ

### Public API

- `GET /api/home`, `/api/sitemap`, `/api/pages/{key}`
- Catalog: countries, universities, courses, scholarships, services
- Language programs: `/api/programs?...`
- Blog / Events
- `POST /api/leads`, `POST /api/applications`
- Auth + Student portal (Sanctum)

### Filament (`/admin`)

- CMS: Countries, Universities, Courses, Scholarships, Programs, Services, Reviews, Blog, Events, Home / Page Content, Partners
- CRM: Leads, Appointments, Consultants, Student Notifications
- Admission: Students, Applications, Documents, Document Types

---

## Phase status

| Phase | Status |
|-------|--------|
| P1 Scaffold + catalog + Filament CMS | Done |
| P2 Live catalog | Done |
| P3 CRM pipeline + apply + services | Done |
| P4 Student portal + blog | Done |
| P5 SEO + events + notifications | Done |
| Deploy Vercel + Render + Supabase | In progress |

---

## เอกสารเพิ่มเติม

- คู่มือ deploy ภาษาอังกฤษแบบสั้น: [DEPLOY.md](./DEPLOY.md)
- Blueprint Render: [render.yaml](./render.yaml)
