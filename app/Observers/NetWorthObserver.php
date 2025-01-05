<?php

namespace App\Observers;

use App\Models\NetWorth;

class NetWorthObserver
{
    /**
     * Handle the created or updated event.
     */
    public function saved(NetWorth $netWorth)
    {
        $this->updateNetWorth($netWorth->user_id);
    }

    /**
     * Handle the deleted event.
     */
    public function deleted(NetWorth $netWorth)
    {
        $this->updateNetWorth($netWorth->user_id);
    }

    /**
     * Update total assets, liabilities, and net worth for the user.
     */
    protected function updateNetWorth($userId)
    {
        // Get all net worth records for the user
        $netWorths = NetWorth::where('user_id', $userId)->get();

        // Calculate subtotal for liquid assets
        $liquidAssetsTotal = $netWorths->where('type', 'liquid assets')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });

        // Calculate subtotal for taxable financial assets
        $taxableFinancialAssetsTotal = $netWorths->where('type', 'taxable financial assets')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });

        // calculate tax-deferred assets
        $taxDeferredAssetsTotal = $netWorths->where('type', 'tax-deferred assets')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });
        
        // calculate tax-free assets
        $taxFreeAssetsTotal = $netWorths->where('type', 'tax-free assets')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });

        // calculate other assets
        $otherAssetsTotal = $netWorths->where('type', 'other assets')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });

        // Calculate total assets
        $assetsTotal = $liquidAssetsTotal + $taxableFinancialAssetsTotal + $taxDeferredAssetsTotal + $taxFreeAssetsTotal + $otherAssetsTotal;

        // Calculate subtotal for liabilities
        $liabilitiesTotal = $netWorths->where('type', 'liability')->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });

        // Update the net worth in the database
        NetWorth::where('user_id', $userId)->update([
            'net_worth' => $assetsTotal - $liabilitiesTotal,
        ]);
    }
}

