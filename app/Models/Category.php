<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * A flattened list of all categories ordered as a tree, each with a depth attribute.
     *
     * @return Collection<int, Category>
     */
    public static function tree(): Collection
    {
        $byParent = static::withCount('products')->orderBy('name')->get()->groupBy('parent_id');
        $result = collect();

        $walk = function (int|string|null $parentId, int $depth) use (&$walk, &$result, $byParent): void {
            foreach ($byParent->get($parentId, collect()) as $category) {
                $category->depth = $depth;
                $result->push($category);
                $walk($category->id, $depth + 1);
            }
        };

        $walk(null, 0);

        return $result;
    }

    /**
     * IDs of this category and every category nested beneath it.
     *
     * @return list<int>
     */
    public function subtreeIds(): array
    {
        $childrenByParent = Category::query()->get(['id', 'parent_id'])->groupBy('parent_id');
        $ids = [$this->id];
        $queue = [$this->id];

        while ($queue !== []) {
            $id = array_shift($queue);

            foreach ($childrenByParent->get($id, collect()) as $child) {
                $ids[] = $child->id;
                $queue[] = $child->id;
            }
        }

        return $ids;
    }
}
