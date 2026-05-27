<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'stock_item_id', 'user_id', 'type', 'quantity',
        'quantity_before', 'quantity_after', 'unit_cost', 'reason', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'quantity_before' => 'decimal:3',
            'quantity_after' => 'decimal:3',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'entrada' => 'Entrada',
            'saida'   => 'Saída',
            'ajuste'  => 'Ajuste',
            default   => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'entrada' => 'text-green-600',
            'saida'   => 'text-red-600',
            'ajuste'  => 'text-blue-600',
            default   => 'text-gray-600',
        };
    }
}
