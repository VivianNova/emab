<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --max-time=50 --timeout=45 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground();

Schedule::command('queue:prune-failed --hours=168')->daily();