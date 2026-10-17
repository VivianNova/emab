<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Three-bedroom house rewiring',
                'service' => 'house-wiring-and-rewiring',
                'description' => 'Full rewiring with a new distribution board and earthing.',
                'completed_on' => '2026-09-12',
            ],
            [
                'title' => 'Warehouse lighting upgrade',
                'service' => 'led-retrofits',
                'description' => 'Old fittings replaced with efficient LED lighting.',
                'completed_on' => '2026-08-03',
            ],
            [
                'title' => 'Rooftop solar installation',
                'service' => 'solar-pv-design-and-installation',
                'description' => 'A rooftop solar system with battery backup for a small business.',
                'completed_on' => '2026-06-20',
            ],
            [
                'title' => 'CCTV and alarm installation',
                'service' => 'cctv',
                'description' => 'Cameras and an alarm system installed across a retail shop.',
                'completed_on' => '2026-04-10',
            ],
        ];

        foreach ($projects as $item) {
            Project::updateOrCreate(
                ['title' => $item['title']],
                [
                    'service_id' => Service::where('slug', $item['service'])->value('id'),
                    'description' => $item['description'],
                    'location' => 'Town Name',
                    'completed_on' => $item['completed_on'],
                ]
            );
        }

        $testimonials = [
            ['client_name' => 'Client A', 'message' => 'Professional work and a quick response. Highly recommended.'],
            ['client_name' => 'Client B', 'message' => 'They finished on time and left everything clean and safe.'],
            ['client_name' => 'Client C', 'message' => 'Fair pricing and clear explanations. We will call them again.'],
        ];

        foreach ($testimonials as $item) {
            Testimonial::updateOrCreate(
                ['client_name' => $item['client_name']],
                ['message' => $item['message'], 'rating' => 5, 'is_published' => true]
            );
        }
    }
}