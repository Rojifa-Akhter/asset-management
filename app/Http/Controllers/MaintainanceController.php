<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;

class MaintainanceController extends Controller
{
    public function TechnicianGet(){
        $technicians=User::where('role','technician')->select('id','name','image','email','role')->get();
        return response()->json([
            'status'=>true,
            'message'=>'Technician retrieve successfully.',
            'data'=>$technicians,
        ]);
    }

    public function assetGet(){
        $assets=Asset::select('id','product')->get();
        return response()->json([
            'status'=>true,
            'message'=>'Asset retrieve successfully.',
            'data'=>$assets,
        ]);
    }
}
