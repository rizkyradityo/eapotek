<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineBatch extends Model
{
    protected $fillable = [
        'medicine_id',
        'batch_number',
        'expired_date',
        'quantity',
        'purchase_price',
        'selling_price',
        'manufacture_date',
        'is_active',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'manufacture_date' => 'date',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the medicine that owns this batch
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get sale items for this batch
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Check if batch is expired
     */
    public function isExpired(): bool
    {
        return $this->expired_date->isPast();
    }

    /**
     * Check if batch is near expiry (within 30 days)
     */
    public function isNearExpiry(): bool
    {
        return $this->expired_date->diffInDays(now()) <= 30 && !$this->isExpired();
    }

    /**
     * Scope to get active batches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get non-expired batches
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expired_date', '>', now()->toDateString());
    }

    /**
     * Scope to get expired batches
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_date', '<=', now()->toDateString());
    }
}
