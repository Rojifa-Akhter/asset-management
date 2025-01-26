<?php
namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    //create asset
    public function createAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_name'        => 'required|string|max:255',
            'brand_name'        => 'nullable|string',
            'qr_code'           => 'nullable|string',
            'unit_price'        => 'nullable|string',
            'current_spend'     => 'nullable|string',
            'max_spend'         => 'nullable|string',
            'range'             => 'nullable|string',
            'location'          => 'nullable|string',
            'manufacture_sno'   => 'nullable|string',
            'manufacture_date'  => 'nullable|date',
            'installation_date' => 'nullable|date',
            'warranty_date'     => 'nullable|date',
            'service_contract'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $asset = Asset::create([
            'organization_id'   => Auth::user()->id,
            'asset_name'        => $request->asset_name,
            'brand_name'        => $request->brand_name,
            'qr_code'           => $request->qr_code,
            'unit_price'        => $request->unit_price,
            'current_spend'     => $request->current_spend,
            'max_spend'         => $request->max_spend,
            'range'             => $request->range,
            'location'          => $request->location,
            'manufacture_sno'   => $request->manufacture_sno,
            'manufacture_date'  => $request->manufacture_date,
            'installation_date' => $request->installation_date,
            'warranty_date'     => $request->warranty_date,
            'service_contract'  => $request->service_contract,
        ]);

        return response()->json([
            'status'  => true,
            'message' => $asset,
        ], 200);
    }

    //asset update
    public function updateAsset(Request $request, $id)
    {
        try {
            $asset = Asset::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Asset Not Found',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'asset_name'        => 'nullable|string',
            'brand_name'        => 'nullable|string',
            'qr_code'           => 'nullable|string',
            'unit_price'        => 'nullable|string',
            'current_spend'     => 'nullable|string',
            'max_spend'         => 'nullable|string',
            'range'             => 'nullable|string',
            'location'          => 'nullable|string',
            'manufacture_sno'   => 'nullable|string',
            'manufacture_date'  => 'nullable|date',
            'installation_date' => 'nullable|date',
            'warranty_date'     => 'nullable|date',
            'service_contract'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $asset->update($validatedData);

        return response()->json([
            'status'  => true,
            'message' => 'Asset updated successfully.',
            'data'    => $asset,
        ], 200);
    }

    //asset list
    public function assetList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search');
        $sortBy  = $request->input('sort_by');

        $assetlist = Asset::query();

        // Apply search filter
        if (! empty($search)) {
            $assetlist->where(function ($query) use ($search) {
                $query->where('asset_name', 'like', "%$search%")
                    ->orWhere('qr_code', 'like', "%$search%")
                    ->orWhere('warranty_date', 'like', "%$search%")
                    ->orWhere('unit_price', 'like', "%$search%")
                    ->orWhere('current_spend', 'like', "%$search%")
                    ->orWhere('max_spend', 'like', "%$search%");
            });
        }

        // Apply sorting
        if (! empty($sortBy)) {
            if ($sortBy == 'address') {
                $assetlist->orderBy('address', 'asc');
            }
        }

        $assets = $assetlist->paginate($perPage);

        // Map customized response
        $data = $assets->getCollection()->map(function ($asset) {
            return [
                'id'            => $asset->id,
                'name'          => $asset->asset_name,
                'qr_code'       => $asset->qr_code,
                'warranty_date' => $asset->warranty_date,
                'unit_price'    => $asset->unit_price,
                'current_spend' => $asset->current_spend,
                'max_spend'     => $asset->max_spend,
            ];
        });

        // Replace the original collection with the mapped data
        $assets->setCollection(collect($data));

        return response()->json([
            'status' => true,
            'data'   => $assets,
        ]);
    }

    //asset details
    public function assetDetails(Request $request, $id)
    {
        $asset = Asset::find($id);

        if (! $asset) {
            return response()->json(['status' => false, 'message' => 'Asset Not Found'], 401);
        }
        $data = [
            'id'                        => $asset->id,
            'asset name'                => $asset->asset_name,
            'brand name'                => $asset->brand_name,
            'range'                     => $asset->range,
            'location'                  => $asset->address,
            'manufacture serial number' => $asset->manufacture_sno,
            'manufacture date'          => $asset->manufacture_date,
            'installation date'         => $asset->installation_date,
            'warranty end date'         => $asset->warranty_date,
            'service contract'          => $asset->service_contract,
        ];
        return response()->json(['status' => true, 'message' => $data]);
    }
    //asset list
    public function assetListAdmin(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search');
        $sortBy  = $request->input('sort_by');

        $assetlist = Asset::query()->with('organization:id,name');

        // Apply search filter
        if (! empty($search)) {
            $assetlist->where(function ($query) use ($search) {
                $query->where('asset_name', 'like', "%$search%")
                    ->orWhere('qr_code', 'like', "%$search%")
                    ->orWhere('warranty_date', 'like', "%$search%")
                    ->orWhere('unit_price', 'like', "%$search%")
                    ->orWhere('current_spend', 'like', "%$search%")
                    ->orWhere('max_spend', 'like', "%$search%");            });
        }

        // Apply sorting
        if (! empty($sortBy)) {
            if ($sortBy == 'asset_name') {
                $assetlist->orderBy('asset_name', 'asc');
            } elseif ($sortBy == 'qr_code') {
                $assetlist->orderBy('qr_code', 'asc');
            } elseif ($sortBy == 'warranty_date') {
                $assetlist->orderBy('warranty_date', 'asc');
            } elseif ($sortBy == 'unit_price') {
                $assetlist->orderBy('unit_price', 'asc');
            } elseif ($sortBy == 'current_spend') {
                $assetlist->orderBy('current_spend', 'asc');
            }elseif ($sortBy == 'organization') {
                $assetlist->orderBy('max_spend', 'asc');
            }
        }

        $assets = $assetlist->paginate($perPage);

        // Map customized response with organization name
        $data = $assets->getCollection()->map(function ($asset) {
            return [
                'id'                => $asset->id,
                'name'              => $asset->asset_name,
                'qr_code'           => $asset->qr_code,
                'warranty_date'     => $asset->warranty_date,
                'unit_price'        => $asset->unit_price,
                'current_spend'     => $asset->current_spend,
                'max_spend'         => $asset->max_spend,
                'organization' => $asset->organization->name ?? 'N/A', // Organization or third party name
            ];
        });

        // Replace the original collection with the mapped data
        $assets->setCollection(collect($data));

        return response()->json([
            'status' => true,
            'data'   => $assets,
        ]);
    }

    //asset delete
    public function deleteAsset($id)
    {
        $asset = Asset::find($id);

        if (! $asset) {
            return response()->json(['status' => 'error', 'message' => 'Asset not found.'], 404);
        }

        $asset->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Asset deleted successfully.',
        ], 200);
    }
}
