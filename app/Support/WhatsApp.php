<?php

// app/Support/WhatsApp.php

namespace App\Support;

use App\Models\QuoteRequest;

class WhatsApp
{
    public static function clientLink(QuoteRequest $quote): ?string
    {
        $number = self::normalise((string) $quote->phone);

        if (! $number) {
            return null;
        }

        $company = setting('company_name', config('app.name'));
        $text = "Hello {$quote->name}, this is {$company} about your quote request {$quote->ref}. When is a good time to talk about the job?";

        return 'https://wa.me/'.$number.'?text='.rawurlencode($text);
    }

    public static function normalise(string $phone): ?string
    {
        $phone = trim($phone);
        $digits = preg_replace('/\D+/', '', $phone);

        return match (true) {
            $digits === '' => null,
            str_starts_with($phone, '+') => $digits,
            str_starts_with($digits, '00') => substr($digits, 2),
            str_starts_with($digits, '0') => config('emab.default_country_code').substr($digits, 1),
            strlen($digits) === 9 => config('emab.default_country_code').$digits,
            default => $digits,
        };
    }
}
