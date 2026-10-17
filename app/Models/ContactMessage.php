<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    // Options for the subject dropdown (also used for validation)
    public const SUBJECTS = [
        'General inquiry',
        'Existing quote or booking',
        'Complaint or feedback',
        'Other',
    ];

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    // #M-0012
public function getRefAttribute(): string
{
    return '#M-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
}

// "Faith Mwangi" becomes "FM"
public function getInitialsAttribute(): string
{
    $initials = str($this->name)
        ->explode(' ')
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    return mb_strtoupper($initials) ?: '?';
}
}