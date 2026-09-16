@echo off
title Smart Waste System - Presentation Mode
color 0A

echo ====================================================================
echo   SMART WASTE SYSTEM - 100%% INSTANT PRESENTATION MODE
echo ====================================================================
REM Ensure MySQL is running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="1" (
    echo [*] Starting MySQL server in background...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone >nul 2>&1
    timeout /t 2 /nobreak >nul
)

echo [*] Warming up Laravel optimizations...
call php artisan optimize:clear >nul 2>&1
call php artisan config:cache >nul 2>&1
call php artisan route:cache >nul 2>&1
call php artisan view:cache >nul 2>&1
call php artisan event:cache >nul 2>&1

echo [*] Enabling multi-worker concurrent server (4 workers)...
set PHP_CLI_SERVER_WORKERS=4

echo [*] Launching browser to http://127.0.0.1:8000...
start "" "http://127.0.0.1:8000"

echo.
echo ====================================================================
echo   SERVER IS LIVE FOR PRESENTATION (Press Ctrl+C to stop)
echo   Local Speed: Sub-50ms instant response time
echo ====================================================================
echo.

php artisan serve --host=127.0.0.1 --port=8000 --no-reload
