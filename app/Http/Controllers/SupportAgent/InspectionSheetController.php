<?php

namespace App\Http\Controllers\SupportAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectionSheetController extends Controller
{
    public function createInspectionSheet(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'ticket_id' => 'required|string|exists:tickets,id',
            'technician_id' => 'required|string|exists:tickets,id',
            'checklist' => 'nullable|string'

        ]);
    }
}
