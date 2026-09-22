<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    //
    protected $fillable = ['product_id', 'variant_id', 'variant_option_id', 'price', 'stock'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
    public function variantOption()
    {
        return $this->belongsTo(VariantOption::class);
    }
    
}
