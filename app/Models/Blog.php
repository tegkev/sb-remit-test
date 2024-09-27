<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['title','content', 'thumbnail_path', 'created_by_id'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function thumbnailUrl(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => url(Storage::url($attributes['thumbnail_path']))
        );
    }
}
