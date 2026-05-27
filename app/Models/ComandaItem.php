<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandaItem extends Model
{
    protected $fillable = [
        'comanda_id', 'product_id', 'added_by', 'product_name',
        'unit_price', 'quantity', 'unit', 'total', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'decimal:3',
            'total' => 'decimal:2',
        ];
    }

    public function comanda(): BelongsTo
    {
        return $this->belongsTo(Comanda::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getFormattedQuantityAttribute(): string
    {
        if ($this->unit === 'kg') {
            return number_format($this->quantity, 3, ',', '.') . ' kg';
        }
        return intval($this->quantity) . ' un';
    }
}
