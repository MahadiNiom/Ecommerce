<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::tree();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create', [
            'category' => new Category,
            'parents' => Category::tree(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::tree()->reject(fn (Category $candidate) => in_array($candidate->id, $category->subtreeIds(), true));

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
