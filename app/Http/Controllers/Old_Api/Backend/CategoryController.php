<?php

namespace App\Http\Controllers\Api\Backend;

use Exception;
use App\Models\Budget;
use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{

    /* public function index(Request $request)
    {
        try {

            $categories = Category::where('user_id', auth()->id())
                ->orWhereNull('user_id')
                ->orderBy('name')
                ->withSum('budgets', 'monthly')
                ->withSum('budgets', 'annual')
                ->select('id', 'name', 'monthly', 'annual')
                ->paginate(10);

            return ApiResponse::success('Categories retrieved successfully', $categories);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    } */

    /* public function index(Request $request)
    {
        try {
            // Fetch categories belonging to the authenticated user or global categories (user_id = null)
            $categories = Category::where('user_id', auth()->id())
                ->orWhereNull('user_id')
                ->orderBy('name') // Sort by name
                ->withSum('budgets as income_monthly', 'monthly')->where('type', 'income')
                ->withSum('budgets as income_annual', 'annual')->where('type', 'income')
                ->withSum('budgets as expense_monthly', 'monthly')->where('type', 'expense')
                ->withSum('budgets as expense_annual', 'annual')->where('type', 'expense')
                ->withSum('budgets as planned_savings_monthly', 'monthly')->where('type', 'planned savings')
                ->withSum('budgets as planned_savings_annual', 'annual')->where('type', 'planned savings')
                ->withSum('budgets as taxes_monthly', 'monthly')->where('type', 'taxes')
                ->withSum('budgets as taxes_annual', 'annual')->where('type', 'taxes')
                ->select('id', 'name', 'income_monthly', 'income_annual', 'expense_monthly', 'expense_annual', 'planned_savings_monthly', 'planned_savings_annual', 'taxes_monthly', 'taxes_annual')
                ->paginate(10); // Paginate with 10 items per page

            return ApiResponse::success('Categories retrieved successfully', $categories);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    } */

    public function index(Request $request)
    {
        try {
            // Calculate sums grouped by type for all budgets
            $budgetSums = Budget::select(
                'type',
                DB::raw('SUM(monthly) as monthly_sum'),
                DB::raw('SUM(annual) as annual_sum')
            )
                ->groupBy('type') // Group by the type of budget (income, expense, etc.)
                ->get();

            // Organize sums into a structured array
            $sums = [
                'income' => [
                    'monthly' => $budgetSums->where('type', 'income')->pluck('monthly_sum')->first() ?? 0,
                    'annual' => $budgetSums->where('type', 'income')->pluck('annual_sum')->first() ?? 0,
                ],
                'expense' => [
                    'monthly' => $budgetSums->where('type', 'expense')->pluck('monthly_sum')->first() ?? 0,
                    'annual' => $budgetSums->where('type', 'expense')->pluck('annual_sum')->first() ?? 0,
                ],
                'planned_savings' => [
                    'monthly' => $budgetSums->where('type', 'planned savings')->pluck('monthly_sum')->first() ?? 0,
                    'annual' => $budgetSums->where('type', 'planned savings')->pluck('annual_sum')->first() ?? 0,
                ],
                'taxes' => [
                    'monthly' => $budgetSums->where('type', 'taxes')->pluck('monthly_sum')->first() ?? 0,
                    'annual' => $budgetSums->where('type', 'taxes')->pluck('annual_sum')->first() ?? 0,
                ],
            ];

            // Fetch categories for authenticated user or global ones
            $categories = Category::where('user_id', auth()->id())
                ->orWhereNull('user_id')
                ->select('id', 'name')
                ->paginate(10); // Paginate with 10 items per page

            // Return data with the calculated sums
            return ApiResponse::success('Categories retrieved successfully', [
                'categories' => $categories,
                'total' => $sums,
            ]);
        } catch (Exception $e) {
            // Handle exceptions
            return ApiResponse::error($e->getMessage());
        }
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);

        try {

            $category = new Category();
            $category->user_id = auth()->id();
            $category->name = $request->name;
            $category->save();

            return ApiResponse::success($category);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
