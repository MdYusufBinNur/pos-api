<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    use HasFactory;
    protected $table = 'user_branch';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id','id');
    }
}
