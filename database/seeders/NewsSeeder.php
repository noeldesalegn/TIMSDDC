<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsItems = [
            [
                'title' => 'Tax Filing Deadline Reminder',
                'body' => 'Remember to file your annual taxes by the end of next month. Please ensure all documentation is complete.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'New Payment Channels Available',
                'body' => 'We have added new bank partners for easier payments. You can now pay using multiple banks including Commercial Bank of Ethiopia and Awash Bank.',
                'created_at' => now()->subWeek(),
                'updated_at' => now()->subWeek(),
            ],
            [
                'title' => 'Holiday Office Hours',
                'body' => 'Our office will have special hours during the holiday season. Please check our updated schedule before visiting.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Tax Code Updates 2024',
                'body' => 'New tax regulations have been implemented. Please review the updated tax code to ensure compliance.',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'title' => 'Online Filing Now Available',
                'body' => 'You can now file your taxes online through our new portal. Visit the website to get started.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
        ];

        foreach ($newsItems as $news) {
            News::create($news);
        }
    }
}


