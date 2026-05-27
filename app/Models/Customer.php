<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'cpf', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    public function defaultAddress(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class)->where('is_default', true);
    }

    public function comandas(): HasMany
    {
        return $this->hasMany(Comanda::class);
    }
}
