<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
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
        return [
            'product_id' => ['required_without:product_variant_id', 'nullable', 'exists:products,id'],
            'product_variant_id' => ['required_without:product_id', 'nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $productId = $this->input('product_id');
                $variantId = $this->input('product_variant_id');

                if ($productId !== null && $variantId !== null) {
                    $validator->errors()->add('product_id', 'Provide either a product or a variant, not both.');

                    return;
                }

                if ($productId === null) {
                    return;
                }

                $product = Product::find($productId);

                if ($product === null) {
                    return;
                }

                if ($product->productVariants()->exists()) {
                    $validator->errors()->add('product_id', 'This product requires choosing a variant.');

                    return;
                }

                if ($product->price === null) {
                    $validator->errors()->add('product_id', 'This product is not available for purchase.');
                }
            },
        ];
    }
}
