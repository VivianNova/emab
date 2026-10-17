<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const STATUSES = ['new', 'confirmed', 'in_progress', 'completed', 'cancelled'];

// #B-0012
public function getRefAttribute(): string
{
    return '#B-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
}

// ['label' => 'Morning', 'hours' => '8am – 12pm'] or null
public function getSlotAttribute(): ?array
{
    return self::SLOTS[$this->preferred_time] ?? null;
}
}
