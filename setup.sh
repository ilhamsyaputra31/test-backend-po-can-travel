#!/bin/bash

echo "========================================="
echo "PO CAN Travel - Setup Script"
echo "========================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

echo "Installing Composer dependencies..."
composer install

echo ""
echo "========================================="
echo "Database Setup"
echo "========================================="
echo ""
echo "Please ensure MySQL is running and you have created the database 'po_can_travel'"
echo "You can create it with: CREATE DATABASE po_can_travel;"
echo ""
read -p "Press Enter to continue after creating the database..."

echo ""
echo "Running migrations..."
php artisan migrate:fresh --seed

echo ""
echo "========================================="
echo "Setup Complete!"
echo "========================================="
echo ""
echo "Getting API Key from database..."
API_KEY=$(php artisan tinker --execute="echo App\Models\ApiKey::first()->api_key;")
echo ""
echo "Your API Key: $API_KEY"
echo ""
echo "Test Credentials:"
echo "  Admin:"
echo "    Email: admin@pocantravel.com"
echo "    Password: password"
echo ""
echo "  Customer:"
echo "    Email: customer@example.com"
echo "    Password: password"
echo ""
echo "To start the server, run:"
echo "  php artisan serve"
echo ""
echo "API will be available at: http://localhost:8000/api"
echo ""
echo "========================================="
