<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stockable_type' => ['required', Rule::in(['product', 'variant'])],
            'stockable_id' => ['required', 'integer'],
            'adjustment' => ['required', 'integer', 'not_in:0', 'min:-999999', 'max:999999'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['stockable_type', 'stockable_id', 'adjustment'])) {
                    return;
                }

                $stockableId = (int) $this->input('stockable_id');

                if ($this->input('stockable_type') === 'product') {
                    $product = Product::find($stockableId);

                    if ($product === null) {
                        $validator->errors()->add('stockable_id', 'Select a valid stockable item.');

                        return;
                    }

                    if ($product->productVariants()->exists()) {
                        $validator->errors()->add('stockable_id', 'Products with variants must be adjusted through a variant.');
                    }

                    return;
                }

                if (ProductVariant::find($stockableId) === null) {
                    $validator->errors()->add('stockable_id', 'Select a valid stockable item.');
                }
            },
        ];
    }
}
