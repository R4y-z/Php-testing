<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['role_id', 'name', 'email', 'password', 'phone', 'active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isGarcom(): bool
    {
        return $this->hasRole(Role::GARCOM);
    }

    public function isCaixa(): bool
    {
        return $this->hasRole(Role::CAIXA);
    }

    public function isCozinha(): bool
    {
        return $this->hasRole(Role::COZINHA);
    }

    public function isDelivery(): bool
    {
        return $this->hasRole(Role::DELIVERY);
    }

    public function isCliente(): bool
    {
        return $this->hasRole(Role::CLIENTE);
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role?->slug, [
            Role::ADMIN, Role::GARCOM, Role::CAIXA, Role::COZINHA, Role::DELIVERY
        ]);
    }
}
