<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function inspectionSheet()
    {
        return $this->belongsTo(InspectionSheet::class);
    }
    public function quatation()
    {
        return $this->belongsTo(Quatation::class);
    }
}
