<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTopic extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'topic_id');
    }
}
