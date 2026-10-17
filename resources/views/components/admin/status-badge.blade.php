@props(['status'])

@php
    $styles = [
        'new'         => 'bg-brand-blue text-white',
        'contacted'   => 'bg-brand-tint text-brand-navy',
        'quoted'      => 'bg-brand-tint text-brand-navy',
        'confirmed'   => 'bg-brand-tint text-brand-navy',
        'in_progress' => 'bg-brand-tint text-brand-navy',
        'accepted'    => 'bg-green-100 text-green-800',
        'completed'   => 'bg-green-100 text-green-800',
        'cancelled'   => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->class(['inline-flex whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold', $styles[$status] ?? 'bg-slate-100 text-slate-600']) }}>
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>