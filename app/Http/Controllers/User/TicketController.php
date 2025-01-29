<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    //create ticket
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id'      => 'required|string|exists:assets,id',
            'problem'       => 'required|string',
            'ticket_type'   => 'nullable|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string',
            'cost'          => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $ticket = Ticket::create([
            'user_id'       => auth()->id(),
            'asset_id'      => $request->asset_id,
            'problem'       => $request->problem,
            'ticket_type'   => $request->ticket_type ?? 'New Tickets',
            'user_comment'  => $request->user_comment,
            'ticket_status' => $request->ticket_status ?? 'New',
            'cost'          => $request->cost ?? null,
            'order_number'  => $request->order_number,
        ]);

        // Eager load the related user and asset
        $ticket->load('user:id,name,address,phone', 'asset:id,product,brand,serial_number');

        return response()->json([
            'status'  => true,
            'message' => 'Ticket created successfully.',
            'data'    => $ticket,
        ], 201);
    }

    //update ticket
    public function updateTicket(Request $request, $id)
    {
        $ticket = Ticket::with('user:id,name,address,phone', 'asset:id,product,brand,serial_number')->findOrFail($id);

        if (!$ticket) {
            return response()->json(['status'=>false, 'message'=>'Ticket not Found'],422);
        }

        $validator = Validator::make($request->all(), [
            'asset_id'      => 'nullable|string|exists:assets,id',
            'ticket_type'   => 'nullable|string',
            'problem'       => 'nullable|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string|in:New,Assigned,Inspection sheet,Awaiting purchase order,Job card created,Completed',
            'cost'          => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        $validatedData = $validator->validated();

        if (isset($validatedData['ticket_status'])) {
            $validatedData['ticket_type'] = ($validatedData['ticket_status'] === 'Completed') ? 'Past Tickets' : 'Open Tickets';
        } elseif ($ticket->ticket_status === 'Completed') {
            $validatedData['ticket_type'] = 'Past Tickets';
        } else {
            $validatedData['ticket_type'] = 'Open Tickets';
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
        $perPage    = $request->input('per_page', 10);
        $search     = $request->input('search');
        $filter     = $request->input('filter');
        $ticketList = Ticket::with('user:id,name,address,phone', 'asset:id,product,brand,serial_number');
        //search
        if ($search) {
            $ticketList = $ticketList->where('ticket_type', $search);
        }
        // Apply role filter
        if (! empty($filter)) {
            $ticketList->where('ticket_status', $filter);
        }
        $ticketList = $ticketList->paginate($perPage);
        return response()->json([
            'status' => true,
            'data'   => $ticketList,

        ]);
    }

    //get ticket details
    public function ticketDetails(Request $request, $id)
    {
        $ticket = Ticket::with('user:id,name,address', 'asset:id,product,brand,serial_number')->find($id);

        if (! $ticket) {
            return response()->json(['status' => false, 'message' => 'Ticket Not Found'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $ticket,
        ]);
    }

//delete ticket
    public function deleteTicket($id)
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found.'], 404);
        }

        $ticket->delete();

        return response()->json([
            'status' => true, 'message' => 'Ticket deleted successfully'], 200);
    }
    //get ticket details for inspection sheet
    public function getTicketDetails(Request $request, $id)
    {
        $ticket = Ticket::with('user:id,name,address', 'asset:id,product,brand,serial_number')->find($id);

        if (! $ticket) {
            return response()->json(['status' => false, 'message' => 'Ticket Not Found'], 404);
        }
        $data = [
            'id'            => $ticket->id,
            'asset'         => $ticket->asset,
            'user'          => $ticket->user,
            'problem'       => $ticket->problem,
            'ticket_status' => $ticket->status,
        ];

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

}
