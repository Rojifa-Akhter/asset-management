<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    protected $table = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
