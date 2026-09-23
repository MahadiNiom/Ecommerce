<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'Clothing']);
        $brand = Brand::create(['name' => 'Acme Apparel']);

        $tags = collect(['trending', 'comfort'])
            ->map(fn (string $name) => Tag::create(['name' => $name]));

        $product = Product::create([
            'name' => 'Classic T-Shirt',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'A comfortable everyday cotton t-shirt.',
        ]);
        $product->tags()->attach($tags->pluck('id'));

        $size = $product->variants()->create(['name' => 'Size']);
        $small = $size->variantOptions()->create(['name' => 'Small']);
        $medium = $size->variantOptions()->create(['name' => 'Medium']);

        $smallVariant = ProductVariant::create(['product_id' => $product->id, 'price' => 19.99, 'stock' => 10]);
        $smallVariant->variantOptions()->attach($small->id);

        $mediumVariant = ProductVariant::create(['product_id' => $product->id, 'price' => 21.99, 'stock' => 5]);
        $mediumVariant->variantOptions()->attach($medium->id);

        Product::create([
            'name' => 'Canvas Tote Bag',
            'price' => 24.99,
            'stock' => 8,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'A sturdy everyday tote with no variant options.',
        ])->tags()->attach($tags->first()->id);
    }
}
