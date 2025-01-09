<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\InspectionSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectinSheetController extends Controller
{
    public function createSheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'assigned_by' => 'required|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'comment' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        // Check roles
        $supportAgent = User::find($request->assigned_by);
        $technician = User::find($request->technician_id);

        if ($supportAgent->role !== 'Support Agent') {
            return response()->json(['status' => false, 'message' => 'Assigned by user must have the role Support Agent.'], 422);
        }

        if ($technician->role !== 'Technician') {
            return response()->json(['status' => false, 'message' => 'Technician must have the role Technician.'], 422);
        }

        $inspectionSheet = InspectionSheet::create([
            'ticket_id' => $request->ticket_id,
            'assigned_by' => $request->assigned_by,
            'technician_id' => $request->technician_id,
            'location' => $request->location,
            'comment' => $request->comment,
            'signature' => $request->signature,
        ]);

        $responseData = [
            'id' => $inspectionSheet->id,
            'ticket' => [
                'id' => $inspectionSheet->ticket->id,
                'product_name' => $inspectionSheet->ticket->product_name,
                'serial_no' => $inspectionSheet->ticket->serial_no,
                'problem' => $inspectionSheet->ticket->problem,
                // 'image' => $inspectionSheet->ticket->image ? asset('storage/' . $ticket->image) : null,

                // 'location' => $inspectionSheet->ticket->location,
            ],
            'assigned_by' => [
                'name' => $supportAgent->name,
                // 'email' => $supportAgent->email,
            ],
            'technician' => [
                'name' => $technician->name,
                // 'email' => $technicianUser->email,
            ],
            'location' => $inspectionSheet->location,
            'comment' => $inspectionSheet->comment,
            'signature' => $inspectionSheet->signature,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Inspection sheet created successfully.',
            'data' => $responseData,
        ], 201);
    }

}
