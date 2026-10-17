<x-mail::message>
# {{ $headline }}

Hello {{ $name }},

{{ $body }}

Reference: **{{ $ref }}**. Call us on **{{ setting('phone') }}**.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>