<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get taxpayers
        $taxpayers = User::where('role', 'taxpayer')->get();
        
        $subjects = [
            'Payment Processing Delay',
            'Incorrect Tax Calculation',
            'Missing Tax Certificate',
            'System Error During Filing',
            'Unable to Access Dashboard',
            'Refund Request Status',
            'Tax Assessment Issue',
        ];

        $messages = [
            'I have been waiting for my payment to be processed for over 3 weeks. Please investigate.',
            'The tax amount calculated seems incorrect. I need a review of my tax assessment.',
            'I have not received my tax certificate yet. Please expedite the process.',
            'I encountered an error while trying to file my taxes online. The system keeps crashing.',
            'I cannot access my taxpayer dashboard. Getting 403 error.',
            'I submitted a refund request 2 months ago and have not received any update.',
            'The tax assessment on my account is incorrect and needs to be reviewed.',
        ];

        foreach ($taxpayers as $index => $taxpayer) {
            Complaint::create([
                'user_id' => $taxpayer->id,
                'subject' => $subjects[$index % count($subjects)],
                'message' => $messages[$index % count($messages)],
                'status' => ['submitted', 'in_progress', 'resolved'][rand(0, 2)],
                'created_at' => now()->subDays(rand(1, 45)),
                'updated_at' => now()->subDays(rand(1, 45)),
            ]);
        }
    }
}


