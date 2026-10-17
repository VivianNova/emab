<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'description', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ActivityLog::record('login', 'Signed in from 127.0.0.1');
    public static function record(string $action, ?string $description = null, ?int $userId = null): void
    {
        static::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => $action,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
   
}