<?php

namespace App\Models;

use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'service_id', 'location', 'description', 'photo', 'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public const STATUSES = ['new', 'contacted', 'quoted', 'accepted', 'completed', 'cancelled'];

    // #Q-0042
    public function getRefAttribute(): string
    {
        return '#Q-'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        return WhatsApp::clientLink($this);
    }
}
