<?php

namespace App\Http\Controllers\Api\Backend;

use Exception;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    // Retrieve transactions for the authenticated user
    public function index(Request $request)
    {
        try {
            $transactions = Transaction::where('user_id', auth()->id())
                ->with(['budget:id,name', 'category:id,name']) // Include related budget and category
                ->select('id', 'budget_id', 'category_id', 'amount', 'description', 'type', 'date')
                ->orderBy('date', 'desc')
                ->paginate(10);

            return ApiResponse::success('Transactions retrieved successfully', $transactions);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // Create a new transaction
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'budget_id' => 'required|exists:budgets,id',
                'category_id' => 'nullable|exists:categories,id',
                'amount' => 'required|numeric',
                'description' => 'nullable|string|max:255',
                'type' => 'required|in:income,expense',
                'date' => 'required|date',
            ]);

            // Ensure the budget belongs to the authenticated user
            $budget = Budget::where('user_id', auth()->id())->findOrFail($request->budget_id);

            // Ensure the category, if provided, belongs to the authenticated user
            $category = $request->category_id
                ? Category::where('user_id', auth()->id())->findOrFail($request->category_id)
                : null;

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'budget_id' => $budget->id,
                'category_id' => $category?->id,
                'amount' => $request->amount,
                'description' => $request->description,
                'type' => $request->type,
                'date' => $request->date,
            ]);

            DB::commit();

            return ApiResponse::success('Transaction created successfully', $transaction);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    // Update an existing transaction
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'budget_id' => 'required|exists:budgets,id',
                'category_id' => 'nullable|exists:categories,id',
                'amount' => 'required|numeric',
                'description' => 'nullable|string|max:255',
                'type' => 'required|in:income,expense',
                'date' => 'required|date',
            ]);

            $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);

            // Ensure the budget belongs to the authenticated user
            $budget = Budget::where('user_id', auth()->id())->findOrFail($request->budget_id);

            // Ensure the category, if provided, belongs to the authenticated user
            $category = $request->category_id
                ? Category::where('user_id', auth()->id())->findOrFail($request->category_id)
                : null;

            $transaction->update([
                'budget_id' => $budget->id,
                'category_id' => $category?->id,
                'amount' => $request->amount,
                'description' => $request->description,
                'type' => $request->type,
                'date' => $request->date,
            ]);

            DB::commit();

            return ApiResponse::success('Transaction updated successfully', $transaction);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    // Delete a transaction
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);
            $transaction->delete();

            DB::commit();

            return ApiResponse::success('Transaction deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
}
