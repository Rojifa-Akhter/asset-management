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
            if (!$validator) {
                return response()->json(['status'=>false, 'message'=>$validator->errors()],422);
            }
            $job_card = JobCard::create([
                'user_id' => auth()->id(),
                'inspection_sheet_id' => $request->inspection_sheet_id,
                'job_card_type' => $request->job_card_type ?? 'New Cards',
                'support_agent_comment' => $request->support_agent_comment,
                'technician_comment' => $request->technician_comment,
                'location_employee_signature' => $request->location_employee_signature,
                'job_status' => $request->job_status ??'New',
            ]);
            $job_card->load('');
            $job_card->save();

    }
}
