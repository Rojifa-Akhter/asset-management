<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = ['id'];
    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

}
