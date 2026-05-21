<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'medicine_id',
        'medicine_batch_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the medicine
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the batch
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(MedicineBatch::class, 'medicine_batch_id');
    }

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal(): void
    {
        $discountFromPercent = $this->unit_price * $this->quantity * ($this->discount_percent / 100);
        $this->discount_amount = $this->discount_amount + $discountFromPercent;
        $this->subtotal = ($this->unit_price * $this->quantity) - $this->discount_amount;
    }
}
