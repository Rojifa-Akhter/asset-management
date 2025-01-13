<?php

namespace App\Http\Controllers\SupportAgent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketTecController extends Controller
{
    //create ticket
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
            'video' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        $images = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                if ($image->isValid()) {
                    $extension = $image->getClientOriginalExtension();
                    $ticket_image = time() . uniqid() . '.' . $extension;
                    $image->move(public_path('uploads/ticket_images'), $ticket_image);
                    $images[] = $ticket_image;
                }
            }
        }

        $videos = [];
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $video) {
                if ($video->isValid()) {
                    $extension = $video->getClientOriginalExtension();
                    $new_video = time() . uniqid() . '.' . $extension;
                    $video->move(public_path('uploads/ticket_videos'), $new_video);
                    $videos[] = $new_video;
                }
            }
        }
        // return json_encode($images);

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

        return response()->json([
            'status' => true,
            'message' => 'Ticket created successfully',
            'data' => [
                'id' => $ticket->id,
                'product_name' => $ticket->product_name,
                'serial_no' => $ticket->serial_no,
                'problem' => $ticket->problem,
                'location' => $ticket->location,
                'comment' => $ticket->comment,
                'ticket_no' => $ticket->ticket_no,
                'image' => $ticket->image,
                'video' => $ticket->video,
            ],
        ], 201);
    }
//update ticket
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
            'ticket_no' => 'nullable|string',
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

        if ($request->hasFile('image')) {
            // return $existingImages;
            $existingImages = is_array($ticket->image)
            ? $ticket->image
            : json_decode($ticket->image, true) ?? [];

            foreach ($existingImages as $oldImage) {
                $parsedUrl = parse_url($oldImage);
                $filePath = ltrim($parsedUrl['path'], '/');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            // Handle new images
            $images = [];
            foreach ($request->file('image') as $image) {
                if ($image->isValid()) {
                    $extension = $image->getClientOriginalExtension();
                    $ticketImage = time() . uniqid() . '.' . $extension;
                    $image->move(public_path('uploads/ticket_images'), $ticketImage);
                    $images[] = $ticketImage;
                }
            }
            $ticket->image = json_encode($images);
        }

        // Handle video update
        if ($request->hasFile('video')) {

            $existingVideos = is_array($ticket->video)
            ? $ticket->video
            : json_decode($ticket->video, true) ?? [];
            foreach ($existingVideos as $oldVideo) {
                $videoUrl = parse_url($oldVideo);
                $filePath = ltrim($videoUrl['path'], '/');
                // return $filePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Store new videos
            $videos = [];
            foreach ($request->file('video') as $video) {
                if ($video->isValid()) {
                    $extension = $video->getClientOriginalExtension();
                    $newVideo = time() . uniqid() . '.' . $extension;
                    $video->move(public_path('uploads/ticket_videos'), $newVideo);
                    $videos[] = $newVideo;
                }
            }
            $ticket->video = json_encode($videos);
        }

        $ticket->save();

        return response()->json([
            'status' => true,
            'message' => 'Ticket updated successfully.',
            'data' => [
                'id' => $ticket->id,
                'product_name' => $ticket->product_name,
                'serial_no' => $ticket->serial_no,
                'problem' => $ticket->problem,
                'location' => $ticket->location,
                'comment' => $ticket->comment,
                'ticket_no' => $ticket->ticket_no,
                'image' => $ticket->image,
                'video' => $ticket->video,
            ],
        ], 200);
    }

    //all ticket list
    public function ticketList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tickets = Ticket::paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $tickets,
        ]);
    }
//get ticket details
    public function ticketDetails(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['status'=> false , 'message'=>'Ticket Not Found'],401);
        }
        return response()->json(['status'=>true, 'message'=>$ticket]);
    }
//delete ticket
    public function deleteTicket($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
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
            'status' => true,
            'message' => 'Ticket deleted successfully, along with associated images and videos.',
        ], 200);
    }

}


