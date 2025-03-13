<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $asset = Asset::first();

        if (!$user || !$asset) {
            return;
        }

        // Create 10 dummy tickets
        Ticket::factory()->count(10)->create([
            'user_id' => $user->id,
            'asset_id' => $asset->id,
        ]);

    }
}
