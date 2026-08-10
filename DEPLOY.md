# Deploy guide — Education Interntions

Target stack (matches product decisions):

| Layer | Platform | Account hint |
|-------|----------|--------------|
| Frontend (Next.js) | [Vercel](https://vercel.com/) | `39479@sjc.ac.th` |
| Backend (Laravel + Filament) | [Render](https://render.com/) | `39479@sjc.ac.th` |
| Database (Postgres) | [Supabase](https://supabase.com/) | `thanitabackup01@gmail.com` — **create a new project** (keep existing DB separate) |
| Source | GitHub | `Thanitasjc` |

## 1) Supabase — new database

1. Sign in at https://supabase.com/ as `thanitabackup01@gmail.com`
2. **New project** (do not reuse the old one), e.g. `education-inter`
3. Region: prefer **Singapore (ap-southeast-1)**
4. After create, open **Project Settings → Database** and copy:
   - Host `db.<ref>.supabase.co`
   - Database `postgres`
   - User `postgres`
   - Password (the one you set)
   - Connection string (URI) — use **Session mode** or direct Postgres for Laravel
5. Optional: **Storage** bucket `public` for Filament uploads later

Laravel env mapping:

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=********
DB_SSLMODE=require

# or single URI (Render often uses this)
DATABASE_URL=postgresql://postgres:PASSWORD@db.xxxxx.supabase.co:5432/postgres?sslmode=require
```

## 2) GitHub

Repo is pushed from this monorepo. If missing:

```bash
git init
git add .
git commit -m "Initial Education Interntions platform"
gh repo create EducationInter --public --source=. --remote=origin --push
```

## 3) Render — API

1. Sign in https://render.com/
2. **New → Blueprint** and select this repo (`render.yaml`), **or**
   - **New Web Service** → root directory `backend`
   - Build / start commands from `render.yaml`
3. Set secrets:
   - `APP_KEY` — generate locally: `php artisan key:generate --show`
   - `APP_URL` — Render service URL, e.g. `https://education-inter-api.onrender.com`
   - `FRONTEND_URL` — Vercel URL, e.g. `https://education-inter.vercel.app`
   - `DATABASE_URL` — from Supabase
   - `SANCTUM_STATEFUL_DOMAINS` — Vercel host(s), e.g. `education-inter.vercel.app,www.yourdomain.com`
4. Deploy, then open `/admin` and `/api/home`

Seed once (Render shell or one-off):

```bash
php artisan db:seed --force
```

## 4) Vercel — frontend

1. Sign in https://vercel.com/
2. **Add New Project** → import GitHub repo
3. Root Directory: `frontend`
4. Framework: Next.js (uses `frontend/vercel.json`)
5. Environment variables:

```env
NEXT_PUBLIC_API_URL=https://education-inter-api.onrender.com/api
NEXT_PUBLIC_SITE_URL=https://education-inter.vercel.app
```

6. Deploy

## 5) Post-deploy checklist

- [ ] `GET /api/home?locale=th` returns JSON
- [ ] Filament `/admin` login works
- [ ] Frontend `/th` loads hero + sections
- [ ] CORS / Sanctum domains include the Vercel host
- [ ] `php artisan storage:link` on Render (already in build)
- [ ] Re-seed demo content if needed

## Notes

- Creating Supabase / Vercel / Render resources requires interactive login in those dashboards; this repo ships the configs and GitHub source ready to connect.
- Keep the **old** Supabase DB untouched; point production env only at the **new** project.
