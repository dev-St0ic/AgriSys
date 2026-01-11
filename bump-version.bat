@echo off
REM ================================================
REM AgriSys Cache Busting Deployment Script (Windows)
REM ================================================
REM This script automatically updates the asset version
REM and deploys your changes
REM ================================================

echo ========================================
echo AgriSys Cache Busting Deployment
echo ========================================
echo.

REM Get current version
for /f "tokens=2 delims==" %%a in ('findstr "^ASSET_VERSION=" .env') do set CURRENT_VERSION=%%a
echo Current version: %CURRENT_VERSION%
echo.

REM Ask for new version
set /p NEW_VERSION="Enter new version (or press Enter for timestamp): "

REM Use timestamp if no version provided
if "%NEW_VERSION%"=="" (
    for /f "tokens=2-4 delims=/ " %%a in ('date /t') do set DATESTR=%%c%%a%%b
    for /f "tokens=1-2 delims=: " %%a in ('time /t') do set TIMESTR=%%a%%b
    set NEW_VERSION=%DATESTR%-%TIMESTR%
    echo Using timestamp version: !NEW_VERSION!
)

echo.
echo Updating version to: %NEW_VERSION%

REM Update .env file
powershell -Command "(Get-Content .env) -replace '^ASSET_VERSION=.*', 'ASSET_VERSION=%NEW_VERSION%' | Set-Content .env"
echo [DONE] Updated .env

REM Update .env.production file
powershell -Command "(Get-Content .env.production) -replace '^ASSET_VERSION=.*', 'ASSET_VERSION=%NEW_VERSION%' | Set-Content .env.production"
echo [DONE] Updated .env.production

REM Clear config cache
php artisan config:clear
echo [DONE] Cleared config cache

echo.
echo ========================================
echo Version updated successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Test locally
echo 2. Commit: git add . ^&^& git commit -m "Bump version to %NEW_VERSION%"
echo 3. Push: git push
echo 4. Users will automatically get new version!
echo.
pause
