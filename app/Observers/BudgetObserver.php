<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Category;

class BudgetObserver
{
    /**
     * Handle the Budget "created" or "updated" or "deleted" events.
     *
     * @param  \App\Models\Budget  $budget
     * @return void
     */
    public function saved(Budget $budget)
    {
        $this->updateCategorySums($budget->category_id);
    }

    public function deleted(Budget $budget)
    {
        $this->updateCategorySums($budget->category_id);
    }

    /**
     * Update the category sums dynamically.
     *
     * @param  int  $categoryId
     * @return void
     */
    protected function updateCategorySums($categoryId)
    {
        $category = Category::find($categoryId);

        if ($category) {
            $category->income_monthly = $category->budgets()->where('type', 'income')->sum('monthly');
            $category->income_annual = $category->budgets()->where('type', 'income')->sum('annual');
            $category->expense_monthly = $category->budgets()->where('type', 'expense')->sum('monthly');
            $category->expense_annual = $category->budgets()->where('type', 'expense')->sum('annual');
            $category->planned_savings_monthly = $category->budgets()->where('type', 'planned savings')->sum('monthly');
            $category->planned_savings_annual = $category->budgets()->where('type', 'planned savings')->sum('annual');
            $category->taxes_monthly = $category->budgets()->where('type', 'taxes')->sum('monthly');
            $category->taxes_annual = $category->budgets()->where('type', 'taxes')->sum('annual');
            $category->save();
        }
    }
}
