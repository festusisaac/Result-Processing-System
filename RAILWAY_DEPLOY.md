# 🚀 Deploying RMS to Railway (with SQLite)

This guide will help you deploy your Result Management System to Railway using SQLite with persistent storage.

## Prerequisites

1. **GitHub Account** - Your code must be on GitHub
2. **Railway Account** - Sign up at [railway.app](https://railway.app)

## Step 1: Create Project on Railway

1. Log in to Railway
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Select your **RMS repository**
5. Click **"Deploy Now"**

## Step 2: Configure Environment Variables

1. Go to your project dashboard
2. Click on the **"Variables"** tab
3. Add the following variables (copy from your local `.env`):

| Variable | Value |
|----------|-------|
| `APP_NAME` | RMS |
| `APP_ENV` | production |
| `APP_KEY` | (Copy from your local .env) |
| `APP_DEBUG` | false |
| `APP_URL` | https://your-project-url.up.railway.app |
| `DB_CONNECTION` | sqlite |
| `DB_DATABASE` | /app/database/database.sqlite |
| `NIXPACKS_PHP_VERSION` | 8.2 |

> **Note:** We set `DB_DATABASE` to `/app/database/database.sqlite` because we will mount a persistent volume there.

## Step 3: Add Persistent Volume (Critical for SQLite)

Since Railway is ephemeral (files are reset on every deploy), you **MUST** add a volume to save your database.

1. Go to your service **Settings**
2. Scroll down to **"Volumes"**
3. Click **"Add Volume"**
4. Mount Path: `/app/database`
5. Click **"Add"**

This ensures your `database.sqlite` file is stored safely and survives deployments.

## Step 4: Push Changes to GitHub

I have created a `nixpacks.toml` file that does two things:
1. Installs the required extensions (`gd`, `zip`, etc.)
2. **Overrides the build command** to force Composer to ignore the platform check.

1. **Commit and Push** the new `nixpacks.toml` file:
   ```bash
   git add nixpacks.toml
   git commit -m "Add nixpacks.toml with build command override"
   git push
   ```

2. Railway will detect the `nixpacks.toml` and use our custom build command.

## Step 5: Verify Deployment

1. Go to the **"Deployments"** tab in Railway
2. You should see a new deployment building
3. Check the **"Logs"** to ensure `composer install` succeeds (it should now have the `--ignore-platform-req=ext-gd` flag)

## 🎉 You're Live!

Your application should now be running on Railway with a persistent SQLite database!

### Troubleshooting

- **500 Error?** Check "Logs" tab in Railway.
- **Database Reset?** Ensure you added the Volume correctly to `/app/database`.
- **Missing Styles?** Ensure `APP_URL` matches your Railway URL exactly (https included).

## 🎉 You're Live!

Your application should now be running on Railway with a persistent SQLite database!

### Troubleshooting

- **500 Error?** Check "Logs" tab in Railway.
- **Database Reset?** Ensure you added the Volume correctly to `/app/database`.
- **Missing Styles?** Ensure `APP_URL` matches your Railway URL exactly (https included).

