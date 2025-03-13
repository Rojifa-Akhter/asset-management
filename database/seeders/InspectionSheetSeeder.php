<?php

namespace Database\Seeders;

use App\Models\InspectionSheet;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InspectionSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportAgent = User::where('role', 'support_agent')->first();
        $technician = User::where('role', 'technician')->first();
        $ticket = Ticket::first();

        if (!$supportAgent || !$technician || !$ticket) {
            return;
        }

        // Create 10 dummy inspection sheets
        InspectionSheet::factory()->count(10)->create([
            'support_agent_id' => $supportAgent->id,
            'technician_id'    => $technician->id,
            'ticket_id'        => $ticket->id,
        ]);
    }
}
