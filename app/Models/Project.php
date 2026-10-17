<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Project extends Model
{
    protected $fillable = ['service_id', 'title', 'description', 'location', 'completed_on'];

    protected $casts = ['completed_on' => 'date'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }

    // The first uploaded image, used as the card cover
    public function coverImage(): HasOne
    {
        return $this->hasOne(ProjectImage::class)->oldestOfMany();
    }
    protected function coverUrl(): Attribute
{
    return Attribute::get(fn () => $this->coverImage
        ? asset('storage/' . $this->coverImage->image_path)
        : asset('images/placeholder.jpg'));
}
}