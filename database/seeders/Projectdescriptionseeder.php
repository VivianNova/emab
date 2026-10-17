<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fills in the description shown on each project card and on the project page (/projects/...).
 * A blank line in a description starts a new paragraph on the project page.
 *
 * How it picks a description for each project:
 *   1. If the project title contains a keyword in $byTitle, it uses that text.
 *   2. Otherwise it uses the text for the project's service category ($byCategory).
 *
 * By default it only fills projects whose description is empty or shorter than $shortLimit characters,
 * so descriptions you wrote by hand are left alone. Set $overwrite = true to replace every description.
 *
 * These are general drafts. Edit them in the admin so they say what was actually done on each job.
 *
 * Run:  php artisan db:seed --class=ProjectDescriptionSeeder
 */
class ProjectDescriptionSeeder extends Seeder
{
    private bool $overwrite = false;

    private int $shortLimit = 100;

    public function run(): void
    {
        // Matched against the project title (lowercase keyword => description)
        $byTitle = [
            'cctv' => "We installed CCTV cameras and an alarm system for a retail shop to help the owner protect the premises and stock. Cameras were positioned to cover the areas that matter most.\n\nThe system was set up and tested so it was ready for everyday use when we handed it over.",
        ];

        // Matched against the category name (lowercase keyword => description)
        $byCategory = [
            'resid'   => "Electrical work completed for a home, done safely and finished neatly. We worked to the owner's needs so every room had the points and lighting it required.\n\nThe installation was tested before handover so the home was ready to use.",
            'commerc' => "An electrical installation completed for a business premises, planned around how the business operates. The work was done safely and tested before handover.\n\nIt gives the business a reliable power supply for its daily operations.",
            'solar'   => "A solar installation completed for a client to help cut electricity costs and reduce reliance on the grid. The system was sized to the client's needs and installed securely.\n\nWe checked the installation before handover so the client could start benefiting straight away.",
            'light'   => "A lighting installation completed for a client, chosen to suit the space and to use energy efficiently. The fittings were installed neatly and tested.\n\nThe result is a brighter, safer space.",
            'secur'   => "A security and safety installation completed for a client to protect people and property. The equipment was installed securely and tested before handover.\n\nIt gives the client peace of mind and safer premises.",
            'inspect' => "An electrical inspection completed for a client to check the safety of the installation. Faults and hazards were identified and reported so they could be fixed.\n\nThe client received the findings and the paperwork they needed.",
            'maint'   => "Fault finding and repair work completed for a client. We traced the cause of the problem and fixed it properly.\n\nThe supply was restored and checked before we left.",
        ];

        $projects = Project::with('service.category')->orderBy('id')->get();

        foreach ($projects as $project) {
            $text = $this->pick($project, $byTitle, $byCategory);

            if ($text === null) {
                $this->command?->warn("No matching text, skipped: {$project->title}");
                continue;
            }

            $current = trim((string) $project->description);

            if (! $this->overwrite && mb_strlen($current) >= $this->shortLimit) {
                $this->command?->line("Kept your description: {$project->title}");
                continue;
            }

            // Query update, so it works even if 'description' is not in the model's $fillable
            Project::whereKey($project->getKey())->update(['description' => $text]);
            $this->command?->info("Updated description: {$project->title}");
        }
    }

    private function pick(Project $project, array $byTitle, array $byCategory): ?string
    {
        $title = Str::lower((string) $project->title);

        foreach ($byTitle as $keyword => $text) {
            if (str_contains($title, $keyword)) {
                return $text;
            }
        }

        $category = Str::lower((string) ($project->service?->category?->name ?? ''));

        foreach ($byCategory as $keyword => $text) {
            if ($category !== '' && str_contains($category, $keyword)) {
                return $text;
            }
        }

        return null;
    }
}