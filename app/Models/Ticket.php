<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = ['id'];

    // protected $cast = [
    //     'iamge'=> 'array'
    // ];

    public function getImageAttribute($image)
    {
        $images = json_decode($image, true) ?? [];
        return array_map(fn($img) => asset('uploads/ticket_images/' . $img), $images);
    }


    // Accessor for Videos
    public function getVideoAttribute($video)
    {
        $videos = json_decode($video, true) ?? [];
        return array_map(fn($vid) => asset('uploads/video/' . $vid), $videos);
    }
}
