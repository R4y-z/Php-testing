<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    protected $fillable = ['number', 'name', 'capacity', 'status', 'location', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function comandas(): HasMany
    {
        return $this->hasMany(Comanda::class, 'table_id');
    }

    public function activeComanda(): HasOne
    {
        return $this->hasOne(Comanda::class, 'table_id')->whereIn('status', ['aberta', 'fechamento']);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponivel';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? 'Mesa ' . $this->number;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'disponivel' => 'bg-green-100 text-green-800 border-green-300',
            'ocupada'    => 'bg-red-100 text-red-800 border-red-300',
            'reservada'  => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'manutencao' => 'bg-gray-100 text-gray-800 border-gray-300',
            default      => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
