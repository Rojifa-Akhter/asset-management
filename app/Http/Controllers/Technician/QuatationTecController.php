<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\InspectionSheet;
use App\Models\Quatation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuatationTecController extends Controller
{
    //quatation create api
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

        $inspectionSheets = InspectionSheet::where('id', $request->sheet_id)
            ->where('ticket_id', $request->ticket_id)
            ->first();

        if (!$inspectionSheets) {
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
            'ticket_id' => $ticket->id,
            'product_name' => $ticket->product_name,
            'serial_no' => $ticket->serial_no,
            'problem' => $ticket->problem,
            'status' => $ticket->status,
            'location' => $ticket->location,
            'image' => $ticket->image,
            'video' => $ticket->video,
            'assigned_by' => $inspectionSheets->assignedBy->name ?? 'N/A', // Fix here
            'signature' => $inspectionSheets->signature ?? 'N/A',
            'cost' => $quatation->cost ?? 0,
            'comment' => $quatation->comment ?? 'No comments provided',
        ];

        return response()->json([
            'status' => true,
            'message' => 'quatation Created Successfully.',
            'data' => $responseData,
        ], 201);
    }
    //update quatation
    public function updateQuatation(Request $request, $id)
    {
        $quatation = Quatation::find($id);

        if (!$quatation) {
            return response()->json(['status' => false, 'message' => 'quatation Not Found'], 401);
        }

        $validator = Validator::make($request->all(), [
            'ticket_id' => 'nullable|exists:tickets,id',
            'sheet_id' => 'nullable|exists:inspection_sheets,id',
            'cost' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $quatation->ticket_id = $validatedData['ticket_id'] ?? $quatation->ticket_id;
        $quatation->sheet_id = $validatedData['sheet_id'] ?? $quatation->sheet_id;
        $quatation->cost = $validatedData['cost'] ?? $quatation->cost;
        $quatation->comment = $validatedData['comment'] ?? $quatation->comment;

        $quatation->save();

        $ticket = Ticket::find($quatation->ticket_id);
        $inspectionSheets = InspectionSheet::where('id', $quatation->sheet_id)
            ->where('ticket_id', $quatation->ticket_id)
            ->first();

        if (!$inspectionSheets) {
            return response()->json([
                'status' => false,
                'message' => 'The provided inspection sheet is not associated with the given ticket.',
            ], 404);
        }

        $responseData = [
            'id' => $quatation->id,
            'ticket_id' => $ticket->id,
            'product_name' => $ticket->product_name,
            'serial_no' => $ticket->serial_no,
            'problem' => $ticket->problem,
            'status' => $ticket->status,
            'location' => $ticket->location,
            'image' => $ticket->image,
            'video' => $ticket->video,
            'assigned_by' => $inspectionSheets->assignedBy->name ?? 'N/A',
            'signature' => $inspectionSheets->signature ?? 'N/A',
            'cost' => $quatation->cost ?? 0,
            'comment' => $quatation->comment ?? 'No comments provided',
        ];

        return response()->json([
            'status' => true,
            'message' => 'quatation Updated Successfully.',
            'data' => $responseData,
        ], 200);
    }
//delete quatation
    public function deleteQuatation(Request $request, $id)
    {
        $quatation = Quatation::find($id);

        if (!$quatation) {
            return response()->json(['status' => 'error', 'message' => 'Quatation not found.'], 422);
        }

        $quatation->delete();

        return response()->json([
            'status' => true,
            'message' => 'Quatation deleted successfully.',
        ], 200);
    }
    //quatation list all
    public function quatationList(Request $request, $id = null)
    {
        $perPage = $request->input('per_page', 10);

        $query = Quatation::with(['ticket', 'inspectionSheet.assignedBy']);
        if ($id) {
            $query->where('ticket_id', $id);
        }

        $quatations = $query->paginate($perPage);

        $data = $quatations->map(function ($quatation) {
            return [
                'id' => $quatation->id,
                'ticket_id' => $quatation->ticket->id,
                'product_name' => $quatation->ticket->product_name,
                'serial_no' => $quatation->ticket->serial_no,
                'problem' => $quatation->ticket->problem,
                'image' => $quatation->ticket->image,
                'video' => $quatation->ticket->video,
                'status' => $quatation->ticket->status,
                'location' => $quatation->ticket->location,
                'assigned_by' => $quatation->inspectionSheet->assignedBy->name ?? 'N/A',
                'signature' => $quatation->inspectionSheet->signature ?? 'N/A',
                'cost' => $quatation->cost,
                'comment' => $quatation->comment,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Quatations List.',
            'data' => $data,
            'pagination' => [
                'current_page' => $quatations->currentPage(),
                'total' => $quatations->total(),
                'per_page' => $quatations->perPage(),
                'last_page' => $quatations->lastPage(),
            ],
        ], 200);
    }
    //get details quatation
    public function quatationDetails(Request $request, $id)
    {
        $quatation = Quatation::with(['ticket', 'inspectionSheet.assignedBy'])->find($id);

        if (!$quatation) {
            return response()->json(['status' => false, 'message' => 'Quatation Not Found'], 404);
        }

        $responseData = [
            'id' => $quatation->id,
            'ticket_id' => $quatation->ticket->id,
            'product_name' => $quatation->ticket->product_name,
            'serial_no' => $quatation->ticket->serial_no,
            'problem' => $quatation->ticket->problem,
            'image' => $quatation->ticket->image,
            'video' => $quatation->ticket->video,
            'assigned_by' => $quatation->inspectionSheet->assignedBy->name,
            'signature' => $quatation->inspectionSheet->signature,
            'cost' => $quatation->cost,
            'comment' => $quatation->comment,
        ];

        return response()->json([
            'status' => true,
            'data' => $responseData,
        ], 200);
    }


}
