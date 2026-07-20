<?php

namespace App\Services;

class ExcelExporterService
{
    /**
     * Generate CSV content representation from converted items array.
     */
    public function exportToCsv(array $items): string
    {
        $output = fopen('php://temp', 'r+');

        // Header row
        fputcsv($output, [
            'nama tindakan',
            'kelas',
            'induk',
            'total tarif',
            'tarif_komponen1',
            'tarif_komponen2',
            'tarif_komponen3',
        ]);

        foreach ($items as $item) {
            fputcsv($output, [
                $item['nama_tindakan'],
                $item['kelas'],
                $item['induk'] ? 'true' : 'false',
                $item['total_tarif'],
                $item['tarif_komponen1'],
                $item['tarif_komponen2'],
                $item['tarif_komponen3'],
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
