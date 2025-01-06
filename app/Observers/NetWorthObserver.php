<?php

namespace App\Observers;

use App\Models\NetWorth;

class NetWorthObserver
{
    public function saved(NetWorth $netWorth)
    {
        $this->updateNetWorth($netWorth->user_id);
    }

    public function deleted(NetWorth $netWorth)
    {
        $this->updateNetWorth($netWorth->user_id);
    }

    protected function updateNetWorth($userId)
    {
        // Group net worth records by year
        $netWorths = NetWorth::where('user_id', $userId)->get()->groupBy('year');

        foreach ($netWorths as $year => $records) {
            $assetsTotal = $this->calculateAssetTotals($records);
            $liabilitiesTotal = $this->calculateSubtotal($records, 'liability');

            // Calculate net worth for the year
            $netWorthForYear = $assetsTotal - $liabilitiesTotal;

            // Update net worth in the database
            NetWorth::where('user_id', $userId)->where('year', $year)->update([
                'net_worth' => $netWorthForYear,
            ]);
        }
    }

    protected function calculateAssetTotals($records)
    {
        $types = [
            'liquid assets',
            'taxable financial assets',
            'tax-deferred assets',
            'tax-free assets',
            'other assets',
        ];

        $total = 0;
        foreach ($types as $type) {
            $total += $this->calculateSubtotal($records, $type);
        }

        return $total;
    }

    protected function calculateSubtotal($records, $type)
    {
        return $records->where('type', $type)->sum(function ($record) {
            return $record->jan + $record->feb + $record->mar + $record->apr + $record->may + $record->jun +
                   $record->jul + $record->aug + $record->sep + $record->oct + $record->nov + $record->dec;
        });
    }
}


