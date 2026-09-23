<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
        $nameRule = Rule::unique(Category::class, 'name');

        if ($this->input('parent_id') !== null) {
            $nameRule->where('parent_id', $this->input('parent_id'));
        } else {
            $nameRule->whereNull('parent_id');
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }
}
