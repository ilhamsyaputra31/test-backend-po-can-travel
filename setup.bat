@echo off
echo =========================================
echo PO CAN Travel - Setup Script
echo =========================================
echo.

REM Check if .env exists
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
    php artisan key:generate
)

echo Installing Composer dependencies...
call composer install

echo.
echo =========================================
echo Database Setup
echo =========================================
echo.
echo Please ensure MySQL is running and you have created the database 'po_can_travel'
echo You can create it with: CREATE DATABASE po_can_travel;
echo.
pause

echo.
echo Running migrations...
php artisan migrate:fresh --seed

echo.
echo =========================================
echo Setup Complete!
echo =========================================
echo.
echo Test Credentials:
echo   Admin:
echo     Email: admin@pocantravel.com
echo     Password: password
echo.
echo   Customer:
echo     Email: customer@example.com
echo     Password: password
echo.
echo To get your API Key, run:
echo   php artisan tinker
echo   App\Models\ApiKey::first()-^>api_key
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo API will be available at: http://localhost:8000/api
echo.
echo =========================================
pause
