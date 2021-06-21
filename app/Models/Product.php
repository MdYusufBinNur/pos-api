<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function product_stock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function product_stock_log()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    public function purchase_request_lo()
    {
        return $this->hasMany(PurchaseRequestLog::class);
    }

}
