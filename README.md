# Education Interntions Platform

Monorepo for **Education Interntions** study-abroad platform.

| Layer | Stack | Deploy |
|-------|--------|--------|
| Frontend | Next.js 16 + Tailwind + next-intl (`th` / `en`) | Vercel |
| Backend API + Admin | Laravel 12 + Filament 3 + Sanctum | VPS / Cloud |
| Database | SQLite (local) / **Supabase Postgres** (prod) | Supabase |
| Cache / Queue | Redis (optional) | Redis / Upstash |
| Storage | Laravel disk / S3 / Supabase Storage | — |

```text
EducationInter/
├── frontend/   # Next.js (website + student portal)
└── backend/    # Laravel API + Filament admin
```

## Quick start

### 1) Backend

```bash
cd backend
cp .env.example .env   # if needed
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --port=8001
```

- API: `http://127.0.0.1:8001/api`
- Admin: `http://127.0.0.1:8001/admin`
- Admin login: `admin@wineducation.local` / `password`
- Consultant: `consultant@wineducation.local` / `password`
- Student: `student@wineducation.local` / `password`

### 2) Frontend

```bash
cd frontend
cp .env.local.example .env.local
# NEXT_PUBLIC_API_URL=http://127.0.0.1:8001/api
# NEXT_PUBLIC_SITE_URL=http://127.0.0.1:3000
npm install
npm run dev
```

- Site: prefer `http://127.0.0.1:3000/th` (avoids Windows IPv6/`localhost` hang)

## Decisions locked

1. **Admin** = Filament on Laravel (`/admin`)
2. **Student auth** = email/password + Facebook + LINE (Socialite)
3. **i18n** = `th` / `en` (next-intl)
4. **Deploy** = Frontend on [Vercel](https://vercel.com/), API on [Render](https://render.com/), DB on [Supabase](https://supabase.com/) Postgres

See **[DEPLOY.md](./DEPLOY.md)** for step-by-step production setup.

## Phase status

| Phase | Status |
|-------|--------|
| P1 Scaffold + catalog + Filament CMS | Done |
| P2 Live catalog | Done |
| P3 CRM pipeline + apply + services | Done |
| P4 Student portal + blog | Done |
| P5 SEO + events + notifications + document review + consultant scope | Done |

## Public API highlights

- `GET /api/home`, `/api/sitemap`, `/api/pages/{key}`
- Catalog: countries, universities, courses, scholarships, services
- Language programs: `/api/programs?age=12-16|16-18|18-25|25-plus|50-plus`, `/api/programs/{slug}`
- Blog: `/api/posts`, `/api/post-categories`
- Events: `/api/events`, `/api/events/{slug}`
- Leads / applications: `POST /api/leads`, `POST /api/applications`
- Auth: register/login + social redirects
- Student (Sanctum): dashboard, applications, documents, appointments, notifications, profile

## Filament admin

- CMS: Countries, Universities, Courses, Scholarships, Language Programs, Services, Reviews, Blog, Events, Home Sections
- CRM: Leads (advance / assign / activities) — consultants see assigned leads only
- Admission: Applications (+ documents relation), Documents (approve/reject + student notify)

## SEO (P5)

- Frontend: `app/sitemap.ts`, `app/robots.ts`, `generateMetadata`, JSON-LD
- Backend: `seo_metadata` morph + `/api/sitemap`
- Blog category filters on `/blog?category=`

## Supabase + reader notes

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password
DB_SSLMODE=require
DB_READ_HOST=db-read.xxxxx.supabase.co
```

Redis (cache/queue) recommended later for CRM jobs & notifications.
