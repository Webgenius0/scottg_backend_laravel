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
    public function getNetWorth(): JsonResponse
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

            // Calculate net worth
            $netWorth = $assetsTotal - $liabilitiesTotal;

            // Return response
            return response()->json([
                'status' => true,
                'message' => 'Net worth calculated successfully',
                'code' => 200,
                'data' => [
                    'liquid_assets_total' => $liquidAssetsTotal,
                    'taxable_financial_assets_total' => $taxableFinancialAssetsTotal,
                    'tax_deferred_assets_total' => $taxDeferredAssetsTotal,
                    'tax_free_assets_total' => $taxFreeAssetsTotal,
                    'other_assets_total' => $otherAssetsTotal,
                    'assets_total' => $assetsTotal,
                    'liabilities_total' => $liabilitiesTotal,
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
    }

    /**
     * Calculate the subtotal for a specific type.
     *
     * @param \Illuminate\Database\Eloquent\Collection $netWorths
     * @param string $type
     * @return float
     */
    private function calculateSubtotal($netWorths, string $type): float
    {
        return $netWorths->where('type', $type)->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });
    }

    public function storeNetWorth(Request $request)
    {
        try {

            // Validate request
            $request->validate([
                'type' => 'required|string|in:liquid assets,taxable financial assets,tax-deferred assets,tax-free assets,other assets,liability',
                'name' => 'required|string|max:255',
                'institution' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
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



            // Create net worth record
            $netWorth = NetWorth::firstOrNew([
                'user_id' => auth()->user()->id,
                'type' => $request->input('type'),
                'name' => $request->input('name'),
                'institution' => $request->input('institution'),
                'year' => $request->input('year'),
            ]);
            $netWorth->notes = $request->input('notes');

            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($months as $month) {
                if ($request->filled($month)) {
                    $netWorth->{$month} = $request->input($month);
                }
            }
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


    /* public function updateNetWorth(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'type' => 'string|in:liquid assets,taxable financial assets,tax-deferred assets,tax-free assets,other assets,liability',
                'name' => 'string|max:255',
                'institution' => 'string|max:255',
                'notes' => 'nullable|string',
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
            $netWorth->update([
                'type' => $request->input('type'),
                'name' => $request->input('name'),
                'institution' => $request->input('institution'),
                'notes' => $request->input('notes'),
                'year' => $request->input('year'),
                'jan' => $request->input('jan'),
                'feb' => $request->input('feb'),
                'mar' => $request->input('mar'),
                'apr' => $request->input('apr'),
                'may' => $request->input('may'),
                'jun' => $request->input('jun'),
                'jul' => $request->input('jul'),
                'aug' => $request->input('aug'),
                'sep' => $request->input('sep'),
                'oct' => $request->input('oct'),
                'nov' => $request->input('nov'),
                'dec' => $request->input('dec'),
            ]);

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
    } */
}
