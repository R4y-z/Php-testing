<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'image',
        'type', 'active', 'available', 'track_stock', 'stock_quantity',
        'stock_min', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'decimal:3',
            'stock_min' => 'decimal:3',
            'active' => 'boolean',
            'available' => 'boolean',
            'track_stock' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function comandaItems(): HasMany
    {
        return $this->hasMany(ComandaItem::class);
    }

    public function stockItem(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->where('available', true);
    }

    public function isKg(): bool
    {
        return $this->type === 'kg';
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock_quantity <= $this->stock_min;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-default.jpg');
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = 'R$ ' . number_format($this->price, 2, ',', '.');
        return $this->isKg() ? $price . '/kg' : $price;
    }
}
