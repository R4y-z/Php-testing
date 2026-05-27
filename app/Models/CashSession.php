<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    protected $fillable = [
        'opened_by', 'closed_by', 'opening_balance', 'closing_balance',
        'expected_balance', 'difference', 'status', 'notes', 'opened_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'expected_balance' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'aberto';
    }

    public static function getCurrent(): ?self
    {
        return self::where('status', 'aberto')->latest()->first();
    }

    public function getTotalByMethod(string $method): float
    {
        return $this->payments()
            ->where('method', $method)
            ->where('status', 'aprovado')
            ->sum('amount');
    }
}
