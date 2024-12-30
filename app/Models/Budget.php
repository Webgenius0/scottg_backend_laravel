<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'name',
        'type', // Added type field
        'monthly',
        'annual',
        'category_id',
        'user_id',
    ];

    public static function boot()
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
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
