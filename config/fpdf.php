<?php
function calculateBill($consumption)
{
    $tiers = [
        [15, 2.3488],
        [10, 2.9882],
        [10, 3.2405],
        [65, 3.6237],
        [50, 3.7171],
        [PHP_INT_MAX, 4.2218]
    ];
    $remaining = $consumption;
    $totalEnergy = 0;
    $breakdown = [];
    foreach ($tiers as $tier) {
        $limit = $tier[0];
        $rate = $tier[1];
        $use = min($remaining, $limit);
        if ($use <= 0) break;
        $amt = $use * $rate;
        $breakdown[] = ['units' => $use, 'rate' => $rate, 'amount' => $amt];
        $totalEnergy += $amt;
        $remaining -= $use;
    }
    $serviceCharge = 8.19;
    $fuelAdj = 0;
    $vat = ($totalEnergy + $serviceCharge + $fuelAdj) * 0.07;
    $total = $totalEnergy + $serviceCharge + $fuelAdj + $vat;
    return ['breakdown' => $breakdown, 'totalEnergy' => $totalEnergy, 'serviceCharge' => $serviceCharge, 'vat' => $vat, 'total' => $total];
}
