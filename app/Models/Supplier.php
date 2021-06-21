<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product_stock_lo()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    public function purchase_request()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
