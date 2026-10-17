<?php

// app/Notifications/NewQuoteRequestAlert.php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewQuoteRequestAlert extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $q = $this->quote;

        return (new MailMessage)
            ->subject('New quote request '.$q->ref)
            ->markdown('emails.quotes.new-alert', [
                'url' => route('admin.quotes.show', $q),
                'name' => $this->plain($q->name, 80),
                'phone' => $this->plain($q->phone, 30),
                'email' => $this->plain($q->email, 80),
                'location' => $this->plain($q->location, 80),
                'service' => $this->plain($q->service?->name, 80),
                'message' => $this->plain($q->description, 400),
                'hasEmail' => filled($q->email),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'title' => 'New quote request',
            'body' => Str::limit($this->quote->name, 50).($this->quote->service ? ' · '.$this->quote->service->name : ''),
        ];
    }

    // Visitor text goes into Markdown mail, so escape Markdown characters.
    private function plain(?string $text, int $limit): string
    {
        $text = Str::limit(trim(preg_replace('/\s+/', ' ', (string) $text)), $limit);

        return preg_replace('/([\\\\`*_{}\[\]()<>#+\-.!|~])/', '\\\\$1', $text);
    }
}
