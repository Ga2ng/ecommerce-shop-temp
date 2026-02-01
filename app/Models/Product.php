<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'reserved_stock',
        'category',
        'image',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get available stock (stock - reserved_stock)
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }

    /**
     * Check if product has available stock
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $this->available_stock >= $quantity;
    }

    /**
     * Reserve stock
     */
    public function reserveStock(int $quantity): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }
        
        $this->increment('reserved_stock', $quantity);
        return true;
    }

    /**
     * Release reserved stock
     */
    public function releaseStock(int $quantity): void
    {
        $this->decrement('reserved_stock', max(0, min($quantity, $this->reserved_stock)));
    }

    /**
     * Confirm reserved stock (move to actual stock reduction)
     */
    public function confirmReservedStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
        $this->decrement('reserved_stock', $quantity);
    }
}

