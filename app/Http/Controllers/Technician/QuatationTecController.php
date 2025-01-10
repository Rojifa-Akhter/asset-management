<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Quatation;
use App\Models\InspectionSheet;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuatationTecController extends Controller
{
    public function createQuatation(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'sheet_id' => 'required|exists:inspection_sheets,id',
            'cost' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }

        $inspectionSheet = InspectionSheet::where('id', $request->sheet_id)
            ->where('ticket_id', $request->ticket_id)
            ->first();

        if (!$inspectionSheet) {
            return response()->json([
                'status' => false,
                'message' => 'The provided ticket is not associated with the given inspection sheet.',
            ], 404);
        }

        $quatation = Quatation::create([
            'ticket_id' => $request->ticket_id,
            'sheet_id' => $request->sheet_id,
            'cost' => $request->cost,
            'comment' => $request->comment,
        ]);

        // Fetch the related ticket
        $ticket = Ticket::find($request->ticket_id);

        $responseData = [
            'id' => $quatation->id,
            'ticket' => [
                'id' => $ticket->id,
                'product_name' => $ticket->product_name,
                'serial_no' => $ticket->serial_no,
                'problem' => $ticket->problem,
                'status' => $ticket->status,
                'location' => $ticket->location,
                'image' => $ticket->image ? asset($ticket->image) : null,
                'video' => $ticket->video,
            ],
            'inspection_sheet' => [
                'assigned_by' => $inspectionSheet->assigned_by->name ?? 'N/A',
                'signature' => $inspectionSheet->signature ?? 'N/A',
                'comment' => $inspectionSheet->comment ?? 'N/A',
            ],
            'cost' => $quatation->cost ?? 0,
            'comment' => $quatation->comment ?? 'No comments provided',
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Quotation Created Successfully.',
            'data' => $responseData,
        ], 201);
    }
}
