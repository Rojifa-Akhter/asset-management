<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    //create asset
    public function createAsset(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'asset_name' => 'required|string',
            'brand_name' => 'nullable|string',
            'QR_code' => 'nullable|string',
            'Unit_Price' => 'nullable|string',
            'Current_Spend' => 'nullable|string',
            'Max_Spend' => 'nullable|string',
            'range' => 'nullable|string',
            'location' => 'nullable|string',
            'manufacture_sno' => 'nullable|string',
            'manufacture_date' => 'nullable|string',
            'installation_date' => 'nullable|string',
            'warranty_date' => 'nullable|string',
            'service_contract' => 'nullable|string',
        ]);
        if ($validator) {
            return response()->json(['status'=>false,'message'=>$validator->errors()],401);
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
        $asset->save();

        return response()->json(['status'=>true, 'message'=>$asset],200);
    }
    //asset update
    public function updateAsset(Request $request,$id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset) {
            return response()->json(['status'=>false, 'message'=>'Asset Not Found'],401);
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
            'manufacture_date' => 'nullable|string',
            'installation_date' => 'nullable|string',
            'warranty_date' => 'nullable|string',
            'service_contract' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $asset->asset_name = $validatedData['asset_name'] ?? $asset->asset_name;
        $asset->brand_name = $validatedData['brand_name'] ?? $asset->brand_name;
        $asset->QR_code = $validatedData['QR_code'] ?? $asset->QR_code;
        $asset->Unit_Price = $validatedData['Unit_Price'] ?? $asset->Unit_Price;
        $asset->Current_Spend = $validatedData['Current_Spend'] ?? $asset->Current_Spend;
        $asset->Max_Spend = $validatedData['Max_Spend'] ?? $asset->Max_Spend;
        $asset->range = $validatedData['range'] ?? $asset->range;
        $asset->location = $validatedData['location'] ?? $asset->location;
        $asset->manufacture_sno = $validatedData['manufacture_sno'] ?? $asset->manufacture_sno;
        $asset->manufacture_date = $validatedData['manufacture_date'] ?? $asset->manufacture_date;
        $asset->installation_date = $validatedData['installation_date'] ?? $asset->installation_date;
        $asset->warranty_date = $validatedData['warranty_date'] ?? $asset->warranty_date;
        $asset->service_contract = $validatedData['service_contract'] ?? $asset->service_contract;

        $asset->save();

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

