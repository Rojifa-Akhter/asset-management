<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\InspectionSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectinSheetController extends Controller
{
    //insprction sheet create
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
            'status' => true,
            'message' => 'Inspection sheet created successfully.',
            'data' => $responseData,
        ], 201);
    }

    //update inspection sheet
    public function updateInspectionSheet(Request $request, $id)
    {
        $sheet = InspectionSheet::find($id);

        if (!$sheet) {
            return response()->json([
                'status' => false,
                'message' => 'Inspection Sheet not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ticket_id' => 'nullable|exists:tickets,id',
            'assigned_by' => 'nullable|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'comment' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        // Validate roles if applicable
        if (!empty($validatedData['assigned_by'])) {
            $supportAgent = User::find($validatedData['assigned_by']);
            if ($supportAgent?->role !== 'Support Agent') {
                return response()->json([
                    'status' => false,
                    'message' => 'Assigned by user must have the role Support Agent.',
                ], 422);
            }
            $sheet->assigned_by = $validatedData['assigned_by'];
        }


        if (isset($validatedData['technician_id'])) {
            $technician = User::find($validatedData['technician_id']);
            if ($technician && $technician->role !== 'Technician') {
                return response()->json([
                    'status' => false,
                    'message' => 'Technician must have the role Technician.',
                ], 422);
            }
            $sheet->technician_id = $validatedData['technician_id'];
        }

        $sheet->ticket_id = $validatedData['ticket_id'] ?? $sheet->ticket_id;
        $sheet->location = $validatedData['location'] ?? $sheet->location;
        $sheet->comment = $validatedData['comment'] ?? $sheet->comment;
        $sheet->signature = $validatedData['signature'] ?? $sheet->signature;

        $sheet->save();

        return response()->json([
            'status' => true,
            'message' => 'Inspection Sheet updated successfully.',
            'data' => $sheet,
        ], 200);
    }
    //inspection sheet list
    public function InspectionSheetList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $sheets = InspectionSheet::with(['ticket', 'assignedBy', 'technician'])->paginate($perPage);

        // Transform data into the desired structure
        $transformedSheets = $sheets->getCollection()->map(function ($sheet) {
            return [
                'id' => $sheet->id,
                'ticket' => [
                    'id' => $sheet->ticket->id ?? Null,
                    'product_name' => $sheet->ticket->product_name ?? Null,
                    'serial_no' => $sheet->ticket->serial_no ?? Null,
                    'problem' => $sheet->ticket->problem ?? Null,
                ],
                'assigned_by' => [
                    'name' => $sheet->assignedBy->name ?? Null,
                ],
                'technician' => [
                    'name' => $sheet->technician?->name ?? Null,
                ],
                'location' => $sheet->location ?? Null,
                'comment' => $sheet->comment ?? Null,
                'signature' => $sheet->signature ?? Null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'current_page' => $sheets->currentPage(),
                'data' => $transformedSheets,
                'total' => $sheets->total(),
                'per_page' => $sheets->perPage(),
                'last_page' => $sheets->lastPage(),
            ],
        ]);
    }

    public function deleteInspectionSheet(Request $request, $id)
    {
        $sheet = InspectionSheet::find($id);

        if (!$sheet) {
            return response()->json(['status' => 'error', 'message' => 'Inspection Sheet not found.'], 422);
        }

        $sheet->delete();

        return response()->json([
            'status' => true,
            'message' => 'Inspection Sheet deleted successfully.',
        ], 200);
    }

}
