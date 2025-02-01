<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdmin extends Controller
{
    public function dashboard(){
         $total_jobcard=JobCard::count();
         $total_user=User::count();
         $total_organization=User::where('role:organization')->count();
    }
}
