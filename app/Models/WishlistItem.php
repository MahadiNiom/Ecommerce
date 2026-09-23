<?php

namespace App\Models;

use Database\Factories\WishlistItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    /** @use HasFactory<WishlistItemFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'product_variant_id', 'product_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Name of the wishlisted product.
     */
    public function getProductNameAttribute(): string
    {
        return $this->productVariant?->product?->name
            ?? $this->product?->name
            ?? 'Unknown product';
    }

    /**
     * Human-readable option label, empty when the product has no variants.
     */
    public function getVariantLabelAttribute(): string
    {
        return $this->productVariant?->combinationLabel() ?? '';
    }

    /**
     * Unit price, e.g. "19.99", or null when unavailable.
     */
    public function getUnitPriceAttribute(): ?string
    {
        return $this->productVariant?->price ?? $this->product?->price;
    }

    /**
     * Remaining stock, or null when the product tracks no stock.
     */
    public function getStockAttribute(): ?int
    {
        return $this->productVariant?->stock ?? $this->product?->stock;
    }
}
