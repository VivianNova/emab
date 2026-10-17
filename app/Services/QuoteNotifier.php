<?php

// app/Services/QuoteNotifier.php

namespace App\Services;

use App\Models\QuoteRequest;
use App\Models\User;
use App\Notifications\NewQuoteRequestAlert;
use App\Notifications\QuoteRequestReceived;
use App\Notifications\QuoteStatusUpdated;
use Illuminate\Support\Facades\Notification;

class QuoteNotifier
{
    public const CLIENT_STATUSES = ['contacted', 'quoted', 'accepted', 'completed', 'cancelled'];

    public function newRequest(QuoteRequest $quote): void
    {
        try {
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewQuoteRequestAlert($quote));
            }

            if (filled($quote->email)) {
                Notification::route('mail', [$quote->email => $quote->name])
                    ->notify(new QuoteRequestReceived($quote));
            }
        } catch (\Throwable $e) {
            report($e); // a notification problem must never break saving the request
        }
    }

    public function statusChanged(QuoteRequest $quote, mixed $from, mixed $to): void
    {
        $from = $from instanceof \BackedEnum ? $from->value : (string) $from;
        $to = $to instanceof \BackedEnum ? $to->value : (string) $to;

        if ($from === $to || ! in_array($to, self::CLIENT_STATUSES, true) || blank($quote->email)) {
            return;
        }

        try {
            Notification::route('mail', [$quote->email => $quote->name])
                ->notify(new QuoteStatusUpdated($quote, $to));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
