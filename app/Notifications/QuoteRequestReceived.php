<?php

// app/Notifications/QuoteRequestReceived.php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuoteRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public QuoteRequest $quote)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We have received your quote request')
            ->markdown('emails.quotes.received', [
                'name' => preg_replace('/([\\\\`*_{}\[\]()<>#+\-.!|~])/', '\\\\$1', Str::limit(trim($this->quote->name), 60)),
                'ref' => $this->quote->ref,
            ]);
    }
}
