<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function createSetting(Request $request)
    {
        // return $request;
        $validator = Validator::make($request->all(),[
            'type' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false, 'message'=> $validator->errors()],401);
        }

        $setting = Setting::updateOrCreate(
            ['type' => $request->type],
            ['description' => $request->description]
        );

        $setting->save();

        return response()->json([
            'status'=>true,
            'message'=>$setting
        ]);

    }
    public function listSetting($type)
    {
        $setting = Setting::where('type', $type)->first();

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => "Setting of type '{$type}' not found.",
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $setting,
        ], 200);
    }
}

