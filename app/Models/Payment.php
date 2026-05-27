<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'code', 'payable_type', 'payable_id', 'cash_session_id', 'processed_by',
        'method', 'status', 'amount', 'change_amount', 'cash_received',
        'reference', 'notes', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'cash_received' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($payment) {
            if (empty($payment->code)) {
                $payment->code = 'PAY-' . strtoupper(Str::random(8));
            }
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match($this->method) {
            'dinheiro'       => 'Dinheiro',
            'pix'            => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito'  => 'Cartão de Débito',
            'misto'          => 'Misto',
            default          => $this->method,
        };
    }
}
