<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = ['id'];


    // public function ticketimage($path)
    // {
    //     $imagePath = storage_path('app/public/' . $path);
    //     if (file_exists($imagePath)) {
    //         return asset('storage/' . $path);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
    //     }
    // }
}
