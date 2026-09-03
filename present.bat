@echo off
title Smart Waste System - Presentation Mode
color 0A

echo ====================================================================
echo   SMART WASTE SYSTEM - 100%% INSTANT PRESENTATION MODE
echo ====================================================================
echo.
echo [*] Warming up Laravel optimizations...
call php artisan optimize:clear >nul 2>&1
call php artisan route:cache >nul 2>&1
call php artisan view:cache >nul 2>&1

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

php artisan serve --host=127.0.0.1 --port=8000
