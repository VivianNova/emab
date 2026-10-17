<x-mail::message>
# New quote request

<x-mail::panel>
**Name:** {{ $name }}<br>
**Phone:** {{ $phone }}<br>
@if($hasEmail)**Email:** {{ $email }}<br>@endif
**Location:** {{ $location }}<br>
**Service:** {{ $service }}<br>
**Message:** {{ $message }}
</x-mail::panel>

<x-mail::button :url="$url">View request</x-mail::button>
</x-mail::message>