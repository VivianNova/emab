<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ServiceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'sort_order'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }



    public function projects(): HasManyThrough
{
        return $this->hasManyThrough(Project::class, Service::class);
}
}