<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id'      => 'required|string|exists:assets,id',
            'problem'       => 'required|string',
            'ticket_name'   => 'nullable|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string',
            'cost'          => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        // Create the ticket
        $ticket = Ticket::create([
            'user_id'       => auth()->id(),
            'asset_id'      => $request->asset_id,
            'problem'       => $request->problem,
            'ticket_name'   => $request->ticket_name ?? 'New Tickets',
            'user_comment'  => $request->user_comment,
            'ticket_status' => $request->ticket_status ?? 'New',
            'cost'          => $request->cost ?? null,
            'order_number'  => $request->order_number,
        ]);

        // Eager load the related user and asset
        $ticket->load('user:id,name,address,phone', 'asset:id,asset_name,brand_name,manufacture_sno');

        // Prepare the response data
        // $responseData = [
        //     'id'           => $ticket->id,
        //     'asset_id'     => $ticket->asset->id ?? null,
        //     'device_name'  => $ticket->asset->asset_name ?? null,
        //     'organization' => $ticket->asset->brand_name ?? null,
        //     'serial no'    => $ticket->asset->manufacture_sno ?? null,
        //     'user_id'      => $ticket->user->id ?? null,
        //     'user_name'    => $ticket->user->name ?? null,
        //     'address'      => $ticket->user->address ?? null,
        //     'phone'        => $ticket->user->phone ?? null,
        //     'problem'      => $ticket->problem,
        // ];

        return response()->json([
            'status'  => true,
            'message' => 'Ticket created successfully.',
            'data'    => $ticket,
        ], 201);
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = Ticket::with('user:id,name,address', 'asset:id,asset_name,brand_name,manufacture_sno')->findOrFail($id);

        // Validate input data
        $validator = Validator::make($request->all(), [
            'asset_id'      => 'nullable|string|exists:assets,id',
            'ticket_name'   => 'nullable|string',
            'problem'       => 'nullable|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string|in:New,Assigned,Inspection,Awaiting PO,Job Card Created,Completed',
            'cost'          => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        $validatedData = $validator->validated();

        if (isset($validatedData['ticket_status'])) {

            $validatedData['ticket_name'] = ($validatedData['ticket_status'] === 'Completed') ? 'Past Tickets' : 'Open Tickets';
        } elseif ($ticket->ticket_status === 'Completed') {
            $validatedData['ticket_name'] = 'Past Tickets';
        } else {
            $validatedData['ticket_name'] = 'Open Tickets';
        }

        // Update ticket fields
        $ticket->update($validatedData);
        // $ticket->load('user:id,name', 'asset:id,asset_name,brand_name,manufacture_sno');

        return response()->json([
            'status'  => true,
            'message' => 'Ticket updated successfully.',
            'data'    => $ticket,
        ], 200);
    }

    //all ticket list with status
    public function ticketList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search');
        // $sortBy = $request->input('sort_by');
        $tickeLtist = Ticket::with('user:id,name,address', 'asset:id,asset_name,brand_name,manufacture_sno');
        //search
        if ($search) {
            $tickeLtist = $tickeLtist->where('ticket_name', $search);
        }
        // Apply role filter
        if (! empty($filter)) {
            $tickeLtist->where('ticket_status', $filter);
        }
        // $tickets = $tickeLtist;
        $tickeLtist = $tickeLtist->paginate($perPage);
        return response()->json([
            'status' => true,
            'data'   => $tickeLtist,

        ]);
    }

    //get ticket details
    public function ticketDetails(Request $request, $id)
    {
        $ticket = Ticket::with(['asset', 'user', 'technician'])->find($id);

        if (! $ticket) {
            return response()->json(['status' => false, 'message' => 'Ticket Not Found'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'ticket_number' => $ticket->id,
                'device_name'   => $ticket->asset->asset_name ?? null,
                'organization'  => $ticket->asset->brand_name ?? null,
                'serial_number' => $ticket->asset->manufacture_sno ?? null,
                'location'      => $ticket->user->address ?? 'N/A',
                'problem'       => $ticket->problem ?? null,
                'cost'          => $ticket->cost ?? null,

            ],
        ]);
    }

//delete ticket
    public function deleteTicket($id)
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found.'], 404);
        }

        // $images = is_array($ticket->image) ? $ticket->image : json_decode($ticket->image, true);
        // if ($images) {
        //     foreach ($images as $imagePath) {
        //         $filePath = public_path('uploads/ticket_images/' . $imagePath);
        //         if (file_exists($filePath)) {
        //             unlink($filePath);
        //         }
        //     }
        // }

        // $videos = is_array($ticket->video) ? $ticket->video : json_decode($ticket->video, true);
        // if ($videos) {
        //     foreach ($videos as $videoPath) {
        //         $filePath = public_path('uploads/ticket_videos/' . $videoPath);
        //         if (file_exists($filePath)) {
        //             unlink($filePath);
        //         }
        //     }
        // }

        $ticket->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Ticket deleted successfully',
        ], 200);
    }

}
