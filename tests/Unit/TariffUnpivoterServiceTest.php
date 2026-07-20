<?php

namespace Tests\Unit;

use App\Services\TariffUnpivoterService;
use PHPUnit\Framework\TestCase;

class TariffUnpivoterServiceTest extends TestCase
{
    public function test_it_transforms_horizontal_tariff_into_parent_and_child_rows(): void
    {
        $service = new TariffUnpivoterService();

        $rows = [
            [
                'nama tindakan' => 'Laparascopy',
                'kelas' => 'KELAS 1',
                'tariff umum' => '1.000.000',
                'operator' => '600.000',
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

        $this->assertEquals(1, $result['total_source_rows']);
        $this->assertEquals(8, $result['total_converted_rows']); // 1 Parent + 7 Children
        $this->assertEquals(0, $result['mismatch_count']);

        $items = $result['items'];

        // Parent Row check
        $this->assertEquals('Laparascopy*', $items[0]['nama_tindakan']);
        $this->assertEquals('KELAS 1', $items[0]['kelas']);
        $this->assertTrue($items[0]['induk']);
        $this->assertEquals(1000000.0, $items[0]['total_tarif']);
        $this->assertEquals(0.0, $items[0]['tarif_komponen1']);
        $this->assertEquals(0.0, $items[0]['tarif_komponen2']);
        $this->assertEquals(0.0, $items[0]['tarif_komponen3']);

        // Child 1: Jasa RS (Facility -> cat1)
        $this->assertEquals('Laparascopy - jasa rs', $items[1]['nama_tindakan']);
        $this->assertFalse($items[1]['induk']);
        $this->assertEquals(150000.0, $items[1]['total_tarif']);
        $this->assertEquals(150000.0, $items[1]['tarif_komponen1']);

        // Child 2: Operator (Honor Doctor -> cat2)
        $this->assertEquals('Laparascopy - operator', $items[2]['nama_tindakan']);
        $this->assertFalse($items[2]['induk']);
        $this->assertEquals(600000.0, $items[2]['total_tarif']);
        $this->assertEquals(600000.0, $items[2]['tarif_komponen2']);

        // Child 7: Jasa Pelayanan (Service -> cat3)
        $this->assertEquals('Laparascopy - jasa pelayanan', $items[7]['nama_tindakan']);
        $this->assertFalse($items[7]['induk']);
        $this->assertEquals(50000.0, $items[7]['total_tarif']);
        $this->assertEquals(50000.0, $items[7]['tarif_komponen3']);
    }
}
