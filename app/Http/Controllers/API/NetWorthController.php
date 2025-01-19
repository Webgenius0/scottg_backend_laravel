<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\NetWorth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class NetWorthController extends Controller
{

    /**
     * Get asset and liability subtotals and net worth.
     *

     * @return JsonResponse
     */



    /* public function getNetWorth(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
            ]);

            // Group net worth records by year and map by type
            $netWorths = NetWorth::where('user_id', auth()->id())
                ->where('year', $validated['year'])
                ->get()
                ->groupBy('year')
                ->map(function ($records) {
                    return $records->sortBy(function ($record) {
                        $order = [
                            'liquid assets',
                            'taxable financial assets',
                            'tax-deferred assets',
                            'tax-free assets',
                            'other assets',
                            'liability',
                            'out of estate'
                        ];
                        return array_search($record->type, $order);
                    })->groupBy('type');
                });

            $result = [];
            foreach ($netWorths as $year => $records) {
                $totals = $this->calculateTotals($records);

                $assetsTotal = array_sum([
                    $totals['liquid_assets'],
                    $totals['taxable_financial_assets'],
                    $totals['tax_deferred_assets'],
                    $totals['tax_free_assets'],
                    $totals['other_assets'],
                ]);

                $netWorth = $assetsTotal - $totals['liabilities'];

                $result[$year] = [
                    'liquid_assets_subTotal' => $totals['liquid_assets'],
                    'taxable_financial_assets_subTotal' => $totals['taxable_financial_assets'],
                    'tax_deferred_assets_subTotal' => $totals['tax_deferred_assets'],
                    'tax_free_assets_subTotal' => $totals['tax_free_assets'],
                    'other_assets_subTotal' => $totals['other_assets'],
                    'liabilities_subTotal' => $totals['liabilities'],
                    'out_of_estate_subTotal' => $totals['out_of_estate'],
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $totals['liabilities'],
                    'out_of_estate_total' => $totals['out_of_estate'],
                    'net_worth' => $netWorth,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Net worth calculated successfully',
                'code' => 200,
                'data' => [$netWorths, $result],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    } */


    public function getNetWorth(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
            ]);

            // Group and sort net worth records by year and type
            $netWorths = NetWorth::where('user_id', auth()->id())
                ->where('year', $validated['year'])
                ->get()
                ->groupBy('year')
                ->map(function ($records) {
                    $order = [
                        'liquid assets',
                        'taxable financial assets',
                        'tax-deferred assets',
                        'tax-free assets',
                        'other assets',
                        'liability',
                        'out of estate'
                    ];

                    // Group the records by type and sort the keys based on the predefined order
                    $grouped = $records->groupBy('type')
                        ->sortKeysUsing(function ($key1, $key2) use ($order) {
                            return array_search($key1, $order) - array_search($key2, $order);
                        });

                    // Ensure that all the default types are present, even if empty
                    $defaultTypes = [
                        'liquid assets',
                        'taxable financial assets',
                        'tax-deferred assets',
                        'tax-free assets',
                        'other assets',
                        'liability',
                        'out of estate'
                    ];

                    // Loop through each default type and check if it has records, otherwise set an empty array
                    foreach ($defaultTypes as $type) {
                        if (!isset($grouped[$type])) {
                            // Set an empty array for types that do not have any records
                            $grouped[$type] = collect([]);
                        }
                    }

                    return $grouped;
                });

            $result = [];
            foreach ($netWorths as $year => $records) {
                $totals = $this->calculateTotals($records);

                $assetsTotal = array_sum([
                    $totals['liquid_assets'],
                    $totals['taxable_financial_assets'],
                    $totals['tax_deferred_assets'],
                    $totals['tax_free_assets'],
                    $totals['other_assets'],
                ]);

                $netWorth = $assetsTotal - $totals['liabilities'];

                $result[$year] = [
                    'liquid_assets_subTotal' => $totals['liquid_assets'],
                    'taxable_financial_assets_subTotal' => $totals['taxable_financial_assets'],
                    'tax_deferred_assets_subTotal' => $totals['tax_deferred_assets'],
                    'tax_free_assets_subTotal' => $totals['tax_free_assets'],
                    'other_assets_subTotal' => $totals['other_assets'],
                    'liabilities_subTotal' => $totals['liabilities'],
                    'out_of_estate_subTotal' => $totals['out_of_estate'],
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $totals['liabilities'],
                    'out_of_estate_total' => $totals['out_of_estate'],
                    'net_worth' => $netWorth,
                ];
            }

            if ($netWorths->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No records found',
                    'code' => 200,
                    'data' => [
                        [
                            $validated['year'] => [
                                'liquid assets' => [],
                                'taxable financial assets' => [],
                                'tax-deferred assets' => [],
                                'tax-free assets' => [],
                                'other assets' => [],
                                'liability' => [],
                                'out of estate' => []
                            ]
                        ],
                        [
                            $validated['year'] => [
                                'liquid_assets_subTotal' => 0,
                                'taxable_financial_assets_subTotal' => 0,
                                'tax_deferred_assets_subTotal' => 0,
                                'tax_free_assets_subTotal' => 0,
                                'other_assets_subTotal' => 0,
                                'liabilities_subTotal' => 0,
                                'out_of_estate_subTotal' => 0,
                                'assets_total' => 0,
                                'liabilities_total' => 0,
                                'out_of_estate_total' => 0,
                                'net_worth' => 0
                            ]
                        ]
                    ],
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Net worth calculated successfully',
                'code' => 200,
                'data' => [$netWorths, $result],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }

    protected function calculateTotals($records): array
    {
        $categories = [
            'liquid assets' => 'liquid_assets',
            'taxable financial assets' => 'taxable_financial_assets',
            'tax-deferred assets' => 'tax_deferred_assets',
            'tax-free assets' => 'tax_free_assets',
            'other assets' => 'other_assets',
            'liability' => 'liabilities',
            'out of estate' => 'out_of_estate',
        ];

        $totals = [];
        foreach ($categories as $type => $key) {
            $totals[$key] = $this->calculateSubtotal($records, $type);
        }

        return $totals;
    }

    protected function calculateSubtotal($records, $type)
    {
        return $records->get($type, collect())->sum(function ($record) {
            return collect([
                'jan',
                'feb',
                'mar',
                'apr',
                'may',
                'jun',
                'jul',
                'aug',
                'sep',
                'oct',
                'nov',
                'dec',
            ])->sum(fn($month) => $record->$month);
        });
    }





    public function storeNetWorth(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'type' => 'required|string|in:liquid assets,taxable financial assets,tax-deferred assets,tax-free assets,other assets,liability,out of estate',
                'name' => 'required|string|max:255',
                'institution' => 'nullable|string',
                'notes' => 'nullable',
                'year' => 'required|numeric',
                'jan' => 'nullable|numeric',
                'feb' => 'nullable|numeric',
                'mar' => 'nullable|numeric',
                'apr' => 'nullable|numeric',
                'may' => 'nullable|numeric',
                'jun' => 'nullable|numeric',
                'jul' => 'nullable|numeric',
                'aug' => 'nullable|numeric',
                'sep' => 'nullable|numeric',
                'oct' => 'nullable|numeric',
                'nov' => 'nullable|numeric',
                'dec' => 'nullable|numeric',
            ]);

            // Custom validation for unique name and institution combination
            $duplicateRecord = NetWorth::where([
                ['user_id', auth()->id()],
                ['year', $request->year],
                ['type', $request->type],
                ['name', $request->name],
                ['institution', $request->institution],
            ])->exists();

            if ($duplicateRecord) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate record found',
                    'code' => 422,
                    'data' => [],
                ], 422);
            }

            // Create net worth record
            $netWorth = NetWorth::create([
                'user_id' => auth()->user()->id,
                'type' => $request->input('type'),
                'name' => $request->input('name'),
                'institution' => $request->input('institution'),
                'year' => $request->input('year'),
            ]);

            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($months as $month) {
                if ($request->filled($month)) {
                    $netWorth->{$month} = $request->input($month);
                }
            }

            $netWorth->notes = $request->input('notes');

            $netWorth->save();

            return response()->json([
                'status' => true,
                'message' => 'Net worth created successfully',
                'code' => 200,
                'data' => $netWorth,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }


    public function updateNetWorth(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'type' => 'string|in:liquid assets,taxable financial assets,tax-deferred assets,tax-free assets,other assets,liability',
                'name' => 'string|max:255',
                'institution' => 'string',
                'notes' => 'nullable',
                'year' => 'numeric',
                'jan' => 'nullable|numeric',
                'feb' => 'nullable|numeric',
                'mar' => 'nullable|numeric',
                'apr' => 'nullable|numeric',
                'may' => 'nullable|numeric',
                'jun' => 'nullable|numeric',
                'jul' => 'nullable|numeric',
                'aug' => 'nullable|numeric',
                'sep' => 'nullable|numeric',
                'oct' => 'nullable|numeric',
                'nov' => 'nullable|numeric',
                'dec' => 'nullable|numeric',
            ]);

            // Update net worth record
            $netWorth = NetWorth::where('user_id', auth()->user()->id)->findOrFail($id);

            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($months as $month) {
                if ($request->filled($month)) {
                    $netWorth->{$month} = $request->input($month);
                }
            }

            $netWorth->type = $request->input('type');
            $netWorth->name = $request->input('name');
            $netWorth->institution = $request->input('institution');
            $netWorth->notes = $request->input('notes');
            $netWorth->year = $request->input('year');

            $netWorth->save();

            return response()->json([
                'status' => true,
                'message' => 'Net worth updated successfully',
                'code' => 200,
                'data' => $netWorth,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }

    public function destroyNetWorth($id)
    {
        try {
            $netWorth = NetWorth::where('user_id', auth()->user()->id)->findOrFail($id);
            $netWorth->delete();

            return response()->json([
                'status' => true,
                'message' => 'Net worth deleted successfully',
                'code' => 200,
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }

    public function bulkDeleteNetWorth(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:net_worths,id',
        ]);
    
        DB::beginTransaction();
    
        try {
            $userId = auth()->user()->id;
            $deletedCount = NetWorth::where('user_id', $userId)
                ->whereIn('id', $request->ids)
                ->delete();
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => "{$deletedCount} net worth record(s) deleted successfully",
                'code' => 200,
                'data' => [],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }

}
