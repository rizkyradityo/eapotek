# E-Apotek Quick Start Guide

## Installation

```bash
# 1. Clone or navigate to project
cd e-apotek

# 2. Install dependencies
composer install

# 3. Configure database in .env file
cp .env.example .env
# Edit .env with your database credentials:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotek
DB_USERNAME=root
DB_PASSWORD=

# 4. Generate application key
php artisan key:generate

# 5. Run migrations and seeders
php artisan migrate:fresh --seed

# 6. Start development server
php artisan serve
```

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@apotek.com | password |
| Apoteker | apoteker@apotek.com | password |
| Kasir | kasir@apotek.com | password |

## Feature Usage

### 1. Manajemen Obat (Medicine Management)

**URL**: `/medicines`

**Features**:
- View all medicines with search and filters
- Create new medicine with batch information
- Edit existing medicine
- View low stock medicines
- View near expiry medicines

**Example Flow**:
1. Login as Admin or Apoteker
2. Navigate to "Obat" menu
3. Click "Tambah Obat"
4. Fill in medicine details:
   - Code (auto-generated or manual)
   - Name
   - Category
   - Unit
   - Price
   - Minimum stock
5. Click "Simpan"

### 2. Point of Sale (POS)

**URL**: `/pos`

**Features**:
- Search medicines by name or code
- Add multiple items to cart
- Apply discounts
- Calculate totals automatically
- Process payment

**Example Flow**:
1. Login as Kasir
2. Navigate to "POS" menu
3. Search for medicine (e.g., "Paracetamol")
4. Select medicine and batch
5. Enter quantity
6. Add to cart
7. Repeat for other items
8. Apply discount if needed
9. Enter payment amount
10. Click "Proses Transaksi"

### 3. Manajemen Pembelian (Purchases)

**URL**: `/purchases`

**Features**:
- Create purchase orders
- Add items with batch numbers
- Receive purchases (update stock)
- Cancel purchases

**Example Flow**:
1. Login as Admin or Apoteker
2. Navigate to "Pembelian" menu
3. Click "Buat Pembelian"
4. Select supplier (optional)
5. Add items:
   - Select medicine
   - Enter batch number
   - Enter expiry date
   - Enter quantity and price
6. Click "Simpan"
7. When stock arrives, click "Terima"

### 4. Laporan (Reports)

**URL**: `/reports`

**Features**:
- Sales reports with daily breakdown
- Stock reports
- Best-selling products
- Stock movement history

**Example Flow**:
1. Login with any role
2. Navigate to "Laporan" menu
3. Select report type:
   - Sales Report
   - Stock Report
   - Stock Movement
4. Apply date filters if needed
5. View or export data

## API Endpoints

### Medicine API
```bash
# Search medicines
GET /api/medicines/search?q=paracetamol

# Get stock info
GET /api/stock/1
```

### POS API
```bash
# Get available batches for medicine
GET /pos/batches/1

# Search medicine for POS
GET /pos/search?q=paracetamol

# Create sale
POST /pos/sales
{
  "items": [
    {
      "medicine_id": 1,
      "batch_id": 1,
      "quantity": 2,
      "unit_price": 2000
    }
  ],
  "paid_amount": 10000,
  "payment_method": "cash"
}
```

## Database Structure

### Core Tables
- **users**: User accounts with roles
- **categories**: Medicine categories
- **units**: Measurement units
- **medicines**: Medicine master data
- **medicine_batches**: Batch tracking with expiry
- **stock_movements**: Stock history

### Transaction Tables
- **sales**: Sales transactions
- **sale_items**: Items in sales
- **purchases**: Purchase orders
- **purchase_items**: Items in purchases
- **suppliers**: Supplier information

## Security Features

1. **Database Transactions**: All critical operations use DB transactions
2. **Form Validation**: Comprehensive validation with custom messages
3. **Role-based Access**: Different roles have different permissions
4. **Stock Validation**: Prevents negative stock
5. **Expiry Validation**: Prevents selling expired items

## Troubleshooting

### Database Connection Error
```bash
# Check .env file configuration
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

## Development Tips

### Adding New Medicine
1. Ensure category exists (or create new one)
2. Ensure unit exists (or create new one)
3. Create medicine with unique code
4. Add at least one batch with expiry date

### Processing Sale
1. Check available stock before selling
2. Select oldest batch first (FIFO)
3. Ensure payment covers total amount
4. System automatically deducts stock

### Receiving Purchase
1. Create purchase order first
2. Add items with batch numbers
3. When stock arrives, click "Terima"
4. Stock is automatically added to inventory

## File Structure Reference

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── MedicineController.php      # Medicine CRUD
│   │   ├── TransactionController.php   # POS & Sales
│   │   ├── PurchaseController.php      # Purchase management
│   │   └── ReportController.php        # Reports & Dashboard
│   └── Requests/
│       ├── MedicineRequest.php         # Medicine validation
│       ├── SaleRequest.php             # Sale validation
│       └── PurchaseRequest.php         # Purchase validation
├── Models/
│   ├── Medicine.php                    # Medicine model
│   ├── MedicineBatch.php               # Batch model
│   ├── Sale.php                        # Sale model
│   ├── SaleItem.php                    # Sale item model
│   ├── Purchase.php                    # Purchase model
│   ├── PurchaseItem.php                # Purchase item model
│   ├── StockMovement.php               # Stock history model
│   ├── Category.php                    # Category model
│   ├── Unit.php                        # Unit model
│   ├── Supplier.php                    # Supplier model
│   └── User.php                        # User model
└── Services/
    ├── MedicineService.php             # Medicine business logic
    ├── StockService.php                # Stock operations
    ├── TransactionService.php          # Sales transactions
    ├── PurchaseService.php             # Purchase operations
    └── ReportService.php               # Reporting logic
```

## Next Steps

1. **Frontend Development**: Install Inertia.js and create Vue/React components
2. **Additional Features**: 
   - Customer management
   - Supplier management
   - Expense tracking
   - Employee management
3. **Testing**: Write unit and feature tests
4. **Deployment**: Configure for production environment

## Support

For issues or questions:
- Check the ERD.md file for database structure
- Review IMPLEMENTATION_SUMMARY.md for detailed implementation
- Check routes with: `php artisan route:list`