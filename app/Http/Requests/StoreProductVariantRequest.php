<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['required', 'integer'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
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

                $optionIds = collect($this->input('options', []));

                foreach ($optionIds as $optionId) {
                    if (! $optionToVariant->has($optionId)) {
                        $validator->errors()->add('options', "The option {$optionId} does not belong to this product.");
                    }
                }

                $variantIds = $optionIds->map(fn ($id) => $optionToVariant->get($id))->filter();

                if ($variantIds->unique()->count() !== $variantIds->count()) {
                    $validator->errors()->add('options', 'Each option must come from a different variant.');
                }
            },
        ];
    }
}
