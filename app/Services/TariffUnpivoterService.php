<?php

namespace App\Services;

class TariffUnpivoterService
{
    /**
     * Transform horizontal tariff rows into unpivoted vertical hierarchy (Parent & Child rows).
     *
     * @param array $rows Source rows from Excel/CSV
     * @param array $mapping Mapping configuration
     *   - action_col: string
     *   - class_col: string
     *   - total_tariff_col: string
     *   - cat1_cols: array (Jasa RS, BMHP, non-honor)
     *   - cat2_cols: array (Operator, Anastesi, Asisten, Penata, Instrumen, honor)
     *   - cat3_cols: array (Jasa Pelayanan)
     *   - parent_suffix: string (default '*')
     *   - child_separator: string (default ' - ')
     *   - skip_zero_components: bool (default false)
     * 
     * @return array Matrix result containing converted items and summary metadata
     */
    public function transform(array $rows, array $mapping): array
    {
        $actionCol = $mapping['action_col'] ?? 'nama tindakan';
        $classCol = $mapping['class_col'] ?? 'kelas';
        $totalTariffCol = $mapping['total_tariff_col'] ?? 'tariff umum';

        $cat1Cols = (array) ($mapping['cat1_cols'] ?? []);
        $cat2Cols = (array) ($mapping['cat2_cols'] ?? []);
        $cat3Cols = (array) ($mapping['cat3_cols'] ?? []);

        $parentSuffix = $mapping['parent_suffix'] ?? '*';
        $childSeparator = isset($mapping['child_separator']) && $mapping['child_separator'] !== '' ? $mapping['child_separator'] : ' - ';
        if ($childSeparator === '-') {
            $childSeparator = ' - ';
        }
        $skipZeroComponents = isset($mapping['skip_zero_components']) ? (bool) $mapping['skip_zero_components'] : true;

        $convertedItems = [];
        $mismatchCount = 0;
        $rowIndex = 1;

        foreach ($rows as $row) {
            $rawAction = trim((string) ($row[$actionCol] ?? ''));
            $rawClass = trim((string) ($row[$classCol] ?? ''));
            $totalTariffInput = ExcelParserService::cleanNumeric($row[$totalTariffCol] ?? 0);

            if (empty($rawAction) && empty($rawClass) && $totalTariffInput == 0) {
                continue; // Skip empty trailing rows
            }

            // Calculate sum of all mapped child components
            $sumChildComponents = 0.0;
            $childComponentRows = [];

            $getRowValue = function ($row, $key) {
                foreach ($row as $k => $v) {
                    if (strcasecmp((string)$k, (string)$key) === 0) {
                        return $v;
                    }
                }
                return null;
            };

            // Helper closure to process component columns
            $processComponentCol = function ($colName, $categoryType) use ($row, $getRowValue, &$sumChildComponents, &$childComponentRows) {
                $val = $getRowValue($row, $colName);
                if ($val !== null) {
                    $amount = ExcelParserService::cleanNumeric($val);
                    $sumChildComponents += $amount;

                    $childComponentRows[] = [
                        'component_name' => trim((string)$colName),
                        'category' => $categoryType,
                        'amount' => $amount,
                    ];
                }
            };

            foreach ($cat1Cols as $col) {
                $processComponentCol($col, 1);
            }

            foreach ($cat2Cols as $col) {
                $processComponentCol($col, 2);
            }

            foreach ($cat3Cols as $col) {
                $processComponentCol($col, 3);
            }

            // Ensure total_tarif is computed from the sum of all component columns
            // If input total_tariff is provided and > 0, check for mismatch against sum of components
            $hasMismatch = false;
            if ($totalTariffInput > 0) {
                $hasMismatch = abs($sumChildComponents - $totalTariffInput) > 0.01;
                $finalTotalTariff = $totalTariffInput;
            } else {
                // Auto-total from sum of components
                $finalTotalTariff = $sumChildComponents;
            }

            if ($hasMismatch) {
                $mismatchCount++;
            }

            // 1. Create Parent Row (induk = true, total_tarif = total dari semua tarif_komponen)
            $convertedItems[] = [
                'row_index' => $rowIndex++,
                'nama_tindakan' => $rawAction . $parentSuffix,
                'kelas' => $rawClass,
                'induk' => true,
                'total_tarif' => $finalTotalTariff,
                'tarif_komponen1' => 0.0,
                'tarif_komponen2' => 0.0,
                'tarif_komponen3' => 0.0,
                'has_mismatch' => $hasMismatch,
            ];

            // 2. Create Child Rows
            foreach ($childComponentRows as $comp) {
                if ($skipZeroComponents && $comp['amount'] == 0) {
                    continue;
                }

                $convertedItems[] = [
                    'row_index' => $rowIndex++,
                    'nama_tindakan' => $rawAction . $childSeparator . $comp['component_name'],
                    'kelas' => $rawClass,
                    'induk' => false,
                    'total_tarif' => $comp['amount'],
                    'tarif_komponen1' => $comp['category'] === 1 ? $comp['amount'] : 0.0,
                    'tarif_komponen2' => $comp['category'] === 2 ? $comp['amount'] : 0.0,
                    'tarif_komponen3' => $comp['category'] === 3 ? $comp['amount'] : 0.0,
                    'has_mismatch' => false,
                ];
            }
        }

        return [
            'total_source_rows' => count($rows),
            'total_converted_rows' => count($convertedItems),
            'mismatch_count' => $mismatchCount,
            'items' => $convertedItems,
        ];
    }
}
