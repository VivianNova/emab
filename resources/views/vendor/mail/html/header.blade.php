@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logo = asset('images/logo.png'))
<img src="{{ $logo }}" alt="{{ $slot }}" height="48" style="height:48px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>