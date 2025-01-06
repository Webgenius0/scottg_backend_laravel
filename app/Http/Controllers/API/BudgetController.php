<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Income;
use App\Models\Saving;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BudgetController extends Controller
{

    public function getTotals(Request $request)
    {
        try {

            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|string',
            ]);

            $incomeTotals = Income::selectRaw('year, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as total_percentage')
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->groupBy('year')
                ->first();

            $expenseTotals = Expense::selectRaw('year, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as total_percentage')
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->groupBy('year')
                ->first();

            $expenseTotalsByType = Expense::selectRaw('type, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as total_percentage')
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->groupBy( 'type')
                ->get();

            $savingTotals = Saving::selectRaw('year, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as total_percentage')
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->groupBy('year')
                ->first();

            $taxTotals = Tax::selectRaw('year, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as total_percentage')
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->groupBy('year')
                ->first();

            return response()->json([
                'incomes' => $incomeTotals,
                'expenses' => $expenseTotals,
                'subtotal_expenses' => $expenseTotalsByType,
                'savings' => $savingTotals,
                'taxes' => $taxTotals,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store or update an income record.
     */
    public function saveIncome(Request $request)
    {
        try {

            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json([
                    'error' => 'You can only enter either monthly or annual amount'
                ], 400);
            }

            $validated = $request->validate([
                'type' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
            ]);

            // Set year and month automatically
            $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M');

            DB::beginTransaction();

            try {
                $income = Income::updateOrCreate(
                    ['user_id' => auth()->id(), 'year' => $validated['year'], 'month' => $validated['month'], 'type' => $validated['type']],
                    array_merge($validated, $this->calculateAmounts($validated))
                );

                $this->updatePercentages(Income::class, $income->user_id);

                DB::commit();

                return response()->json($income, 200);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update an expense record.
     */
    public function saveExpense(Request $request)
    {
        try {

            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json([
                    'error' => 'You can only enter either monthly or annual amount'
                ], 400);
            }

            $validated = $request->validate([
                'type' => 'required|string',
                'name' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
            ]);

            // Set year and month automatically
            $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M');

            DB::beginTransaction();

            try {

                $expense = Expense::updateOrCreate(
                    ['user_id' => auth()->id(), 'year' => $validated['year'], 'month' => $validated['month'], 'type' => $validated['type'], 'name' => $validated['name']],
                    array_merge($validated, $this->calculateAmounts($validated))
                );

                $this->updatePercentages(Expense::class, $expense->user_id);

                DB::commit();

                return response()->json($expense, 200);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update a saving record.
     */
    public function saveSaving(Request $request)
    {
        try {

            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json([
                    'error' => 'You can only enter either monthly or annual amount'
                ], 400);
            }

            $validated = $request->validate([
                'type' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
            ]);

            // Set year and month automatically
            $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M');

            DB::beginTransaction();

            try {
                $saving = Saving::updateOrCreate(
                    ['user_id' => auth()->id(), 'year' => $validated['year'], 'month' => $validated['month'], 'type' => $validated['type']],
                    array_merge($validated, $this->calculateAmounts($validated))
                );

                $this->updatePercentages(Saving::class, $saving->user_id);

                DB::commit();

                return response()->json($saving, 200);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update a tax record.
     */
    public function saveTax(Request $request)
    {
        try {

            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json([
                    'error' => 'You can only enter either monthly or annual amount'
                ], 400);
            }

            $validated = $request->validate([
                'type' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
            ]);

            // Set year and month automatically
            $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M');

            DB::beginTransaction();

            try {

                $tax = Tax::updateOrCreate(
                    ['user_id' => auth()->id(), 'year' => $validated['year'], 'month' => $validated['month'], 'type' => $validated['type']],
                    array_merge($validated, $this->calculateAmounts($validated))
                );

                $this->updatePercentages(Tax::class, $tax->user_id);

                DB::commit();

                return response()->json($tax, 200);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Automatically calculate annual or monthly amounts.
     */
    private function calculateAmounts($data)
    {
        $calculated = [];
        if (isset($data['monthly_amount']) && $data['monthly_amount'] > 0) {
            $calculated['annual_amount'] = $data['monthly_amount'] * 12;
        } elseif (isset($data['annual_amount']) && $data['annual_amount'] > 0) {
            $calculated['monthly_amount'] = $data['annual_amount'] / 12;
        }

        return $calculated;
    }

    /**
     * Update percentage_total for incomes or expenses.
     */
    private function updatePercentages($model, $userId)
    {
        $records = $model::where('user_id', $userId)->get();
        $totalMonthly = $records->sum('monthly_amount');
        $totalAnnual = $records->sum('annual_amount');

        foreach ($records as $record) {
            $record->update([
                'percentage_total' => $totalMonthly > 0
                    ? ($record->monthly_amount / $totalMonthly) * 100
                    : 0,
            ]);
        }
    }
}
