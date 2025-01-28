<?php

namespace App\Imports;

use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetImport implements ToModel,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Asset([
            'organization_id'   => Auth::id(),
            'product_id'        => $row[0],
            'brand'        => $row[1] ?? null,
            'range'           => $row[2] ?? null,
            'product'        => $row[3] ?? null,
            'qr_code'     => $row[4] ?? null,
            'serial_number'         => $row[5] ?? null,
            'external_serial_number'             => $row[6] ?? null,
            'manufacturing_date'          => $row[7] ?? null,
            'installation_date'   => $row[8] ?? null,
            'warranty_end_date'  => $row[9] ?? null,
            'unit_price' => $row[10] ?? null,
            'max_spend'     => $row[11] ?? null,
            'fitness_product'  => $row[12] ?? null,
            'has_odometer'  => $row[13] ?? null,
            'location'  => $row[14] ?? null,
            'residual_price'  => $row[15] ?? null,
        ]);
    }



    /**
     * Number of rows to read per chunk.
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
