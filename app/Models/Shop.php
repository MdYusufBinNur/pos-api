<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
