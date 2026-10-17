<x-mail::message>
# Thank you, {{ $name }}

We have received your quote request (reference **{{ $ref }}**). Our team will review it and get back to you.

Need to add something or speak to us sooner? Call **{{ setting('phone') }}**.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>