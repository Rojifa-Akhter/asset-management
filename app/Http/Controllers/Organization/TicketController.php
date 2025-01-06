<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'serial_no' => 'required|string|max:255',
            'problem' => 'required|string',
            'location' => 'nullable|string|max:255',
            'comment' => 'required|string',
            'ticket_no' => 'required|string|max:255|unique:tickets,ticket_no',
            'image' => 'nullable|array',
            // 'image.*' => 'file|mimes:jpeg,png,jpg,gif',
            'video' => 'nullable|array',
            // 'video.*' => 'file|mimes:mp4,mkv,avi',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $images = [];
        if ($request->has('image')) {
            foreach ($request->file('image') as $file) {
                $path = $file->store('ticket-images', 'public');
                $images[] = $path;
            }
        }

        $videos = [];
        if ($request->has('video')) {
            foreach ($request->file('video') as $file) {
                $path = $file->store('ticket-videos', 'public');
                $videos[] = $path;
            }
        }

        $ticket = Ticket::create([
            'product_name' => $request->product_name,
            'serial_no' => $request->serial_no,
            'problem' => $request->problem,
            'location' => $request->location,
            'comment' => $request->comment,
            'ticket_no' => $request->ticket_no,
            'image' => json_encode($images),
            'video' => json_encode($videos),
        ]);

        $ticket->save();

        $imageUrls = array_map(fn($path) => asset('storage/' . $path), $images);
        $videoUrls = array_map(fn($path) => asset('storage/' . $path), $videos);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully',
            'data' => [
                'id' => $ticket->id,
                'product_name' => $ticket->product_name,
                'serial_no' => $ticket->serial_no,
                'problem' => $ticket->problem,
                'location' => $ticket->location,
                'comment' => $ticket->comment,
                'ticket_no' => $ticket->ticket_no,
                'image' => $imageUrls,
                'video' => $videoUrls,
            ],
        ], 201);
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'problem' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'ticket_no' => 'nullable|string|max:255|unique:tickets,ticket_no,' . $ticket->id,
            'image' => 'nullable|array',
            'video' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $ticket->product_name = $validatedData['product_name'] ?? $ticket->product_name;
        $ticket->serial_no = $validatedData['serial_no'] ?? $ticket->serial_no;
        $ticket->problem = $validatedData['problem'] ?? $ticket->problem;
        $ticket->location = $validatedData['location'] ?? $ticket->location;
        $ticket->comment = $validatedData['comment'] ?? $ticket->comment;
        $ticket->ticket_no = $validatedData['ticket_no'] ?? $ticket->ticket_no;

        //image updates
        $images = json_decode($ticket->image, true) ?? [];
        if ($request->hasFile('image')) {
            // Delete old images
            foreach ($images as $oldImage) {
                $oldImagePath = str_replace(asset('storage/'), '', $oldImage);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            // Store new images
            $images = [];
            foreach ($request->file('image') as $file) {
                $path = $file->store('ticket-images', 'public');
                $images[] = $path;
            }
            $ticket->image = json_encode($images);
        }

        // video updates
        $videos = json_decode($ticket->video, true) ?? [];
        if ($request->hasFile('video')) {
            // Delete old videos
            foreach ($videos as $oldVideo) {
                $oldVideoPath = str_replace(asset('storage/'), '', $oldVideo);
                if (Storage::disk('public')->exists($oldVideoPath)) {
                    Storage::disk('public')->delete($oldVideoPath);
                }
            }

            // Store new videos
            $videos = [];
            foreach ($request->file('video') as $file) {
                $path = $file->store('ticket-videos', 'public');
                $videos[] = $path;
            }
            $ticket->video = json_encode($videos);
        }

        $ticket->save();

        // Prepare URLs for response
        $imageUrls = array_map(fn($path) => asset('storage/' . $path), $images);
        $videoUrls = array_map(fn($path) => asset('storage/' . $path), $videos);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket updated successfully.',
            'data' => [
                'id' => $ticket->id,
                'product_name' => $ticket->product_name,
                'serial_no' => $ticket->serial_no,
                'problem' => $ticket->problem,
                'location' => $ticket->location,
                'comment' => $ticket->comment,
                'ticket_no' => $ticket->ticket_no,
                'image' => $imageUrls,
                'video' => $videoUrls,
            ],
        ], 200);
    }

    public function deleteTicket($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found.'], 404);
        }

        $images = json_decode($ticket->image, true);
        if ($images) {
            foreach ($images as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }

        $videos = json_decode($ticket->video, true);
        if ($videos) {
            foreach ($videos as $videoPath) {
                if (Storage::disk('public')->exists($videoPath)) {
                    Storage::disk('public')->delete($videoPath);
                }
            }
        }

        $ticket->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket deleted successfully.',
        ], 200);
    }

}
