<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get taxpayers
        $taxpayers = User::where('role', 'taxpayer')->get();

        foreach ($taxpayers as $taxpayer) {
            // Create some completed payments
            for ($i = 0; $i < rand(2, 5); $i++) {
                Payment::create([
                    'user_id' => $taxpayer->id,
                    'amount' => rand(1000, 15000),
                    'status' => 'completed',
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(1, 60)),
                ]);
            }

            // Create some pending payments
            for ($i = 0; $i < rand(0, 2); $i++) {
                Payment::create([
                    'user_id' => $taxpayer->id,
                    'amount' => rand(1000, 15000),
                    'status' => 'pending',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}


