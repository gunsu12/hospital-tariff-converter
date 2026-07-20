<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'total_rows_source',
        'total_rows_converted',
        'status',
        'mapping_template_id',
        'mismatch_count',
    ];

    public function mappingTemplate(): BelongsTo
    {
        return $this->belongsTo(MappingTemplate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConvertedTariffItem::class);
    }
}
