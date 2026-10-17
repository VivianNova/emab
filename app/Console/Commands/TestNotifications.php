<?php

// app/Console/Commands/TestNotifications.php

namespace App\Console\Commands;

use App\Models\QuoteRequest;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestNotifications extends Command
{
    protected $signature = 'emab:test-notifications
        {--smtp= : Send one plain email now to this address (checks MAIL_* values)}
        {--client-email= : Email for the test request (omit to test the WhatsApp path)}
        {--cleanup : Delete test requests and their bell notifications}';

    protected $description = 'Test mail credentials, the queue and the quote notifications';

    public function handle(): int
    {
        if ($to = $this->option('smtp')) {
            try {
                Mail::raw('SMTP test from '.config('app.name').' at '.now()->toDateTimeString(),
                    fn ($m) => $m->to($to)->subject('SMTP test'));
                $this->info("Sent to {$to}. Check the inbox and spam folder.");

                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error($e->getMessage());

                return self::FAILURE;
            }
        }

        if ($this->option('cleanup')) {
            $ids = QuoteRequest::where('name', 'TEST - ignore')->pluck('id');
            DatabaseNotification::whereIn('data->quote_id', $ids)->delete();
            QuoteRequest::whereIn('id', $ids)->delete();
            $this->info("Removed {$ids->count()} test request(s).");

            return self::SUCCESS;
        }

        // Adjust these fields to your required columns.
        $quote = QuoteRequest::forceCreate([
            'name' => 'TEST - ignore',
            'phone' => '0000000000',
            'email' => $this->option('client-email'),
            'location' => 'Test',
            'description' => 'Created by emab:test-notifications.',
            'status' => 'new',
        ]);

        $this->info("Created test request #{$quote->id}.");
        $this->line('Pending jobs: '.DB::table('jobs')->count().' | Failed jobs: '.DB::table('failed_jobs')->count());
        $this->line('Run `php artisan queue:work --stop-when-empty` (or wait for cron), then check the inboxes and the bell.');
        $this->line('Change its status in the admin to test the status email. Clean up with --cleanup.');

        return self::SUCCESS;
    }
}
