<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvertedTariffItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_history_id',
        'row_index',
        'nama_tindakan',
        'kelas',
        'induk',
        'total_tarif',
        'tarif_komponen1',
        'tarif_komponen2',
        'tarif_komponen3',
        'has_mismatch',
    ];

    protected $casts = [
        'induk' => 'boolean',
        'has_mismatch' => 'boolean',
        'total_tarif' => 'float',
        'tarif_komponen1' => 'float',
        'tarif_komponen2' => 'float',
        'tarif_komponen3' => 'float',
    ];

    public function importHistory(): BelongsTo
    {
        return $this->belongsTo(ImportHistory::class);
    }
}
