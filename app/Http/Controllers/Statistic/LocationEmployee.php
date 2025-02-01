<?php

namespace App\Http\Controllers\Statistic;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationEmployee extends Controller
{
    public function dashboard(){
        $assets=Asset::all();
        $data=$assets;
        return response()->json([
            'status'=>true,
            'message'=>'Data retrieve successfully',
            'data'=>$data,
        ]);
    }
}
