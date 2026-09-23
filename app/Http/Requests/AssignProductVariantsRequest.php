<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AssignProductVariantsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'combinations' => ['required', 'array'],
            'combinations.*.option_ids' => ['required', 'string'],
            'combinations.*.selected' => ['nullable', 'in:0,1'],
            'combinations.*.price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'combinations.*.stock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = $this->route('product');

                if ($product === null) {
                    return;
                }

                $optionToVariant = $product->variants()->with('variantOptions')->get()
                    ->mapWithKeys(fn ($variant) => $variant->variantOptions->mapWithKeys(fn ($option) => [$option->id => $variant->id]));

                foreach ($this->input('combinations', []) as $key => $data) {
                    $optionIds = collect(explode(',', (string) ($data['option_ids'] ?? '')))
                        ->map(fn ($id) => (int) $id)
                        ->filter()
                        ->values();

                    $selected = (int) ($data['selected'] ?? 0) === 1;

                    if ($optionIds->isEmpty() || $optionIds->diff($optionToVariant->keys())->isNotEmpty()) {
                        $validator->errors()->add(
                            "combinations.{$key}.option_ids",
                            'The combination contains an option that does not belong to this product.'
                        );

                        continue;
                    }

                    $variantIds = $optionIds->map(fn ($id) => $optionToVariant->get($id))->filter();

                    if ($variantIds->unique()->count() !== $variantIds->count()) {
                        $validator->errors()->add(
                            "combinations.{$key}.option_ids",
                            'Each option must come from a different variant.'
                        );
                    }

                    if ($selected && blank($data['price'])) {
                        $validator->errors()->add("combinations.{$key}.price", 'The price is required for a selected combination.');
                    }

                    if ($selected && blank($data['stock'])) {
                        $validator->errors()->add("combinations.{$key}.stock", 'The stock is required for a selected combination.');
                    }
                }
            },
        ];
    }
}
