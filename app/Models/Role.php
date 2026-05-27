<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    const ADMIN    = 'admin';
    const GARCOM   = 'garcom';
    const CAIXA    = 'caixa';
    const COZINHA  = 'cozinha';
    const DELIVERY = 'delivery';
    const CLIENTE  = 'cliente';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
