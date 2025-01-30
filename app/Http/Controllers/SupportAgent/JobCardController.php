<?php
namespace App\Http\Controllers\SupportAgent;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobCardController extends Controller
{
    public function createJobCard(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'ticket_id'                   => 'nullable|string|exists:tickets,id',
                'inspection_sheet_id'         => 'required|string|exists:inspection_sheets,id',
                'job_card_type'               => 'nullable|string',
                'support_agent_comment'       => 'nullable|string',
                'technician_comment'          => 'nullable|string',
                'location_employee_signature' => 'nullable|string',
                'job_status'                  => 'nullable|string',
            ]);
        if (! $validator) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }
        $job_card = JobCard::create([
            'support_agent_id'            => auth()->id(),
            'ticket_id'                   => $request->ticket_id,
            'inspection_sheet_id'         => $request->inspection_sheet_id,
            'job_card_type'               => $request->job_card_type ?? 'New Cards',
            'support_agent_comment'       => $request->support_agent_comment,
            'technician_comment'          => $request->technician_comment,
            'location_employee_signature' => $request->location_employee_signature,
            'job_status'                  => $request->job_status ?? 'New',
        ]);
        $job_card->load(
            'supportAgent:id,name',
            'inspectionSheet:id,ticket_id,technician_id',
            'inspectionSheet.technician:id,name',
            'inspectionSheet.ticket:id,asset_id,problem,order_number,cost,user_id',
            'inspectionSheet.ticket.asset:id,product,brand,serial_number',
            'inspectionSheet.ticket.user:id,name,address,phone'

        );

        $job_card->save();

        return response()->json(['status' => true, 'message' => 'Job Card Create Successfully', 'data' => $job_card], 201);

    }
    public function updateJobCard(Request $request, $id)
    {
        $job_card = JobCard::with('supportAgent:id,name',
            'inspectionSheet:id,ticket_id,technician_id',
            'inspectionSheet.technician:id,name',
            'inspectionSheet.ticket:id,asset_id,problem,order_number,cost,user_id',
            'inspectionSheet.ticket.asset:id,product,brand,serial_number',
            'inspectionSheet.ticket.user:id,name,address,phone')->findOrFail($id);

        if (! $job_card) {
            return response()->json(['status' => false, 'message' => 'Job Card Not Found'], 422);
        }
        $validator = Validator::make($request->all(), [
            'job_card_type'               => 'nullable|string',
            'support_agent_comment'       => 'nullable|string',
            'technician_comment'          => 'nullable|string',
            'location_employee_signature' => 'nullable|string',
            'job_status'                  => 'nullable|string|in:New,Assigned,In Progress,On hold,Cancel,To be allocated,Awaiting courier
                                                                ,Collected by courier,Parts required,Picking,To be invoiced,Invoiced,Completed',
        ]);
        $validatedData = $validator->validated();

        if (isset($validatedData['job_status'])) {
            $validatedData['job_card_type'] = ($validatedData['job_status'] === 'Completed') ? 'Past Cards' : 'Open Cards';
        } elseif ($job_card->job_status === 'Completed') {
            $validatedData['job_card_type'] = 'Past Cards';
        } else {
            $validatedData['job_card_type'] = 'Open Cards';
        }
        $job_card->update($validatedData);

        return response()->json([
            'status'  => true,
            'message' => 'Inspection Sheet Update Successfully',
            'data'    => $job_card,
        ]);
    }

}
