<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionSheet extends Model
{
    protected $guarded = ['id'];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

}
