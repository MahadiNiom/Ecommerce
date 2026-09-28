<?php

namespace App\Models;

use App\Models\Concerns\Searchable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, Searchable;

    protected $fillable = ['name', 'price', 'stock', 'category_id', 'brand_id', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * The display price for grids and search results, or null when the product
     * cannot be priced yet.
     *
     * A product priced through its variants is shown as "From" the cheapest
     * variant, mirroring how the storefront presents it. Requires the
     * productVariants relation to be loaded.
     */
    public function priceLabel(): ?string
    {
        if ($this->productVariants->isNotEmpty()) {
            return 'From $'.number_format((float) $this->productVariants->min('price'), 2);
        }

        if ($this->price === null) {
            return null;
        }

        return '$'.number_format((float) $this->price, 2);
    }

    /**
     * Scope a query to products matching the term on their own name or on the
     * name of the category or brand they belong to.
     */
    #[Scope]
    protected function matchingSearchTerm(Builder $query, string $term): void
    {
        $query->where(function (Builder $query) use ($term): void {
            $query->matchingName($term)
                ->orWhereHas('category', fn (Builder $query) => $query->matchingName($term))
                ->orWhereHas('brand', fn (Builder $query) => $query->matchingName($term));
        });
    }
}
