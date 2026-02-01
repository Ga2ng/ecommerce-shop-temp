<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 published news
        News::factory(15)->published()->create();
        
        // Create 5 unpublished news
        News::factory(5)->unpublished()->create();
    }
}
