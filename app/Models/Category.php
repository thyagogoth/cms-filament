<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
