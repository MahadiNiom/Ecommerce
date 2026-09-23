<?php

namespace App\Models;

use Database\Factories\CartItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
     * Name of the purchased product.
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
     * Unit price of the line, e.g. "19.99", or null when unavailable.
     */
    public function getUnitPriceAttribute(): ?string
    {
        return $this->productVariant?->price ?? $this->product?->price;
    }

    /**
     * Value of this line, e.g. "12.34".
     */
    public function getLineTotalAttribute(): ?string
    {
        return $this->unit_price !== null
            ? number_format((float) $this->unit_price * $this->quantity, 2, '.', '')
            : null;
    }
}
