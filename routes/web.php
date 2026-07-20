<?php

use App\Http\Controllers\TariffConverterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TariffConverterController::class, 'index'])->name('converter.index');
Route::post('/api/upload', [TariffConverterController::class, 'uploadFile'])->name('converter.upload');
Route::post('/api/parse-headers', [TariffConverterController::class, 'parseHeaders'])->name('converter.parse_headers');
Route::post('/api/convert', [TariffConverterController::class, 'convert'])->name('converter.convert');
Route::post('/api/save-template', [TariffConverterController::class, 'saveTemplate'])->name('converter.save_template');
Route::post('/api/export', [TariffConverterController::class, 'export'])->name('converter.export');
