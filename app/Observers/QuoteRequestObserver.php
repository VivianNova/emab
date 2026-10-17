<?php // app/Observers/QuoteRequestObserver.php

namespace App\Observers;

use App\Models\QuoteRequest;
use App\Services\QuoteNotifier;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class QuoteRequestObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private QuoteNotifier $notifier) {}

    public function created(QuoteRequest $quote): void
    {
        $this->notifier->newRequest($quote);
    }

    public function updated(QuoteRequest $quote): void
    {
        if ($quote->wasChanged('status')) {
            $this->notifier->statusChanged($quote, $quote->getOriginal('status'), $quote->status);
        }
    }
}