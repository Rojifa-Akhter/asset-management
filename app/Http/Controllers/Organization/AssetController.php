<?php

namespace App\Http\Controllers\Organization;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssetController extends Controller
{
    //create asset
    public function createAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string',
            'QR_code' => 'nullable|string',
            'Unit_Price' => 'nullable|string',
            'Current_Spend' => 'nullable|string',
            'Max_Spend' => 'nullable|string',
            'range' => 'nullable|string',
            'location' => 'nullable|string',
            'manufacture_sno' => 'nullable|string',
            'manufacture_date' => 'nullable|date',
            'installation_date' => 'nullable|date',
            'warranty_date' => 'nullable|date',
            'service_contract' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $asset = Asset::create([
            'asset_name' => $request->asset_name,
            'brand_name' => $request->brand_name,
            'QR_code' => $request->QR_code,
            'Unit_Price' => $request->Unit_Price,
            'Current_Spend' => $request->Current_Spend,
            'Max_Spend' => $request->Max_Spend,
            'range' => $request->range,
            'location' => $request->location,
            'manufacture_sno' => $request->manufacture_sno,
            'manufacture_date' => $request->manufacture_date,
            'installation_date' => $request->installation_date,
            'warranty_date' => $request->warranty_date,
            'service_contract' => $request->service_contract,
        ]);

        return response()->json([
            'status' => true,
            'message' => $asset
        ], 200);
    }

    //asset update
    public function updateAsset(Request $request, $id)
    {
        try {
            $asset = Asset::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Asset Not Found',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'asset_name' => 'nullable|string',
            'brand_name' => 'nullable|string',
            'QR_code' => 'nullable|string',
            'Unit_Price' => 'nullable|string',
            'Current_Spend' => 'nullable|string',
            'Max_Spend' => 'nullable|string',
            'range' => 'nullable|string',
            'location' => 'nullable|string',
            'manufacture_sno' => 'nullable|string',
            'manufacture_date' => 'nullable|date',
            'installation_date' => 'nullable|date',
            'warranty_date' => 'nullable|date',
            'service_contract' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $asset->update($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Asset updated successfully.',
            'data' => $asset,
        ], 200);
    }

    //asset list
    public function assetList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $assets = Asset::paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $assets,
        ]);
    }
    //asset details
    public function assetDetails(Request $request, $id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['status'=> false , 'message'=>'Asset Not Found'],401);
        }
        return response()->json(['status'=>true, 'message'=>$asset]);
    }
    //asset delete
    public function deleteAsset($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['status' => 'error', 'message' => 'Asset not found.'], 404);
        }


        $asset->delete();

        return response()->json([
            'status' => true,
            'message' => 'Asset deleted successfully.',
        ], 200);
    }
}

