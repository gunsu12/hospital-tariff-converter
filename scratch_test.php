<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\TariffUnpivoterService;

$service = new TariffUnpivoterService();

$rows = [
    [
        'nama tindakan' => 'Laparascopy',
        'kelas' => 'KELAS 1',
        'tariff umum' => '1.000.000',
        'operator' => '500.000',
        'asisten' => '50.000',
        'anastesi' => '50.000',
        'penata' => '50.000',
        'instrumen' => '50.000',
        'jasa rs' => '150.000',
        'jasa pelayanan' => '50.000',
    ]
];

$mapping = [
    'action_col' => 'nama tindakan',
    'class_col' => 'kelas',
    'total_tariff_col' => 'tariff umum',
    'cat1_cols' => ['jasa rs'],
    'cat2_cols' => ['operator', 'asisten', 'anastesi', 'penata', 'instrumen'],
    'cat3_cols' => ['jasa pelayanan'],
    'parent_suffix' => '*',
    'child_separator' => ' - ',
];

$result = $service->transform($rows, $mapping);

echo "=== RESULT CONVERSION SUMMARY ===\n";
echo "Total Source Rows: " . $result['total_source_rows'] . "\n";
echo "Total Converted Rows: " . $result['total_converted_rows'] . "\n";
echo "Mismatch Count: " . $result['mismatch_count'] . "\n\n";

echo "=== CONVERTED ITEMS ===\n";
foreach ($result['items'] as $item) {
    printf(
        "%-3d | %-30s | %-7s | %-5s | %12s | %12s | %12s | %12s\n",
        $item['row_index'],
        $item['nama_tindakan'],
        $item['kelas'],
        $item['induk'] ? 'true' : 'false',
        number_format($item['total_tarif'], 0, ',', '.'),
        number_format($item['tarif_komponen1'], 0, ',', '.'),
        number_format($item['tarif_komponen2'], 0, ',', '.'),
        number_format($item['tarif_komponen3'], 0, ',', '.')
    );
}
