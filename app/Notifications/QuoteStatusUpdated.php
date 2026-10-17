<?php

// app/Notifications/QuoteStatusUpdated.php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuoteStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public QuoteRequest $quote, public string $status)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $body] = match ($this->status) {
            'contacted' => ['We are following up on your request', 'Our team has looked at your request and is getting in touch with you. If you have not heard from us yet, please call us.'],
            'quoted' => ['Your quotation is ready', 'We have prepared a quotation for your request. If you have any questions about it, please call us.'],
            'accepted' => ['Thank you for accepting our quotation', 'We are glad to be working with you. We will contact you shortly to agree the next steps.'],
            'completed' => ['Your job is complete', 'Thank you for choosing us. If anything is not right, please call us and we will put it right.'],
            'cancelled' => ['Your request has been cancelled', 'Your quote request has been cancelled. If this is a mistake or your needs change, please call us or send a new request.'],
            default => ['An update on your request', 'The status of your request has changed. Please call us if you have any questions.'],
        };

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.quotes.status', [
                'headline' => $subject,
                'body' => $body,
                'name' => preg_replace('/([\\\\`*_{}\[\]()<>#+\-.!|~])/', '\\\\$1', Str::limit(trim($this->quote->name), 60)),
                'ref' => $this->quote->ref,
            ]);
    }
}
