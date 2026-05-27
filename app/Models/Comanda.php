<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comanda extends Model
{
    protected $fillable = [
        'number', 'table_id', 'customer_id', 'opened_by', 'closed_by',
        'customer_name', 'status', 'total', 'discount', 'notes',
        'opened_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'discount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($comanda) {
            if (empty($comanda->opened_at)) {
                $comanda->opened_at = now();
            }
            if (empty($comanda->number)) {
                $comanda->number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        $last = self::whereDate('created_at', today())->orderByDesc('id')->first();
        if ($last) {
            $parts = explode('-', $last->number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }
        return date('Ymd') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ComandaItem::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function calculateTotal(): void
    {
        $this->total = $this->items()
            ->whereNotIn('status', ['cancelado'])
            ->sum('total') - $this->discount;
        $this->save();
    }

    public function isOpen(): bool
    {
        return $this->status === 'aberta';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aberta'     => 'Aberta',
            'fechamento' => 'Em Fechamento',
            'finalizada' => 'Finalizada',
            'cancelada'  => 'Cancelada',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aberta'     => 'bg-green-100 text-green-800',
            'fechamento' => 'bg-yellow-100 text-yellow-800',
            'finalizada' => 'bg-gray-100 text-gray-800',
            'cancelada'  => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }
}
