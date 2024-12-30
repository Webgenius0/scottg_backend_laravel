<?php

namespace App\Models;

use App\Models\User;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
