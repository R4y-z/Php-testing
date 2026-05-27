<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'code', 'customer_id', 'user_id', 'table_id', 'type', 'status',
        'payment_method', 'payment_status', 'subtotal', 'delivery_fee',
        'discount', 'total', 'notes', 'delivery_address',
        'customer_name', 'customer_phone',
        'confirmed_at', 'prepared_at', 'ready_at', 'delivered_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'prepared_at' => 'datetime',
            'ready_at' => 'datetime',
            'delivered_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->code)) {
                $order->code = strtoupper(Str::random(3)) . date('His');
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['recebido', 'confirmado', 'preparando', 'pronto']);
    }

    public function scopeDelivery($query)
    {
        return $query->where('type', 'delivery');
    }

    public function isDelivery(): bool
    {
        return $this->type === 'delivery';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'pago';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['recebido', 'confirmado']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'recebido'     => 'Recebido',
            'confirmado'   => 'Confirmado',
            'preparando'   => 'Preparando',
            'pronto'       => 'Pronto',
            'saiu_entrega' => 'Saiu para entrega',
            'finalizado'   => 'Finalizado',
            'cancelado'    => 'Cancelado',
            default        => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'recebido'     => 'bg-yellow-100 text-yellow-800',
            'confirmado'   => 'bg-blue-100 text-blue-800',
            'preparando'   => 'bg-orange-100 text-orange-800',
            'pronto'       => 'bg-green-100 text-green-800',
            'saiu_entrega' => 'bg-purple-100 text-purple-800',
            'finalizado'   => 'bg-gray-100 text-gray-800',
            'cancelado'    => 'bg-red-100 text-red-800',
            default        => 'bg-gray-100 text-gray-800',
        };
    }
}
