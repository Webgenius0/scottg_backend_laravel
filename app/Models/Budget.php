<?php

namespace App\Models;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'name',
        'monthly',
        'annual',
    ];

    /* public static function boot()
    {
        parent::boot();

        static::saved(function ($budget) {
            $category = $budget->category;
            $category->updateBudgetSums();
        });

        static::deleted(function ($budget) {
            $category = $budget->category;
            $category->updateBudgetSums();
        });
    } */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
