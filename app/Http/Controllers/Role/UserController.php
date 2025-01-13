<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Quatation;
use App\Models\Asset;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //user can get quatation
    public function getQuatation(Request $request,$id)
    {

        // return 8;
        $quatation = Quatation::with(['asset'])->find($id);

        if (!$quatation) {
            return response()->json(['status' => false, 'message' => 'Quotation Not Found'], 404);
        }

        $responseData = [
            'id' => $quatation->id,
            'asset_id' => $quatation->ticket->id,
            'product_name' => $quatation->ticket->product_name,
            'serial_no' => $quatation->ticket->serial_no,
            'location' => $quatation->ticket->location,
            'problem' => $quatation->ticket->problem,
            'cost' => $quatation->cost,
        ];

        return response()->json([
            'status' => true,
            'data' => $responseData,
        ], 200);
    }
    //send quatation with user information

}
