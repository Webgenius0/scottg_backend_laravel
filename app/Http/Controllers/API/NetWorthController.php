<?php

namespace App\Http\Controllers\API;

use App\Models\NetWorth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Exception;

class NetWorthController extends Controller
{

    /**
     * Get asset and liability subtotals and net worth.
     *

     * @return JsonResponse
     */
    /* public function getNetWorth(): JsonResponse
    {
        try {

            // Get all net worth records for the user
            $netWorths = NetWorth::where('user_id', auth()->user()->id)->get();

            // Calculate subtotals for each asset type
            $liquidAssetsTotal = $this->calculateSubtotal($netWorths, 'liquid assets');
            $taxableFinancialAssetsTotal = $this->calculateSubtotal($netWorths, 'taxable financial assets');
            $taxDeferredAssetsTotal = $this->calculateSubtotal($netWorths, 'tax-deferred assets');
            $taxFreeAssetsTotal = $this->calculateSubtotal($netWorths, 'tax-free assets');
            $otherAssetsTotal = $this->calculateSubtotal($netWorths, 'other assets');

            // Calculate total assets
            $assetsTotal = $liquidAssetsTotal + $taxableFinancialAssetsTotal + $taxDeferredAssetsTotal + $taxFreeAssetsTotal + $otherAssetsTotal;

            // Calculate subtotal for liabilities
            $liabilitiesTotal = $this->calculateSubtotal($netWorths, 'liability');

            // calculate subtotal for out of estate
            $outOfEstateTotal = $this->calculateSubtotal($netWorths, 'out of estate');

            // Calculate net worth
            $netWorth = $assetsTotal - $liabilitiesTotal;

            // Return response
            return response()->json([
                'status' => true,
                'message' => 'Net worth calculated successfully',
                'code' => 200,
                'data' => [
                    'liquid_assets_subTotal' => $liquidAssetsTotal,
                    'taxable_financial_assets_subTotal' => $taxableFinancialAssetsTotal,
                    'tax_deferred_assets_subTotal' => $taxDeferredAssetsTotal,
                    'tax_free_assets_subTotal' => $taxFreeAssetsTotal,
                    'other_assets_subTotal' => $otherAssetsTotal,
                    'liabilities_subTotal' => $liabilitiesTotal,
                    'out_of_estate_subTotal' => $outOfEstateTotal,
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $liabilitiesTotal,
                    'out_of_estate_total' => $outOfEstateTotal,
                    'net_worth' => $netWorth,
                ],
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

            // Group net worth records by year and map by type
            $netWorths = NetWorth::where('user_id', auth()->user()->id)
            ->where('year', $validated['year'])
            ->get()->groupBy('year')->map(function ($records) {
                
                return $records->groupBy('type');

            });

            $result = [];
            foreach ($netWorths as $year => $records) {
                $liquidAssetsTotal = $this->calculateSubtotal($records, 'liquid assets');
                $taxableFinancialAssetsTotal = $this->calculateSubtotal($records, 'taxable financial assets');
                $taxDeferredAssetsTotal = $this->calculateSubtotal($records, 'tax-deferred assets');
                $taxFreeAssetsTotal = $this->calculateSubtotal($records, 'tax-free assets');
                $otherAssetsTotal = $this->calculateSubtotal($records, 'other assets');
                $liabilitiesTotal = $this->calculateSubtotal($records, 'liability');
                $outOfEstateTotal = $this->calculateSubtotal($records, 'out of estate');
                $assetsTotal = $liquidAssetsTotal + $taxableFinancialAssetsTotal + $taxDeferredAssetsTotal +
                    $taxFreeAssetsTotal + $otherAssetsTotal;
                $netWorth = $assetsTotal - $liabilitiesTotal;

                $result[$year] = [
                    'liquid_assets_subTotal' => $liquidAssetsTotal,
                    'taxable_financial_assets_subTotal' => $taxableFinancialAssetsTotal,
                    'tax_deferred_assets_subTotal' => $taxDeferredAssetsTotal,
                    'tax_free_assets_subTotal' => $taxFreeAssetsTotal,
                    'other_assets_subTotal' => $otherAssetsTotal,
                    'liabilities_subTotal' => $liabilitiesTotal,
                    'out_of_estate_subTotal' => $outOfEstateTotal,
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $liabilitiesTotal,
                    'out_of_estate_total' => $outOfEstateTotal,
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
    }

    protected function calculateSubtotal($records, $type)
    {
        return $records->where('type', $type)->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
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
}
