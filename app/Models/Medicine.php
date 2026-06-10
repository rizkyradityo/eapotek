<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'unit_id',
        'generic_name',
        'manufacturer',
        'price',
        'min_stock',
        'description',
        'composition',
        'side_effects',
        'usage_instruction',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the medicine
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the medicine
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get all batches of this medicine
     */
    public function batches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class);
    }

    /**
     * Get active batches (not expired)
     */
    public function activeBatches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class)
            ->where('is_active', true)
            ->where('expired_date', '>', now()->toDateString())
            ->where('quantity', '>', 0);
    }

    /**
     * Get stock movements for this medicine
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get stock opname items for this medicine
     */
    public function stockOpnameItems(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Get total stock from all batches
     */
    public function getTotalStockAttribute(): int
    {
        return $this->batches()->sum('quantity');
    }

    /**
     * Check if stock is below minimum
     */
    public function isBelowMinStock(): bool
    {
        return $this->total_stock < $this->min_stock;
    }

    /**
     * Scope to get active medicines
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search medicines
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('generic_name', 'like', "%{$search}%");
        });
    }
}
