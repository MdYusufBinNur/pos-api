<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'units';

    protected $guarded = [];

    public function product_stock_log()
    {
        return $this->hasOne(ProductStockLog::class);
    }
    public function purchase_request_log()
    {
        return $this->hasOne(PurchaseRequestLog::class);
    }
}
