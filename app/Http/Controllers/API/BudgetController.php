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

            $data = [
                'incomes' => $this->getTotalsByModel(Income::class, $validated),
                'expenses' => $this->getTotalsByModel(Expense::class, $validated),
                'subtotal_expenses' => $this->getTotalsByType(Expense::class, $validated),
                'savings' => $this->getTotalsByModel(Saving::class, $validated),
                'taxes' => $this->getTotalsByModel(Tax::class, $validated),
            ];

            // Fetch individual records for incomes, expenses, savings, and taxes
            $data['incomes_records'] = Income::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->select('type', 'monthly_amount', 'annual_amount', 'percentage_total')
                ->get();

            $data['expenses_records'] = Expense::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->select('type', 'name', 'monthly_amount', 'annual_amount', 'percentage_total')
                ->get();

            $data['savings_records'] = Saving::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->select('type', 'monthly_amount', 'annual_amount', 'percentage_total')
                ->get();

            $data['taxes_records'] = Tax::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->select('type', 'monthly_amount', 'annual_amount', 'percentage_total')
                ->get();

            // Calculations
            $grossIncome = $data['incomes']->total_annual ?? 0;
            $totalExpenses = $data['expenses']->total_annual ?? 0;
            $totalSavings = $data['savings']->total_annual ?? 0;
            $totalTaxes = $data['taxes']->total_annual ?? 0;
            $netIncome = $grossIncome - $totalTaxes;
            $yearlyExcessShortfall = $netIncome - ($totalExpenses + $totalSavings);
            $monthlyExcessShortfall = $yearlyExcessShortfall / 12;

            // Add to response
            $data['gross_income'] = round($grossIncome, 2);
            $data['total_taxes'] = $totalTaxes;
            $data['net_income'] = $netIncome;
            $data['total_expenses'] = $totalExpenses;
            $data['total_savings'] = $totalSavings;
            $data['yearly_excess_shortfall'] = round($yearlyExcessShortfall, 2);
            $data['monthly_excess_shortfall'] = round($monthlyExcessShortfall, 2);

            return response()->json([

                'status' => true,
                'message' => 'Successfully fetched data',
                'code' => 200,
                'data' => $data

            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveIncome(Request $request)
    {
        return $this->saveRecord(Income::class, $request);
    }

    public function saveExpense(Request $request)
    {
        return $this->saveRecord(Expense::class, $request, ['name' => 'required|string']);
    }

    public function saveSaving(Request $request)
    {
        return $this->saveRecord(Saving::class, $request);
    }

    public function saveTax(Request $request)
    {
        return $this->saveRecord(Tax::class, $request);
    }

    /**
     * Generic method to save a record.
     */
    private function saveRecord($model, Request $request, array $extraRules = [])
    {
        try {
            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json(['error' => 'You can only enter either monthly or annual amount'], 400);
            }

            $rules = array_merge([
                'type' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
            ], $extraRules);

            $validated = $request->validate($rules);

            $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M');

            DB::beginTransaction();

            $record = $model::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'year' => $validated['year'],
                    'month' => $validated['month'],
                    'type' => $validated['type'],
                ] + (isset($validated['name']) ? ['name' => $validated['name']] : []),
                array_merge($validated, $this->calculateAmounts($validated))
            );

            $this->updatePercentages($model, $record->user_id);

            // Fetch the updated record from the database
            $freshRecord = $model::find($record->id);

            DB::commit();

            return response()->json($freshRecord, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch totals by model.
     */
    private function getTotalsByModel($model, $validated)
    {
        return $model::selectRaw('year, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as percentage_of_total')
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->groupBy('year')
            ->first();
    }

    /**
     * Fetch totals by type.
     */
    private function getTotalsByType($model, $validated)
    {
        return $model::selectRaw('type, SUM(monthly_amount) as total_monthly, SUM(annual_amount) as total_annual, SUM(percentage_total) as percentage_of_total')
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->groupBy('type')
            ->get();
    }

    /**
     * Automatically calculate annual or monthly amounts.
     */
    private function calculateAmounts($data)
    {
        $calculated = [];
        if (!empty($data['monthly_amount'])) {
            $calculated['annual_amount'] = $data['monthly_amount'] * 12;
        } elseif (!empty($data['annual_amount'])) {
            $calculated['monthly_amount'] = $data['annual_amount'] / 12;
        }
        return $calculated;
    }

    /**
     * Update percentages.
     */
    private function updatePercentages($model, $userId)
    {
        $totals = $model::where('user_id', $userId)->sum('annual_amount');

        $model::where('user_id', $userId)->each(function ($record) use ($totals) {
            $record->update(['percentage_total' => $totals > 0 ? ($record->annual_amount / $totals) * 100 : 0]);
        });
    }
}
