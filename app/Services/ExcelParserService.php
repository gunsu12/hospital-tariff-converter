<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelParserService
{
    /**
     * Clean numeric currency input strings into float numbers.
     * Examples:
     * "1.000.000" -> 1000000.00
     * "Rp 50.000" -> 50000.00
     * "1,000,000.50" -> 1000000.50
     */
    public static function cleanNumeric(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (empty($value)) {
            return 0.0;
        }

        $str = trim((string) $value);
        // Remove currency symbols (Rp, $, etc) and whitespace
        $str = preg_replace('/[^\d.,]/u', '', $str);

        if (empty($str)) {
            return 0.0;
        }

        // Both dot and comma present (e.g. 1.000.000,00 or 1,000,000.00)
        if (str_contains($str, '.') && str_contains($str, ',')) {
            if (strrpos($str, ',') > strrpos($str, '.')) {
                // Indonesian: 1.000.000,50
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // US: 1,000,000.50
                $str = str_replace(',', '', $str);
            }
        } elseif (str_contains($str, '.')) {
            // Only dot present (e.g. 1.000.000 or 150.000 or 10.50)
            $parts = explode('.', $str);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($parts[1]) === 3)) {
                $str = str_replace('.', '', $str);
            }
        } elseif (str_contains($str, ',')) {
            // Only comma present (e.g. 150,000 or 150,00)
            $parts = explode(',', $str);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($parts[1]) === 3)) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        }

        return (float) $str;
    }

    /**
     * Convert string to clean valid UTF-8 encoding.
     */
    public static function sanitizeUtf8(mixed $str): string
    {
        $string = (string) $str;
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'Windows-1252, ISO-8859-1, ASCII');
        }
        // Remove invalid UTF-8 byte sequences
        return @iconv('UTF-8', 'UTF-8//IGNORE', $string) ?: $string;
    }

    /**
     * Parse raw uploaded file (.xlsx, .xls, .csv, .txt) into headers and associative row array.
     */
    public function parseFile(string $filePath, string $originalExtension = ''): array
    {
        $ext = strtolower($originalExtension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        // Use native CSV parser for .csv / .txt
        if (in_array($ext, ['csv', 'txt'])) {
            return $this->parseCsvFile($filePath);
        }

        // Check if ZipArchive extension is loaded for .xlsx
        if (!class_exists('ZipArchive')) {
            throw new \Exception('Ekstensi PHP php_zip (ZipArchive) belum aktif di Laragon/PHP. Harap simpan file sebagai format .csv (Comma Delimited) atau aktifkan extension=zip di php.ini');
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return ['headers' => [], 'rows' => []];
        }

        // First row as header
        $rawHeaders = array_shift($rows);
        $headers = [];
        foreach ($rawHeaders as $colKey => $h) {
            $hName = self::sanitizeUtf8(trim((string) $h));
            if ($hName !== '') {
                $headers[$colKey] = $hName;
            }
        }

        $parsedRows = [];
        foreach ($rows as $row) {
            $parsedRow = [];
            $hasValue = false;
            foreach ($headers as $colKey => $hName) {
                $val = self::sanitizeUtf8(trim((string) ($row[$colKey] ?? '')));
                if ($val !== '') {
                    $hasValue = true;
                }
                $parsedRow[$hName] = $val;
            }

            if ($hasValue) {
                $parsedRows[] = $parsedRow;
            }
        }

        return [
            'headers' => array_values($headers),
            'rows' => $parsedRows,
        ];
    }

    /**
     * Native PHP CSV parser handling autodetect delimiters & UTF-8 conversion
     */
    public function parseCsvFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \Exception('Gagal membaca isi berkas.');
        }

        // Convert encoding to UTF-8
        $content = self::sanitizeUtf8($content);
        // Strip UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (empty($lines)) {
            return ['headers' => [], 'rows' => []];
        }

        // Autodetect delimiter
        $firstLine = $lines[0];
        $delimiter = ',';
        if (str_contains($firstLine, ';')) {
            $delimiter = ';';
        } elseif (str_contains($firstLine, "\t")) {
            $delimiter = "\t";
        }

        $rawHeaders = str_getcsv(array_shift($lines), $delimiter);
        $headers = array_map(fn($h) => self::sanitizeUtf8(trim(trim((string)$h), '"\'')), $rawHeaders);

        $parsedRows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $values = str_getcsv($line, $delimiter);
            
            $row = [];
            $hasValue = false;
            foreach ($headers as $idx => $hName) {
                $val = self::sanitizeUtf8(trim(trim((string)($values[$idx] ?? '')), '"\''));
                if ($val !== '') $hasValue = true;
                $row[$hName] = $val;
            }

            if ($hasValue) {
                $parsedRows[] = $row;
            }
        }

        return [
            'headers' => $headers,
            'rows' => $parsedRows,
        ];
    }

    /**
     * Parse raw tabular array or file data into headers and rows.
     */
    public function parseHeadersAndPreview(array $rows): array
    {
        if (empty($rows)) {
            return [
                'headers' => [],
                'sample_rows' => [],
                'total_rows' => 0,
            ];
        }

        $headers = array_values(array_map('trim', array_keys($rows[0])));
        
        return [
            'headers' => $headers,
            'sample_rows' => array_slice($rows, 0, 10),
            'total_rows' => count($rows),
        ];
    }
}
