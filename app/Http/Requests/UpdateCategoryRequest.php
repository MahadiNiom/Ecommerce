<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $parentId = $this->input('parent_id');
        $nameRule = Rule::unique(Category::class, 'name')->ignore($this->route('category'));

        if ($parentId !== null) {
            $nameRule->where('parent_id', $parentId);
        } else {
            $nameRule->whereNull('parent_id');
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $category = $this->route('category');
                $parentId = $this->input('parent_id');

                if ($category !== null && $parentId !== null && in_array((int) $parentId, $category->subtreeIds(), true)) {
                    $validator->errors()->add('parent_id', 'A category cannot be its own parent or a descendant.');
                }
            },
        ];
    }
}
