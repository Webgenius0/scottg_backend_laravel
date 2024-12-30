<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

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

    public function index(Request $request)
    {
        try {
            // Fetch categories belonging to the authenticated user or global categories (user_id = null)
            $categories = Category::where('user_id', auth()->id())
                ->orWhereNull('user_id')
                ->orderBy('name') // Sort by name
                ->withSum('budgets as income_monthly', 'monthly')
                ->withSum('budgets as income_annual', 'annual')
                ->withSum('budgets as expense_monthly', 'monthly')
                ->withSum('budgets as expense_annual', 'annual')
                ->withSum('budgets as planned_savings_monthly', 'monthly')
                ->withSum('budgets as planned_savings_annual', 'annual')
                ->withSum('budgets as taxes_monthly', 'monthly')
                ->withSum('budgets as taxes_annual', 'annual')
                ->select('id', 'name', 'income_monthly', 'income_annual', 'expense_monthly', 'expense_annual', 'planned_savings_monthly', 'planned_savings_annual', 'taxes_monthly', 'taxes_annual')    
                ->paginate(10); // Paginate with 10 items per page

            return ApiResponse::success('Categories retrieved successfully', $categories);
        } catch (Exception $e) {
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
