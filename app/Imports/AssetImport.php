<?php
namespace App\Imports;

use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetImport implements ToModel, WithChunkReading,WithHeadingRow
{
    public function model(array $row)
    {
        return new Asset([
            'organization_id'        => Auth::id(),
            'product_id'             => $row[0],
            'brand'                  => $row[1] ?? null,
            'range'                  => $row[2] ?? null,
            'product'                => $row[3] ?? null,
            'qr_code'                => $row[4] ?? null,
            'serial_number'          => $row[5] ?? null,
            'external_serial_number' => $row[6] ?? null,
            'manufacturing_date'     => isset($row[7]) ? $this->parseDate($row[7]) : null,
            'installation_date'      => isset($row[8]) ? $this->parseDate($row[8]) : null,
            'warranty_end_date'      => isset($row[9]) ? $this->parseDate($row[9]) : null,
            'unit_price'             => $this->parseNumeric($row[10]),
            'max_spend'              => $this->parseNumeric($row[11]),
            'fitness_product'        => $row[12] == 'true' || $row[12] == 1,
            'has_odometer'           => $row[13] == 'true' || $row[13] == 1,
            'location'               => $row[14] ?? null,
            'residual_price'         => $this->parseNumeric($row[15]),
        ]);
    }

    /**
     * Parse and validate a numeric value, or return null if invalid.
     *
     * @param mixed $value
     * @return float|null
     */
    private function parseNumeric($value)
    {
        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Parse the date dynamically or return null if invalid.
     *
     * @param string $date
     * @return string|null
     */
    private function parseDate($date)
    {
        $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d']; // Add other formats as needed

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) {
                // Continue to the next format
            }
        }

        // Return null if no formats match
        return null;
    }

    /**
     * Number of rows to read per chunk.
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
