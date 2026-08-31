# Laravel + Render Deployment Guide (100% Free Tier)

This guide provides a step-by-step process for deploying this Laravel + Inertia.js application for free using **Render** (for web hosting) and a free external database provider, bypassing the usual free tier limits.

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
   - **Environment:** `PHP`
   - **Build Command:** `bash render-build.sh`
   - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Instance Type:** Free
4. Scroll down and click **Advanced** to add your **Environment Variables**:

   | Key | Value | Notes |
   |-----|-------|-------|
   | `APP_NAME` | `Smart Waste System` | Application title |
   | `APP_ENV` | `production` | Production mode |
   | `APP_KEY` | *(Run `php artisan key:generate --show` locally and paste)* | 32-char encryption key |
   | `APP_DEBUG` | `false` | Disable stack traces in prod |
   | `APP_URL` | `https://your-app-name.onrender.com` | Your Render web URL |
   | `APP_TIMEZONE` | `Asia/Manila` | Philippines Local Time |
   | `DB_CONNECTION` | `mysql` *(or `pgsql` for Supabase)* | Database driver |
   | `DB_HOST` | *(From your database provider)* | Database host |
   | `DB_PORT` | `3306` *(or `5432` for pgsql)* | Database port |
   | `DB_DATABASE` | *(From your database provider)* | Database name |
   | `DB_USERNAME` | *(From your database provider)* | Database user |
   | `DB_PASSWORD` | *(From your database provider)* | Database password |
   | `SESSION_DRIVER` | `database` | Persistent sessions |
   | `QUEUE_CONNECTION` | `database` | Background queue driver |
   | `CACHE_STORE` | `database` | Cache driver |
   | `CRON_SECRET` | *(Create a strong secret password, e.g. `WasteSysSecretKey_2026!`)* | Protects background runner |
   | `OTP_DEV_MODE` | `true` *(or `false` if using Semaphore)* | If `true`, code `123456` works for testing without SMS fees |
   | `SEMAPHORE_API_KEY` | *(Optional: Your Semaphore API Key)* | For real SMS OTP delivery in the PH |
   | `SEMAPHORE_SENDER_NAME` | `SmartWaste` | Sender name on SMS |

5. Click **Create Web Service**. Render will execute `render-build.sh` and deploy your app.

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

