<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAddress extends Model
{
    protected $fillable = [
        'customer_id', 'label', 'street', 'number', 'complement',
        'neighborhood', 'city', 'state', 'zip_code', 'reference', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFullAddressAttribute(): string
    {
        $address = "{$this->street}, {$this->number}";
        if ($this->complement) $address .= " - {$this->complement}";
        $address .= ", {$this->neighborhood}, {$this->city}/{$this->state}";
        if ($this->zip_code) $address .= " - CEP: {$this->zip_code}";
        return $address;
    }
}
