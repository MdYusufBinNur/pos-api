<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSaleLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product_sale()
    {
        return $this->belongsTo(ProductSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
