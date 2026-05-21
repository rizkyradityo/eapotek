# E-Apotek Installation Guide

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or MariaDB
- Node.js (optional, for frontend assets)

## Installation Steps

### 1. Clone or Download Project

```bash
cd /path/to/your/projects
cp -r e-apotek /path/to/new/location
cd e-apotek
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` file:

```env
APP_NAME=E-Apotek
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotek
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

Create a database named `apotek` in your MySQL/MariaDB server.

### 6. Run Migrations and Seeders

```bash
php artisan migrate:fresh --seed
```

This will:
- Create all database tables
- Seed sample data (users, categories, units, medicines)

### 7. Start Development Server

```bash
php artisan serve
```

Access the application at: `http://localhost:8000`

## Default Login Credentials

After seeding, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Admin |    | password |
| Apoteker | apoteker@apotek.com | password |
| Kasir | kasir@apotek.com | password |

## Features

### 1. Manajemen Obat (Medicine Management)
- CRUD operations for medicines
- Category and unit management
- Batch and expiry date tracking
- Low stock and near expiry alerts

### 2. Point of Sale (POS)
- Real-time product search
- Multiple items per transaction
- Automatic calculations
- Stock validation

### 3. Manajemen Pembelian (Purchases)
- Create purchase orders
- Receive stock
- Track supplier orders

### 4. Laporan (Reports)
- Sales reports
- Stock reports
- Stock movement history
- Dashboard summary

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── MedicineController.php
│   │   ├── TransactionController.php
│   │   ├── PurchaseController.php
│   │   └── ReportController.php
│   └── Requests/
│       ├── MedicineRequest.php
│       ├── SaleRequest.php
│       └── PurchaseRequest.php
├── Models/
│   ├── Medicine.php
│   ├── MedicineBatch.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── Purchase.php
│   ├── PurchaseItem.php
│   ├── StockMovement.php
│   ├── Category.php
│   ├── Unit.php
│   ├── Supplier.php
│   └── User.php
└── Services/
    ├── MedicineService.php
    ├── StockService.php
    ├── TransactionService.php
    ├── PurchaseService.php
    └── ReportService.php
```

## Troubleshooting

### Database Connection Error

```bash
# Check .env configuration
# Ensure database exists
php artisan migrate
```

### Migration Errors

```bash
# Reset migrations
php artisan migrate:fresh --seed
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Production Deployment

1. Set `APP_DEBUG=false` in `.env`
2. Configure proper database credentials
3. Set up queue workers (if needed)
4. Configure caching
5. Set up SSL/HTTPS

## Security Notes

- Change default password immediately
- Use strong database passwords
- Keep Laravel and dependencies updated
- Configure proper file permissions
- Use HTTPS in production

## Support

For issues or questions, refer to:
- README.md
- IMPLEMENTATION_SUMMARY.md
- QUICK_START.md