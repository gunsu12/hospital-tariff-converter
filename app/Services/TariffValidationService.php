<?php

namespace App\Services;

class TariffValidationService
{
    /**
     * Perform reconciliation check between sum of component items vs total tariff.
     */
    public function validateReconciliation(float $totalTariff, float $sumComponents): array
    {
        $diff = round($totalTariff - $sumComponents, 2);
        $isValid = abs($diff) <= 0.01;

        return [
            'is_valid' => $isValid,
            'total_tariff' => $totalTariff,
            'sum_components' => $sumComponents,
            'difference' => $diff,
            'message' => $isValid
                ? 'Total tarif sesuai dengan penjumlahan komponen.'
                : "Terdapat selisih sebesar Rp " . number_format(abs($diff), 0, ',', '.') . " antara Total Tarif dengan Jumlah Rincian Komponen.",
        ];
    }
}
