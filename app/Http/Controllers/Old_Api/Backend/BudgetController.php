<?php

namespace App\Http\Controllers\Api\Backend;

use Exception;
use App\Models\Budget;
use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BudgetController extends Controller
{

    /* public function index(Request $request)
    {
        
        try{

            $budgets = Budget::where('user_id', auth()->id())
            ->with('category')
            ->select('id', 'name', 'monthly', 'annual')
            ->paginate(10);

            return ApiResponse::success('Budgets retrieved successfully', $budgets,);

        }
        catch(Exception $e){
            return ApiResponse::error($e->getMessage());
        }

    } */

    /* public function index(Request $request)
    {
        try {
            // Retrieve budgets for the authenticated user
            $budgets = Budget::where('user_id', auth()->id())
                ->with('category:id,name')
                ->select('id', 'type', 'name', 'monthly', 'annual', 'category_id')
                ->paginate(10);

            // Retrieve categories with budgets for the authenticated user
            $categories = Category::whereHas('budgets', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->with(['budgets' => function ($query) {
                    $query->select('id', 'name', 'monthly', 'annual', 'category_id');
                }])
                ->select('id', 'name')
                ->paginate(10);

            return ApiResponse::success('Data retrieved successfully', [
                'budgets' => $budgets,
                'categories' => $categories,
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
 */

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


    /* public function store(Request $request)
    {

        try {

            $request->validate([

                'type' => 'required|in:income,expense,planned savings,taxes',
                'name' => 'required|string',
                'monthly' => 'nullable|numeric',
                'annual' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $monthly = $request->monthly;
            $annual = $request->annual;

            if ($monthly && $annual) {
                return ApiResponse::error('You can only set either monthly or annual budget, not both.');
            }

            if ($monthly && !$annual) {
                $annual = $monthly * 12;
            } elseif ($annual && !$monthly) {
                $monthly = $annual / 12;
            }

            $budget = new Budget();
            $budget->user_id = auth()->id();
            $budget->category_id = $request->category_id;
            $budget->type = $request->type;
            $budget->name = $request->name;
            $budget->monthly = $monthly;
            $budget->annual = $annual;
            $budget->save();

            return ApiResponse::success('Budget created successfully', $budget);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    } */

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



    /* public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'type' => 'required|in:income,expense,planned savings,taxes',
                'name' => 'required|string',
                'monthly' => 'nullable|numeric',
                'annual' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $monthly = $request->monthly;
            $annual = $request->annual;

            if ($monthly && $annual) {
                return ApiResponse::error('You can only set either monthly or annual budget, not both.');
            }

            if ($monthly && !$annual) {
                $annual = $monthly * 12;
            } elseif ($annual && !$monthly) {
                $monthly = $annual / 12;
            }

            $budget = Budget::where('user_id', auth()->id())->findOrFail($id);
            $budget->category_id = $request->category_id;
            $budget->type = $request->type;
            $budget->name = $request->name;
            $budget->monthly = $monthly;
            $budget->annual = $annual;
            $budget->save();

            return ApiResponse::success('Budget updated successfully', $budget);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    } */

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

    /* public function calculateBudgetSums()
    {
        // Sum of monthly and annual budgets for each type
        $incomeMonthlySum = Budget::where('type', 'income')->sum('monthly');
        $incomeAnnualSum = Budget::where('type', 'income')->sum('annual');
        $expenseMonthlySum = Budget::where('type', 'expense')->sum('monthly');
        $expenseAnnualSum = Budget::where('type', 'expense')->sum('annual');
        $plannedSavingsMonthlySum = Budget::where('type', 'planned savings')->sum('monthly');
        $plannedSavingsAnnualSum = Budget::where('type', 'planned savings')->sum('annual');
        $taxesMonthlySum = Budget::where('type', 'taxes')->sum('monthly');
        $taxesAnnualSum = Budget::where('type', 'taxes')->sum('annual');

        // Combine results into an array
        $budgetSums = [
            'income_monthly' => $incomeMonthlySum,
            'income_annual' => $incomeAnnualSum,
            'expense_monthly' => $expenseMonthlySum,
            'expense_annual' => $expenseAnnualSum,
            'planned_savings_monthly' => $plannedSavingsMonthlySum,
            'planned_savings_annual' => $plannedSavingsAnnualSum,
            'taxes_monthly' => $taxesMonthlySum,
            'taxes_annual' => $taxesAnnualSum,
        ];

        // Return response as JSON
        return ApiResponse::success('Budget sums calculated successfully', $budgetSums);
    } */
}
