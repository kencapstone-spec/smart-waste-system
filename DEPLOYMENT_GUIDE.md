# Deployment Guide: Railway & Render

This guide provides step-by-step instructions for deploying this Laravel + Inertia.js application to **Railway** (recommended) or **Render**.

---

## Deploying on Railway (Recommended)

Railway offers persistent execution, automatic Docker builds, instant zero-downtime deploys, and one-click database plugins.

### 1. Connect Repository
1. Log into [Railway.app](https://railway.app).
2. Click **New Project** > **Deploy from GitHub repo**.
3. Select your `smart-waste-system` repository.
4. Railway will automatically detect [railway.json](file:///c:/xampp/htdocs/smart-waste-system/railway.json) and [Dockerfile](file:///c:/xampp/htdocs/smart-waste-system/Dockerfile).

### 2. Add MySQL Database (1-Click)
1. In your Railway project canvas, click **+ New** > **Database** > **Add MySQL**.
2. Railway will provision a private MySQL container and generate environment variables (`MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQL_URL`).
3. Click your application service > **Variables** > **Add Reference** and select the MySQL variables (or Railway links them automatically if connected). Our `config/database.php` automatically recognizes these!

### 3. Add Application Environment Variables
Under your Web Service > **Variables**, add:

| Key | Value | Notes |
|---|---|---|
| `APP_NAME` | `Smart Waste System` | App name |
| `APP_ENV` | `production` | Production environment |
| `APP_KEY` | *(Run `php artisan key:generate --show` and paste)* | Required 32-char key |
| `APP_DEBUG` | `false` | Security: hide traces |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` | Your Railway domain |
| `APP_TIMEZONE` | `Asia/Manila` | Philippines Time |
| `DB_CONNECTION` | `mysql` | Database driver |
| `PHP_CLI_SERVER_WORKERS` | `4` | 4x concurrency workers |
| `SESSION_DRIVER` | `cookie` | Zero DB query overhead |
| `CACHE_STORE` | `file` | Fast file caching |
| `OTP_DEV_MODE` | `true` *(or `false` with Semaphore)* | Test OTP `123456` |
| `CRON_SECRET` | *(Random secret string)* | For `/run-background-jobs` |

### 4. Database Migrations & Auto-Seeding
- Database migrations run **automatically** upon container startup via `docker-entrypoint.sh`.
- To seed default barangay zones and test accounts on first deploy:
  1. Go to your Web Service in Railway.
  2. Open the **Deployments** tab > click the active deployment > open the **Terminal / Exec** console.
  3. Run:
     ```bash
     php artisan db:seed --force
     ```
  4. Predefined accounts are now active:
     - **Super Admin**: `09111111111` (OTP: `123456`)
     - **Barangay Official**: `09333333333` (OTP: `123456`)
     - **Personnel**: `09555555555` (OTP: `123456`)
     - **Resident**: `09666666666` (OTP: `123456`)

---

## Prerequisites
- A GitHub account with your code pushed to a repository.
- A free account on [Render](https://render.com).
- A free account on [cron-job.org](https://cron-job.org) (for keep-alive and queue/scheduler processing).

---

## Step 1: Set Up a Free Permanent Database
Because Render's free databases expire or get wiped, we will use an external service.

1. Go to [TiDB Serverless](https://tidbcloud.com/) (for free MySQL) or [Supabase](https://supabase.com/) (for free PostgreSQL) and create an account.
2. Create a new database cluster.
3. Once created, find your database connection details (Host, Port, Database Name, Username, and Password). You will need these for Render later.

---

## Step 2: Application Preparedness
The repository already includes:
- `render-build.sh` (handles `composer install`, `npm install`, `npm run build`, `storage:link`, cache optimization, and database migrations).
- `/run-background-jobs` endpoint in `routes/web.php` (processes both background queue jobs and daily schedule tasks like midnight task generation).
- Reverse proxy trust & automatic HTTPS scheme enforcement in `bootstrap/app.php` and `AppServiceProvider.php`.

Ensure you commit and push all changes to your GitHub repository before proceeding:
```bash
git add .
git commit -m "chore: prepare repository for Render deployment"
git push origin main
```

---

## Step 3: Deploy on Render

1. Log into Render and click **New +** > **Web Service**.
2. Connect your GitHub repository.
3. Configure the service:
   - **Name:** `smart-waste-system` (or your chosen name)
   - **Language / Runtime:** **Docker** *(Render does NOT have a native PHP runtime — you must select Docker!)*
   - **Instance Type:** Free
   - *(Note: With Docker selected, Render automatically uses `Dockerfile` and `docker-entrypoint.sh`; you do not need to configure Build or Start commands).*
4. Scroll down and click **Advanced** to add your **Environment Variables**:

   | Key | Value | Notes |
   |-----|-------|-------|
   | `APP_NAME` | `Smart Waste System` | Application title |
   | `APP_ENV` | `production` | Production mode |
   | `APP_KEY` | *(Run `php artisan key:generate --show` locally and paste)* | 32-char encryption key |
   | `APP_DEBUG` | `false` | Disable stack traces in prod |
   | `APP_URL` | `https://your-app-name.onrender.com` | Your Render web URL |
   | `APP_TIMEZONE` | `Asia/Manila` | Philippines Local Time |
   | `PHP_CLI_SERVER_WORKERS` | `4` | Multi-worker concurrency (4x faster loads) |
   | `DB_CONNECTION` | `mysql` *(or `pgsql` for Supabase)* | Database driver |
   | `DB_HOST` | *(From your database provider)* | Database host |
   | `DB_PORT` | `3306` *(or `5432` for pgsql)* | Database port |
   | `DB_DATABASE` | *(From your database provider)* | Database name |
   | `DB_USERNAME` | *(From your database provider)* | Database user |
   | `DB_PASSWORD` | *(From your database provider)* | Database password |
   | `SESSION_DRIVER` | `cookie` | Instant sessions (zero DB latency overhead) |
   | `QUEUE_CONNECTION` | `database` | Background queue driver |
   | `CACHE_STORE` | `file` | Fast local file cache |
   | `CRON_SECRET` | *(Create a strong secret password, e.g. `WasteSysSecretKey_2026!`)* | Protects background runner |
   | `OTP_DEV_MODE` | `true` *(or `false` if using Semaphore)* | If `true`, code `123456` works for testing without SMS fees |
   | `SEMAPHORE_API_KEY` | *(Optional: Your Semaphore API Key)* | For real SMS OTP delivery in the PH |
   | `SEMAPHORE_SENDER_NAME` | `SmartWaste` | Sender name on SMS |

5. Click **Create Web Service**. Render will build your Docker image and deploy your app.

---

## Step 4: Setup Cron Jobs (Keep-Alive & Queues)

Now that your app is live, we need to prevent it from sleeping and automate the queue listener & daily scheduler.

1. Go to [cron-job.org](https://cron-job.org) and log in.
2. Go to **Cronjobs** > **Create Cronjob**.

### Cron Job 1: Keep App Awake
- **Title:** `Keep App Alive`
- **URL:** `https://your-app-name.onrender.com`
- **Schedule:** Every 10 minutes
- Save.

### Cron Job 2: Process Queues & Run Schedules
- **Title:** `Laravel Queue & Schedule Worker`
- **URL:** `https://your-app-name.onrender.com/run-background-jobs?secret=YOUR_CRON_SECRET` *(Replace YOUR_CRON_SECRET with what you put in Render)*
- **Schedule:** Every 1 minute
- Save.

---

## Step 5: (Optional) Seed Default Data on First Deploy

If you need the initial zones and admin accounts on your remote database:
1. In the Render Dashboard, go to your Web Service > **Shell**.
2. Run:
   ```bash
   php artisan db:seed --force
   ```
3. Your database will now have the default 13 zones and predefined login accounts:
   - **Super Admin**: `09111111111` or `09222222222` (OTP: `123456` if `OTP_DEV_MODE=true`)
   - **Barangay Official**: `09333333333` or `09444444444`
   - **Personnel**: `09555555555`
   - **Resident**: `09666666666`

---

## 🎉 You're Done!
Your Laravel + Inertia application is now deployed for free, stays awake 24/7, processes background queues and daily schedule tasks automatically every minute, and uses a permanent external database.

