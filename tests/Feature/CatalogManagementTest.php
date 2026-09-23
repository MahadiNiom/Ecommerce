<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function catalogActingAdmin(): User
{
    $admin = User::factory()->create();
    $admin->givePermissionTo(Permission::findOrCreate('manage products'));

    test()->actingAs($admin);

    return $admin;
}

it('lists categories for a user with the manage products permission', function () {
    catalogActingAdmin();
    Category::create(['name' => 'Clothing']);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Clothing')
        ->assertSee('Create Category');
});

it('creates a category', function () {
    catalogActingAdmin();

    $this->post(route('categories.store'), ['name' => 'Clothing'])
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'Clothing']);
});

it('rejects creating a category with a duplicate name', function () {
    catalogActingAdmin();
    Category::create(['name' => 'Clothing']);

    $this->post(route('categories.store'), ['name' => 'Clothing'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('categories', 1);
});

it('updates a category', function () {
    catalogActingAdmin();
    $category = Category::create(['name' => 'Clothing']);

    $this->put(route('categories.update', $category), ['name' => 'Apparel'])
        ->assertRedirect(route('categories.index'));

    expect($category->refresh()->name)->toBe('Apparel');
});

it('deletes an empty category', function () {
    catalogActingAdmin();
    $category = Category::create(['name' => 'Clothing']);

    $this->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('nulls the category on its products when a category is deleted', function () {
    catalogActingAdmin();
    $category = Category::create(['name' => 'Clothing']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    $this->delete(route('categories.destroy', $category))->assertRedirect();

    expect($product->refresh()->category_id)->toBeNull();
});

it('creates a subcategory under a parent category', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Clothing']);

    $this->post(route('categories.store'), ['name' => 'Shirts', 'parent_id' => $parent->id])
        ->assertRedirect(route('categories.index'));

    expect(Category::where('name', 'Shirts')->first()->parent_id)->toBe($parent->id);
});

it('allows the same name under different parents', function () {
    catalogActingAdmin();
    $men = Category::create(['name' => 'Men']);
    $women = Category::create(['name' => 'Women']);

    $this->post(route('categories.store'), ['name' => 'Shirts', 'parent_id' => $men->id])->assertRedirect();
    $this->post(route('categories.store'), ['name' => 'Shirts', 'parent_id' => $women->id])->assertRedirect();

    expect(Category::where('name', 'Shirts')->count())->toBe(2);
});

it('rejects a duplicate name within the same parent', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Men']);
    Category::create(['name' => 'Shirts', 'parent_id' => $parent->id]);

    $this->post(route('categories.store'), ['name' => 'Shirts', 'parent_id' => $parent->id])
        ->assertSessionHasErrors('name');
});

it('rejects assigning a category to itself as its parent', function () {
    catalogActingAdmin();
    $category = Category::create(['name' => 'Shirts']);

    $this->put(route('categories.update', $category), [
        'name' => $category->name,
        'parent_id' => $category->id,
    ])->assertSessionHasErrors('parent_id');
});

it('rejects assigning a category to one of its descendants', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Men']);
    $child = Category::create(['name' => 'Shirts', 'parent_id' => $parent->id]);

    $this->put(route('categories.update', $parent), [
        'name' => $parent->name,
        'parent_id' => $child->id,
    ])->assertSessionHasErrors('parent_id');
});

it('keeps child categories when a parent is deleted', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Men']);
    $child = Category::create(['name' => 'Shirts', 'parent_id' => $parent->id]);

    $this->delete(route('categories.destroy', $parent))->assertRedirect();

    expect(Category::find($child->id)->parent_id)->toBeNull()
        ->and(Category::where('name', 'Shirts')->exists())->toBeTrue();
});

it('shows the subcategory indented under its parent on the index', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Men']);
    $child = Category::create(['name' => 'Shirts', 'parent_id' => $parent->id]);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Men', false)
        ->assertSee('└ Shirts', false);
});

it('excludes a category and its descendants from the parent options on edit', function () {
    catalogActingAdmin();
    $parent = Category::create(['name' => 'Men']);
    $child = Category::create(['name' => 'Shirts', 'parent_id' => $parent->id]);

    $this->get(route('categories.edit', $child))
        ->assertOk()
        ->assertDontSee('value="'.$child->id.'"', false);
});

it('creates a brand and a tag', function () {
    catalogActingAdmin();

    $this->post(route('brands.store'), ['name' => 'Acme'])->assertRedirect(route('brands.index'));
    $this->post(route('tags.store'), ['name' => 'trending'])->assertRedirect(route('tags.index'));

    $this->assertDatabaseHas('brands', ['name' => 'Acme']);
    $this->assertDatabaseHas('tags', ['name' => 'trending']);
});

it('forbids listing categories without the manage products permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('categories.index'))->assertForbidden();
});

it('assigns a category, brand, and tags when creating a product', function () {
    catalogActingAdmin();
    $category = Category::create(['name' => 'Clothing']);
    $brand = Brand::create(['name' => 'Acme']);
    $tag = Tag::create(['name' => 'trending']);

    $this->post(route('products.store'), [
        'name' => 'T-Shirt',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'description' => 'Comfortable.',
        'tags' => [$tag->id],
    ])->assertRedirect();

    $product = Product::where('name', 'T-Shirt')->first();

    expect($product)->not->toBeNull()
        ->and($product->category_id)->toBe($category->id)
        ->and($product->brand_id)->toBe($brand->id)
        ->and($product->description)->toBe('Comfortable.')
        ->and($product->tags()->pluck('tags.id'))->toContain($tag->id);
});

it('replaces the tags on a product when it is updated', function () {
    catalogActingAdmin();
    $oldTag = Tag::create(['name' => 'old']);
    $newTag = Tag::create(['name' => 'new']);
    $product = Product::factory()->create();
    $product->tags()->attach($oldTag->id);

    $this->put(route('products.update', $product), [
        'name' => $product->name,
        'tags' => [$newTag->id],
    ])->assertRedirect();

    expect($product->refresh()->tags()->pluck('tags.id'))->toContain($newTag->id)
        ->and($product->tags()->pluck('tags.id'))->not->toContain($oldTag->id);
});

it('stores price and stock when creating a product', function () {
    catalogActingAdmin();

    $this->post(route('products.store'), [
        'name' => 'Canvas Tote Bag',
        'price' => 24.99,
        'stock' => 8,
    ])->assertRedirect();

    $product = Product::where('name', 'Canvas Tote Bag')->first();

    expect($product->price)->toBe('24.99')
        ->and($product->stock)->toBe(8);
});

it('rejects a negative product price', function () {
    catalogActingAdmin();

    $this->post(route('products.store'), [
        'name' => 'Broken',
        'price' => -5,
    ])->assertSessionHasErrors('price');
});
