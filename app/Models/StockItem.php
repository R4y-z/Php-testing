<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    protected $fillable = [
        'product_id', 'name', 'unit', 'quantity', 'min_quantity',
        'cost_price', 'supplier', 'active',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'min_quantity' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }
}
