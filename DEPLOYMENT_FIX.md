# Railway Deployment Fix - SQLite Connection Error

## Problem
The app was crashing with `SQLSTATE[HY000] [2002] Connection refused (Connection: mysql)` because:
1. Laravel was trying to connect to MySQL instead of SQLite
2. The config cache wasn't being cleared before migrations ran
3. PDO SQLite extension wasn't explicitly included in the Docker image
4. The Docker entrypoint wasn't properly starting Apache after setup

## Solutions Applied

### 1. **Fixed Docker Entrypoint (`docker-entrypoint.sh`)**
   - **Clear caches FIRST** before anything else (this was the main issue)
   - Removed empty DB variables instead of setting them to empty strings
   - Added database directory creation before trying to use it
   - Added seeding guard to prevent re-seeding on every deploy
   - Clear caches again right before running migrations
   - Better error handling with `2>&1` redirection

### 2. **Updated Dockerfile**
   - Added explicit `pdo_sqlite` PHP extension: `docker-php-ext-install pdo pdo_sqlite`
   - Created database directory and fixed permissions during build
   - **Added CMD directive**: `CMD ["apache2-foreground"]` (critical for app to stay running)
   - This allows the entrypoint script to run setup, then start Apache

### 3. **Updated `.env.example`**
   - Changed default `DB_CONNECTION` from `mysql` to `sqlite`
   - Cleared MySQL connection details
   - Set `DB_DATABASE` to `database/database.sqlite`

## How It Works Now

1. **Container starts** → Docker runs entrypoint script
2. **Entrypoint clears all caches** (before config is loaded)
3. **Entrypoint configures SQLite** in `.env` and as environment variables
4. **Entrypoint creates database and runs migrations** against SQLite
5. **Entrypoint caches configuration** with SQLite settings
6. **CMD starts Apache** with the correct database configuration

## To Deploy on Railway

1. Railway will automatically detect the changes and redeploy
2. Go to your Railway project dashboard
3. Check the **Deployments** tab for the new build (may take 2-3 minutes)
4. Monitor the **Logs** tab to ensure migrations complete successfully
5. Ensure you have a **Volume** mounted at `/app/database` to persist the SQLite file

## Verification

In the Railway logs, you should see:
```
✅ Clearing all caches...
✅ Configuring SQLite in .env...
✅ Running migrations with SQLite...
✅ Caching configuration...
✅ Deployment complete!
```

If you still see MySQL connection errors, the config cache might have been created before the entrypoint ran. In that case, trigger a rebuild in Railway.

## Local Testing (Optional)

To test locally with Docker:
```bash
docker build -t rms:latest .
docker run -p 8080:80 -v $(pwd)/database:/app/database rms:latest
```

Then visit `http://localhost:8080`
