<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'read_time',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(BlogTopic::class, 'topic_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'saved_posts', 'blog_post_id', 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
