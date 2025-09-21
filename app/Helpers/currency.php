<?php

use App\Models\Currency;

if (!function_exists('convertCurrency')) {
    /**
     * Convert an amount from one currency to another using DB rates.
     *
     * @param float  $amount
     * @param string $from (e.g. 'INR')
     * @param string $to   (e.g. 'USD')
     * @return float
     */
    function convertCurrency(float $amount, string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return $amount;
        }

        $fromRate = Currency::where('code', $from)->value('value');
        $toRate = Currency::where('code', $to)->value('value');

        if (!$fromRate || !$toRate) {
            return $amount; 
        }

        // Base stored as INR=1.0. If base is INR, toRate = value in INR per 1 unit of currency.
        // Formula: amount_in_INR = amount / fromRate, then convert to target.
        $amountInInr = $amount / $fromRate;
        $converted = $amountInInr * $toRate;

        return round($converted, 2);
    }
}
