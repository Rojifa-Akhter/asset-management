<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    // public function showimage($path)
    // {
    //     $imagePath = storage_path('app/public/' . $path);
    //     if (file_exists($imagePath)) {
    //         // Generate the full URL for the stored image
    //         return asset('storage/' . $path);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
    //     }
    // }

    public function getImageAttribute($image)
    {
        return asset('uploads/profile_images/' . $image);
    }

}
