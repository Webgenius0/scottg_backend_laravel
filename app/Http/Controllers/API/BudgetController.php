<?php

namespace App\Http\Controllers\API;

use App\Models\Budget;
use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;

class BudgetController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            // Retrieve categories and budgets for the authenticated user
            $categories = Category::whereHas('budgets', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->with([
                    'budgets' => function ($query) {
                        $query->select('id', 'type', 'name', 'monthly', 'annual', 'category_id');
                    }
                ])
                ->select('id', 'name', 'income_monthly', 'income_annual', 'expense_monthly', 'expense_annual', 'planned_savings_monthly', 'planned_savings_annual', 'taxes_monthly', 'taxes_annual')
                ->paginate(10);

            return ApiResponse::success('Categories and budgets retrieved successfully', $categories);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        // Start a database transaction to ensure data integrity
        DB::beginTransaction();

        try {
            $request->validate([
                'type' => 'required|in:income,expense,planned savings,taxes',
                'name' => 'required|string',
                'monthly' => 'nullable|numeric',
                'annual' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $monthly = $request->monthly ?? ($request->annual / 12);
            $annual = $request->annual ?? ($request->monthly * 12);

            $budget = Budget::create([
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'type' => $request->type,
                'name' => $request->name,
                'monthly' => $monthly,
                'annual' => $annual,
            ]);

            //commit the transaction if everything goes well
            DB::commit();

            return ApiResponse::success('Budget created successfully', $budget);
        } catch (Exception $e) {
            //rollback the transaction if something goes wrong
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        // Start a database transaction to ensure data integrity
        DB::beginTransaction();

        try {
            $request->validate([
                'type' => 'required|in:income,expense,planned savings,taxes',
                'name' => 'required|string',
                'monthly' => 'nullable|numeric',
                'annual' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $budget = Budget::where('user_id', auth()->id())->findOrFail($id);

            $monthly = $request->monthly ?? ($request->annual / 12);
            $annual = $request->annual ?? ($request->monthly * 12);

            $budget->update([
                'category_id' => $request->category_id,
                'type' => $request->type,
                'name' => $request->name,
                'monthly' => $monthly,
                'annual' => $annual,
            ]);

            //commit the transaction if everything goes well
            DB::commit();

            return ApiResponse::success('Budget updated successfully', $budget);
        } catch (Exception $e) {

            //rollback the transaction if something goes wrong
            DB::rollBack();

            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        // Start a database transaction to ensure data integrity
        DB::beginTransaction();

        try {
            $budget = Budget::where('user_id', auth()->id())->findOrFail($id);
            $budget->delete();

            //commit the transaction if everything goes well
            DB::commit();

            return ApiResponse::success('Budget deleted successfully');
        } catch (Exception $e) {

            //rollback the transaction if something goes wrong
            DB::rollBack();

            return ApiResponse::error($e->getMessage());
        }
    }

}
