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
            'asset_name'        => $row[0],
            'brand_name'        => $row[1] ?? null,
            'qr_code'           => $row[2] ?? null,
            'unit_price'        => $row[3] ?? null,
            'current_spend'     => $row[4] ?? null,
            'max_spend'         => $row[5] ?? null,
            'range'             => $row[6] ?? null,
            'location'          => $row[7] ?? null,
            'manufacture_sno'   => $row[8] ?? null,
            'manufacture_date'  => $row[9] ?? null,
            'installation_date' => $row[10] ?? null,
            'warranty_date'     => $row[11] ?? null,
            'service_contract'  => $row[12] ?? null,
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
