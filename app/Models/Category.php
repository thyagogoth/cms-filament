<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};

class Category extends Model implements HasMedia
{
    //    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'parent_id',
    ];
}
