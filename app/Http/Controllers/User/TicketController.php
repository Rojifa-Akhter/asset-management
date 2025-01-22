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
            'technician_id' => 'nullable|string|exists:users,id',
            'problem'       => 'required|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string',
            'price'         => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $ticket = Ticket::create([
            'user_id'       => auth()->id(),
            'asset_id'      => $request->asset_id,
            'technician_id' => $request->technician_id,
            'problem'       => $request->problem,
            'user_comment'  => $request->user_comment,
            'ticket_status' => $request->ticket_status ?? 'New',
            'price'         => $request->price,
            'order_number'  => $request->order_number,
        ]);

        $user  = $ticket->user;  //  Ticket model`user` relationship defined.
        $asset = $ticket->asset;

        $responseData = [
            'id'           => $ticket->id,
            'asset_id'     => $asset->id ?? null,
            'device_name'  => $asset->asset_name ?? null,
            'serial_no'    => $asset->manufacture_sno ?? null,
            'user_address' => $user->address ?? null,
            'problem'      => $ticket->problem,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Ticket created successfully.',
            'data'    => $responseData,$ticket
        ], 201);
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'asset_id'      => 'nullable|string|exists:assets,id',
            'technician_id' => 'nullable|string|exists:users,id',
            'problem'       => 'nullable|string',
            'user_comment'  => 'nullable|string',
            'ticket_status' => 'nullable|string',
            'price'         => 'nullable|string',
            'order_number'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        // Update ticket fields
        $ticket->update($validatedData);

        $asset = $ticket->asset;
        $responseData = [
            'id'            => $ticket->id,
            'asset_id'      => $asset->id ?? null,
            'device_name'   => $asset->asset_name ?? null,
            'serial_no'     => $asset->manufacture_sno ?? null,
            'problem'       => $ticket->problem,
            'user_comment'  => $ticket->user_comment,
            'ticket_status' => $ticket->ticket_status,
            'price'         => $ticket->price,
            'order_number'  => $ticket->order_number,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Ticket updated successfully.',
            'data'    => $responseData,
        ], 200);
    }

    //all ticket list
    public function ticketList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tickets = Ticket::paginate($perPage);

        return response()->json([
            'status' => true,
            'data'   => $tickets,
        ]);
    }
//get ticket details
    public function ticketDetails(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            return response()->json(['status' => false, 'message' => 'Ticket Not Found'], 401);
        }
        return response()->json(['status' => true, 'message' => $ticket]);
    }
//delete ticket
    public function deleteTicket($id)
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found.'], 404);
        }

        $images = is_array($ticket->image) ? $ticket->image : json_decode($ticket->image, true);
        if ($images) {
            foreach ($images as $imagePath) {
                $filePath = public_path('uploads/ticket_images/' . $imagePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $videos = is_array($ticket->video) ? $ticket->video : json_decode($ticket->video, true);
        if ($videos) {
            foreach ($videos as $videoPath) {
                $filePath = public_path('uploads/ticket_videos/' . $videoPath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $ticket->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Ticket deleted successfully, along with associated images and videos.',
        ], 200);
    }

}
