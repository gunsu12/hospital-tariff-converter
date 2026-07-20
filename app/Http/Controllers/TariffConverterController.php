<?php

namespace App\Http\Controllers;

use App\Models\MappingTemplate;
use App\Services\ExcelExporterService;
use App\Services\ExcelParserService;
use App\Services\TariffUnpivoterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TariffConverterController extends Controller
{
    public function index()
    {
        return Inertia::render('TariffConverter/Index', [
            'templates' => MappingTemplate::orderBy('name')->get(),
        ]);
    }

    public function uploadFile(Request $request, ExcelParserService $parserService): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $result = $parserService->parseFile($file->getRealPath(), $ext);

            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca berkas: ' . $e->getMessage(),
            ], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    public function parseHeaders(Request $request, ExcelParserService $parserService): JsonResponse
    {
        $request->validate([
            'rows' => 'nullable|array',
        ]);

        $rows = $request->input('rows', []);
        $result = $parserService->parseHeadersAndPreview($rows);

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function convert(Request $request, TariffUnpivoterService $unpivoterService): JsonResponse
    {
        $validated = $request->validate([
            'rows' => 'required|array',
            'mapping' => 'required|array',
            'mapping.action_col' => 'nullable|string',
            'mapping.class_col' => 'nullable|string',
            'mapping.total_tariff_col' => 'nullable|string',
            'mapping.cat1_cols' => 'nullable|array',
            'mapping.cat2_cols' => 'nullable|array',
            'mapping.cat3_cols' => 'nullable|array',
            'mapping.parent_suffix' => 'nullable|string',
            'mapping.child_separator' => 'nullable|string',
            'mapping.skip_zero_components' => 'nullable|boolean',
        ]);

        try {
            $rows = $validated['rows'] ?? [];
            $mapping = $validated['mapping'] ?? [];

            $result = $unpivoterService->transform($rows, $mapping);

            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses konversi: ' . $e->getMessage(),
            ], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    public function saveTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'config' => 'required|array',
        ]);

        $template = MappingTemplate::create($validated);

        return response()->json([
            'success' => true,
            'template' => $template,
            'message' => 'Template mapping berhasil disimpan.',
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function export(Request $request, ExcelExporterService $exporterService)
    {
        $items = $request->input('items', []);

        $csvContent = $exporterService->exportToCsv($items);
        $filename = 'tariff_converted_' . date('Ymd_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
