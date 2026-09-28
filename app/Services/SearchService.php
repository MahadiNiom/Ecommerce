<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SearchService
{
    /**
     * Terms shorter than this are treated as "keep typing" rather than a search,
     * so a single keystroke cannot scan the whole catalogue.
     */
    public const MIN_QUERY_LENGTH = 2;

    private const PRODUCT_LIMIT = 6;

    private const SUGGESTION_LIMIT = 3;

    /**
     * Type-ahead results for the storefront search box.
     *
     * @return array{
     *     query: string,
     *     tooShort: bool,
     *     resultsUrl: string,
     *     products: list<array{id: int, name: string, url: string, price: string, category: string|null, brand: string|null}>,
     *     categories: list<array{type: string, label: string, meta: string, url: string}>,
     *     brands: list<array{type: string, label: string, meta: string, url: string}>,
     *     tags: list<array{type: string, label: string, meta: string, url: string}>
     * }
     */
    public function search(string $term): array
    {
        $term = trim($term);
        $searchable = mb_strlen($term) >= self::MIN_QUERY_LENGTH;

        return [
            'query' => $term,
            'tooShort' => ! $searchable,
            'resultsUrl' => route('shop.index', ['q' => $term]),
            'products' => $searchable ? $this->products($term) : [],
            'categories' => $searchable ? $this->suggestions(Category::query(), 'Category', 'category', $term) : [],
            'brands' => $searchable ? $this->suggestions(Brand::query(), 'Brand', 'brand', $term) : [],
            'tags' => $searchable ? $this->suggestions(Tag::query(), 'Tag', 'tag', $term) : [],
        ];
    }

    /**
     * @return list<array{id: int, name: string, url: string, price: string, category: string|null, brand: string|null}>
     */
    private function products(string $term): array
    {
        return Product::query()
            ->with(['category', 'brand', 'productVariants'])
            ->matchingSearchTerm($term)
            ->orderBy('name')
            ->limit(self::PRODUCT_LIMIT)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('shop.show', $product),
                'price' => $product->priceLabel() ?? 'Unavailable',
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
            ])
            ->all();
    }

    /**
     * @param  Builder<Category>|Builder<Brand>|Builder<Tag>  $query
     * @return list<array{type: string, label: string, meta: string, url: string}>
     */
    private function suggestions(Builder $query, string $type, string $filter, string $term): array
    {
        return $query
            ->withCount('products')
            ->matchingName($term)
            ->orderBy('name')
            ->limit(self::SUGGESTION_LIMIT)
            ->get()
            ->map(function (Model $model) use ($type, $filter): array {
                $count = (int) $model->getAttribute('products_count');

                return [
                    'type' => $type,
                    'label' => $model->name,
                    'meta' => $count.' product'.($count === 1 ? '' : 's'),
                    'url' => route('shop.index', [$filter => $model->getKey()]),
                ];
            })
            ->all();
    }
}
