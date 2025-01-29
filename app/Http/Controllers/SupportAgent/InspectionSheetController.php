<?php
namespace App\Http\Controllers\SupportAgent;

use App\Http\Controllers\Controller;
use App\Models\InspectionSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectionSheetController extends Controller
{
    // create inspection sheet
    public function createInspectionSheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id'                   => 'required|string|exists:tickets,id',
            'technician_id'               => 'required|string|exists:users,id',
            'inspection_sheet_type'       => 'nullable|string',
            'support_agent_comment'       => 'nullable|string',
            'technician_comment'          => 'nullable|string',
            'location_employee_signature' => 'nullable|string',
            'image'                       => 'nullable|string',
            'video'                       => 'nullable',
            'status'                      => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }

        // Image upload
        $newImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . uniqid() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/sheet_images'), $imageName);
                $newImages[] = $imageName;
            }
        }

        // Video upload
        $newVideos = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $videoName = time() . uniqid() . '_' . $video->getClientOriginalName();
                $video->move(public_path('uploads/sheet_videos'), $videoName);
                $newVideos[] = $videoName;
            }
        }

        $inspectionSheet = InspectionSheet::create([
            'assigned'                    => auth()->id(),
            'ticket_id'                   => $request->ticket_id,
            'technician_id'               => $request->technician_id,
            'inspection_sheet_type'       => $request->inspection_sheet_type ?? 'New Tickets',
            'support_agent_comment'       => $request->support_agent_comment,
            'technician_comment'          => $request->technician_comment,
            'location_employee_signature' => $request->location_employee_signature ?? null,
            'image'                       => json_encode($newImages),
            'video'                       => json_encode($newVideos),
            'status'                      => $request->status ?? 'New',
        ]);

        // Eager load the related user and asset
        $inspectionSheet->load('user:id,name,address', 'ticket:id,problem,asset_id', 'ticket.asset:id,product,brand,serial_number', 'technician:id,name');

        return response()->json(['status' => true, 'message' => 'Inspection Sheet Created Successfully', 'data' => $inspectionSheet]);
    }
    public function updateInspectionSheet(Request $request, $id)
    {
        $inspection_sheet = InspectionSheet::with('user:id,name,address', 'ticket:id,problem,asset_id', 'ticket.asset:id,product,brand,serial_number', 'technician:id,name')->findOrFail($id);

        if (! $inspection_sheet) {
            return response()->json(['status' => false, 'message' => 'Inspection Sheet Not Found'], 422);
        }
        $validator = Validator::make($request->all(), [
            'ticket_id'                   => 'nullable|string|exists:tickets,id',
            'technician_id'               => 'nullable|string|exists:users,id',
            'inspection_sheet_type'       => 'nullable|string',
            'support_agent_comment'       => 'nullable|string',
            'technician_comment'          => 'nullable|string',
            'location_employee_signature' => 'nullable|string',
            'image'                       => 'nullable|string',
            'video'                       => 'nullable',
            'status'                      => 'nullable',
        ]);
        $validatedData = $validator->validated();

        if (isset($validatedData['status'])) {
            $validatedData['inspection_sheet_type'] = ($validatedData['status'] === 'Completed') ? 'Past Sheets' : 'Open Sheets';
        } elseif ($inspection_sheet->status === 'Completed') {
            $validatedData['inspection_sheet_type'] = 'Past Sheets';
        } else {
            $validatedData['inspection_sheet_type'] = 'Open Sheets';
        }

        $inspection_sheet->update($validatedData);

        return response()->json(['status' => true,
            'message'                         => 'Inspection Sheet Update Successfully',
            'data'                            => $inspection_sheet,
        ]);

    }

}
