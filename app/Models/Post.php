<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Str;
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'is_published',
        'is_featured',
        'user_id',
    ];

    public $casts = ['content' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function excerpt(): string
    {
        //        return Str::words(tiptap_converter()->asText($this->content), 40, '...');
        return Str::words(strip_tags($this->content), 200, '...');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function cover(): string
    {
        return $this->getFirstMediaUrl();
    }
}
