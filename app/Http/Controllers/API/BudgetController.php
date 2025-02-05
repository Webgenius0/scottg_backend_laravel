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

    public function getIncomes(Request $request)
    {
        return $this->getEntityTotals(Income::class, $request, 'incomes');
    }

    public function getExpenses(Request $request)
    {
        return $this->getEntityTotalsForExpense(Expense::class, $request, 'expenses');
    }

    public function getSavings(Request $request)
    {
        return $this->getEntityTotals(Saving::class, $request, 'savings');
    }

    public function getTaxes(Request $request)
    {
        return $this->getEntityTotals(Tax::class, $request, 'taxes');
    }

    private function getEntityTotalsForExpense($model, $request, $entityName)
    {

        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ]);

            $defaultItems = [
                [
                    'type' => 'Home',
                    'name' => 'Mortgage/Rent',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Property Taxes',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Insurance - Home/Flood/Tenant',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Utilities - Electric/Gas/Water',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Communications - Telephone/Internet',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Home Repairs/Improvement',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Maintenance - Winter/Spring',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Lawncare/Garden',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Alarm',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Condo/Neighborhood Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Housekeeping',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Furniture/Supplies/Misc.',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Home',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Payment(s)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Maintenance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Excise Tax',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Registration',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Parking',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Gas',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Bus/Rail Card Pass',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Uber/Taxis',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Groceries',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Cell Phone',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Personal Care Items/Toiletries',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Clothing',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Hairdresser/Nails',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Household Supplies',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Dry Cleaners/Laundry',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Subscriptions - News, Music, TV, Apps, etc.',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Travel',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Dining Out',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Entertainment/Hobbies',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Shopping/Amazon',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Fitness/Health Club/Personal Trainer',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Sports',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Country Club Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Boat Expense',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Charity',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Political Contributions',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Gifts',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Health Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Dental Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Vision Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Co-pays/Deductibles',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Prescriptions (net of insurance)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Rehab/Therapy',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Other Medical Visits',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Tax Preparation',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Legal',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Financial Planning',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Life Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Disability Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Umbrella Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Jewelry Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Daycare/Preschool',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'After School Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Babysitting/Nanny',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Camps/Summer Programs',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Transport/Travel',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Toys/General Spending',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Private School Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Activities - Sports, Music, After School',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Food (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Clothing (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Healthcare/Prescriptions (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Veterinarian',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Food/Prescriptions',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Boarding Charges',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Dog Walker',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Dog Grooming',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Student Loan Debt',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Credit Card Balance (if not paid off monthly)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Home Equity Line of Credit',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Other Debt',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
                [
                    'type' => 'User Defined Other',
                    'name' => 'User Defined Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                ],
            ];

            // Fetch records from the database
            $records = $model::where('user_id', auth()->user()->id)
                ->where('year', $validated['year'])
                // ->where('month', $validated['month'])
                ->select('type', 'name', DB::raw('round(monthly_amount) as monthly_amount'), DB::raw('round(annual_amount) as annual_amount'), 'percentage_total')
                ->get()
                ->toArray();

            // Merge defaults with existing records
            $mergedRecords = collect($defaultItems)->map(function ($defaultItem) use ($records) {
                $existingRecord = collect($records)->where('type', $defaultItem['type'])->firstWhere('name', $defaultItem['name']);
                return $existingRecord ?? $defaultItem;
            });

            // Fetch totals and subtotals
            $totals = $this->getTotalsByModel($model, $validated);
            $subTotals = $this->getTotalsByType($model, $validated);

            return response()->json([
                'status' => true,
                'message' => "Successfully fetched $entityName data",
                'code' => 200,
                'data' => [
                    'totals' => $totals,
                    'subtotals' => $subTotals,
                    'records' => $mergedRecords->groupBy('type'),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generic method to fetch totals for a specific entity.
     */
    private function getEntityTotals($model, Request $request, $entityName)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ]);

            $defaultItems = match ($model) {
                Income::class => [
                    [
                        'type' => 'W2 Income (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Bonus (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'W2 Income (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Bonus (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Other Income (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Other Income (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                ],
                Saving::class => [
                    [
                        'type' => '401k Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Education/529 Plan Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Individual IRA/Roth IRA Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'HSA Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Brokerage Contributions',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Specific Goal Based Savings',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                ],
                Tax::class => [
                    [
                        'type' => 'Federal',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'State',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                    [
                        'type' => 'Other',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                    ],
                ],
            };

            // Fetch totals
            $totals = $this->getTotalsByModel($model, $validated);

            // fetch records
            $records = $model::where('user_id', auth()->user()->id)
                ->where('year', $validated['year'])
                // ->where('month', $validated['month'])
                ->select('type', DB::raw('round(monthly_amount) as monthly_amount'), DB::raw('round(annual_amount) as annual_amount'), 'percentage_total')
                ->get();

            // Merge defaults with existing records
            $mergedRecords = collect($defaultItems)->map(function ($defaultItem) use ($records) {
                $existingRecord = collect($records)->firstWhere('type', $defaultItem['type']);
                return $existingRecord ?? $defaultItem;
            });

            return response()->json([
                'status' => true,
                'message' => "Successfully fetched $entityName data",
                'code' => 200,
                'totals' => $totals,
                'records' => $mergedRecords,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch totals by model.
     */
    private function getTotalsByModel($model, $validated)
    {
        $data = $model::selectRaw('year, round(SUM(monthly_amount)) as total_monthly, round(SUM(annual_amount)) as total_annual, round(SUM(percentage_total)) as percentage_of_total')
            ->where('user_id', auth()->id())
            ->where('year', $validated['year'])
            // ->where('month', $validated['month'])
            ->groupBy('year')
            ->first();

        if (empty($data)) {
            return [
                'year' => $validated['year'],
                'total_monthly' => 0,
                'total_annual' => 0,
                'percentage_of_total' => 0,
            ];
        }

        return $data;
    }

    /**
     * Fetch totals by type.
     */
    private function getTotalsByType($model, $validated)
    {
        $data = $model::selectRaw('type, round(SUM(monthly_amount)) as total_monthly, round(SUM(annual_amount)) as total_annual, round(SUM(percentage_total)) as percentage_of_total')
            ->where('user_id', auth()->id())
            ->where('year', $validated['year'])
            // ->where('month', $validated['month'])
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $defaultTypes = [
            'Home',
            'Transport',
            'Basic Living',
            'Discretionary',
            'Medical',
            'Professional Fees',
            'Insurance',
            'Kids',
            'Pet',
            'Debt Repayments',
            'User Defined Other',
        ];

        $result = [];
        foreach ($defaultTypes as $type) {
            $result[] = [
                'type' => $type,
                'total_monthly' => $data[$type]['total_monthly'] ?? 0,
                'total_annual' => $data[$type]['total_annual'] ?? 0,
                'percentage_of_total' => $data[$type]['percentage_of_total'] ?? 0,
            ];
        }

        return $result;

        // return $data;
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
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ], $extraRules);

            $validated = $request->validate($rules);

            /* $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M'); */

            DB::beginTransaction();

            $record = $model::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'year' => $validated['year'],
                    // 'month' => $validated['month'],
                    'type' => $validated['type'],
                ] + (isset($validated['name']) ? ['name' => $validated['name']] : []),
                array_merge($validated, $this->calculateAmounts($validated))
            );

            $this->updatePercentages($model, $record->user_id);

            // Fetch the updated record from the database
            $freshRecord = $model::find($record->id);

            DB::commit();

            return response()->json([

                'success' => true,
                'message' => 'Record saved successfully',
                'code' => 200,
                'record' => $freshRecord
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Automatically calculate annual or monthly amounts.
     */
    private function calculateAmounts($data)
    {
        $calculated = [
            'monthly_amount' => $data['monthly_amount'] ?? 0,
            'annual_amount' => $data['annual_amount'] ?? 0
        ];

        if ($calculated['monthly_amount'] == 0 && $calculated['annual_amount'] > 0) {
            $calculated['monthly_amount'] = $calculated['annual_amount'] / 12;
        } elseif ($calculated['annual_amount'] == 0 && $calculated['monthly_amount'] > 0) {
            $calculated['annual_amount'] = $calculated['monthly_amount'] * 12;
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
