<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'published', 'description' => 'The post is visible to everyone.'],
            ['name' => 'draft', 'description' => 'The post is a work in progress.'],
            ['name' => 'scheduled', 'description' => 'The post will be published at a later date.'],
            ['name' => 'review', 'description' => 'The post is waiting for moderator approval.'],
            ['name' => 'archived', 'description' => 'The post is old and kept for records.'],
            ['name' => 'deleted', 'description' => 'The post is marked for removal.'],
        ];

        foreach ($statuses as $status) {
            \App\Models\PostStatus::create($status);
        }
    }
}
