# MySQL Migration Guide

## Step 1: Create MySQL Database

Open phpMyAdmin and run this SQL:
```sql
CREATE DATABASE stroke_rehab_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Step 2: Update .env File

Replace the database configuration section in your `.env` file:

**OLD (SQLite):**
```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

**NEW (MySQL):**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stroke_rehab_db
DB_USERNAME=root
DB_PASSWORD=
```

## Step 3: Run Migrations

After updating `.env`, run:
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop all existing tables
- Run all migrations
- Seed initial data (admin, clinician, patients, exercises)

## Step 4: Verify in phpMyAdmin

1. Open phpMyAdmin
2. Select `stroke_rehab_db` database
3. You should see all tables with data

## Tables Created:
- users
- patients
- clinicians
- rehab_plans
- exercises
- plan_exercises
- patient_feedback
- cache
- jobs
- sessions
- password_reset_tokens
- migrations

Done! Your application will work exactly the same with MySQL.
