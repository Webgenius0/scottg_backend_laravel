<?php

namespace App\Models;

use App\Models\User;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'income_monthly',
        'income_annual',
        'expense_monthly',
        'expense_annual',
        'planned_savings_monthly',
        'planned_savings_annual',
        'taxes_monthly',
        'taxes_annual'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /* public function updateBudgetSums()
    {
        $budgets = $this->budgets;

        $this->income_monthly = $budgets->where('type', 'income')->sum('monthly');
        $this->income_annual = $budgets->where('type', 'income')->sum('annual');

        $this->expense_monthly = $budgets->where('type', 'expense')->sum('monthly');
        $this->expense_annual = $budgets->where('type', 'expense')->sum('annual');

        $this->planned_savings_monthly = $budgets->where('type', 'planned savings')->sum('monthly');
        $this->planned_savings_annual = $budgets->where('type', 'planned savings')->sum('annual');

        $this->taxes_monthly = $budgets->where('type', 'taxes')->sum('monthly');
        $this->taxes_annual = $budgets->where('type', 'taxes')->sum('annual');

        $this->save();
    } */
}
