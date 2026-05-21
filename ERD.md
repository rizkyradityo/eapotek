# ERD Sederhana E-Apotek

## Entity Relationship Diagram

```
┌─────────────────┐
│     Users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ role            │ ← admin/apoteker/kasir
│ phone           │
│ address         │
│ is_active       │
└────────┬────────┘
         │
         ├──────────────────────────────────────────┐
         │                                          │
         ▼                                          ▼
┌─────────────────┐                    ┌─────────────────┐
│     Sales       │                    │   Purchases     │
├─────────────────┤                    ├─────────────────┤
│ id (PK)         │                    │ id (PK)         │
│ invoice_number  │                    │ invoice_number  │
│ user_id (FK)    │                    │ user_id (FK)    │
│ customer_id     │                    │ supplier_id     │
│ subtotal        │                    │ subtotal        │
│ discount        │                    │ discount        │
│ total           │                    │ total           │
│ payment_method  │                    │ payment_status  │
│ status          │                    │ status          │
└────────┬────────┘                    └────────┬────────┘
         │                                      │
         ▼                                      ▼
┌─────────────────┐                    ┌─────────────────┐
│   SaleItems     │                    │  PurchaseItems  │
├─────────────────┤                    ├─────────────────┤
│ id (PK)         │                    │ id (PK)         │
│ sale_id (FK)    │                    │ purchase_id (FK)│
│ medicine_id     │                    │ medicine_id     │
│ batch_id        │                    │ batch_number    │
│ quantity        │                    │ expired_date    │
│ unit_price      │                    │ quantity        │
│ subtotal        │                    │ unit_price      │
└────────┬────────┘                    └────────┬────────┘
         │                                      │
         └──────────────┬───────────────────────┘
                        ▼
               ┌─────────────────┐
               │    Medicines    │
               ├─────────────────┤
               │ id (PK)         │
               │ code            │
               │ name            │
               │ category_id (FK)│
               │ unit_id (FK)    │
               │ price           │
               │ min_stock       │
               └────────┬────────┘
                        │
         ┌──────────────┴──────────────┐
         ▼                             ▼
┌─────────────────┐          ┌─────────────────┐
│   Categories    │          │      Units      │
├─────────────────┤          ├─────────────────┤
│ id (PK)         │          │ id (PK)         │
│ name            │          │ name            │
│ description     │          │ symbol          │
└─────────────────┘          └─────────────────┘

         ┌───────────────────────────────────────┐
         │                                       │
         ▼                                       ▼
┌─────────────────┐                    ┌─────────────────┐
│ MedicineBatches │                    │ StockMovements  │
├─────────────────┤                    ├─────────────────┤
│ id (PK)         │                    │ id (PK)         │
│ medicine_id (FK)│                    │ medicine_id (FK)│
│ batch_number    │                    │ batch_id        │
│ expired_date    │                    │ type            │
│ quantity        │                    │ quantity        │
│ purchase_price  │                    │ previous_stock  │
│ selling_price   │                    │ new_stock       │
│ is_active       │                    │ reference_type  │
└─────────────────┘                    │ reference_id    │
                                       │ user_id (FK)    │
                                       └─────────────────┘

         ┌─────────────────┐
         │   Suppliers     │
         ├─────────────────┤
         │ id (PK)         │
         │ code            │
         │ name            │
         │ contact_person  │
         │ phone           │
         │ email           │
         │ address         │
         └─────────────────┘
```

## Relationships

### Users
- `hasMany(Sale::class)` - User can create sales
- `hasMany(Purchase::class)` - User can create purchases
- `hasMany(StockMovement::class)` - User can make stock movements

### Medicines
- `belongsTo(Category::class)` - Medicine belongs to one category
- `belongsTo(Unit::class)` - Medicine has one unit
- `hasMany(MedicineBatch::class)` - Medicine has many batches
- `hasMany(StockMovement::class)` - Medicine has stock movements

### Sales
- `belongsTo(User::class)` - Sale created by user (cashier)
- `belongsTo(User::class, 'customer_id')` - Optional customer
- `hasMany(SaleItem::class)` - Sale has many items

### Purchases
- `belongsTo(User::class)` - Purchase created by user
- `belongsTo(Supplier::class)` - Purchase from supplier
- `hasMany(PurchaseItem::class)` - Purchase has many items

### Medicine Batches
- `belongsTo(Medicine::class)` - Batch belongs to medicine
- `hasMany(StockMovement::class)` - Batch has stock movements

## Key Business Rules

1. **Stock Validation**: Stock cannot go negative
2. **Batch Expiry**: Expired batches cannot be sold
3. **Atomic Transactions**: Sales and stock updates are transactional
4. **Role-based Access**: Different roles have different permissions
5. **Minimum Stock Alert**: Alert when stock below minimum threshold